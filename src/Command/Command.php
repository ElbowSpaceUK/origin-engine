<?php

namespace OriginEngine\Command;

use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\IO\Proxy;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Runners\DebugPipelineRunner;
use OriginEngine\Pipeline\Runners\NormalPipelineRunner;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\Runners\VerbosePipelineRunner;
use OriginEngine\Pipeline\Runners\VeryVerbosePipelineRunner;
use OriginEngine\Site\Site;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \LaravelZero\Framework\Commands\Command implements SignalableCommandInterface
{

    protected function configure()
    {
        parent::configure();
        if(method_exists($this, 'configureRunPipelines')) {
            $this->configureRunPipelines();
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        if(! $output instanceof OutputStyle) {
            throw new \Exception(sprintf('The output interface must be of type %s, %s given.', OutputStyle::class, get_class($output)));
        }
        $this->app->instance(Proxy::class, new Proxy($output));
    }

    protected function getOrAskForOption(string $option, \Closure $ask, \Closure $validator, bool $useOption = true)
    {
        if($useOption && $this->option($option)) {
            $value = $this->option($option);
        } else {
            $value = $ask();
        }

        if(!$validator($value)) {
            IO::error(sprintf('[%s] is not a valid %s', $value, $option));
            return $this->getOrAskForOption($option, $ask, $validator, false);
        }

        return $value;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputs = [
            OutputInterface::VERBOSITY_NORMAL => fn($service) => new NormalPipelineRunner($service),
            OutputInterface::VERBOSITY_VERBOSE => fn($service) => new VerbosePipelineRunner($service),
            OutputInterface::VERBOSITY_VERY_VERBOSE => fn($service) => new VeryVerbosePipelineRunner($service),
            OutputInterface::VERBOSITY_DEBUG => fn($service) => new DebugPipelineRunner($service)
        ];
        $verbosity = $output->getVerbosity();
        if(array_key_exists($verbosity, $outputs)) {
            $this->app->extend(PipelineRunner::class, $outputs[$verbosity]);
        }

        return parent::execute($input, $output);
    }

    public function getSubscribedSignals(): array
    {
        return [SIGINT, SIGTERM];
    }

    public function handleSignal(int $signal): void
    {
        event(new SignalReceived($signal));
    }

}
