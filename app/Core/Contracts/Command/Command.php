<?php

namespace App\Core\Contracts\Command;

use App\Core\Contracts\Feature\FeatureRepository;
use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Contracts\Site\SiteRepository;
use App\Core\Contracts\Site\SiteResolver;
use App\Core\Feature\Feature;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\IO\Proxy;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Site\Site;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \LaravelZero\Framework\Commands\Command
{

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

}
