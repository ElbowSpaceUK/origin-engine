<?php

namespace OriginEngine\Contracts\Command;

use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\IO\Proxy;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\DebugPipelineRunner;
use OriginEngine\Pipeline\NormalPipelineRunner;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\VerbosePipelineRunner;
use OriginEngine\Pipeline\VeryVerbosePipelineRunner;
use OriginEngine\Site\Site;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \LaravelZero\Framework\Commands\Command
{

    protected bool $usePipelines = false;

    protected function configure()
    {
        parent::configure();
        if($this->usePipelines) {
            $this->addOption('config', 'C', InputOption::VALUE_IS_ARRAY|InputOption::VALUE_OPTIONAL, 'Data to pass to the installation pipeline. Separate the variable and value with an equals.', []);
        }
    }



    public function getPipelineConfig(): PipelineConfig
    {
        if($this->usePipelines) {
            return new PipelineConfig(collect($this->option('config'))->mapWithKeys(function($data) {
                $parts = explode('=', $data);
                if(count($parts) !== 2) {
                    throw new \Exception(sprintf('Data [%s] could not be parsed, please ensure you include both the variable name and value separated with an =.', $data));
                }
                return [$parts[0] => $parts[1]];
            })->toArray());
        }
        return new PipelineConfig([]);
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

}
