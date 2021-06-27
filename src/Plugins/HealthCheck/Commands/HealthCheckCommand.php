<?php

namespace OriginEngine\Plugins\HealthCheck\Commands;

use Illuminate\Container\RewindableGenerator;
use OriginEngine\Commands\Pipelines\HealthCheckPipeline;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Command\Command;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Site\Site;
use Symfony\Component\Console\Output\OutputInterface;

class HealthCheckCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'healthcheck {--quick : Only run quick checks}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check your local environments and syncronise origin.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        $failedChecks = [];
        foreach($this->getCheckers() as $checker) {
            if(!$checker->isQuickCheck() && $this->option('quick')) {
                continue;
            }
            IO::task('Checking ' . $checker->checking(), function() use ($checker, $siteRepository, &$failedChecks) {
                $checkResult = true;
                foreach($siteRepository->all() as $site) {
                    $result = $checker->check($site);
                    if($result->getStatus() === false) {
                        if(!array_key_exists($checker->checking(), $failedChecks)) {
                            $failedChecks[$checker->checking()] = [];
                        }
                        $failedChecks[$checker->checking()][] = [
                            'message' => $result->getMessage(),
                            'site' => $site
                        ];
                    }
                    if($checkResult === true && $result->getStatus() === false) {
                        $checkResult = false;
                    }
                    if($this->verbosity > OutputInterface::VERBOSITY_VERBOSE && $result->getStatus() === true) {
                        IO::writeln(sprintf(
                            '- Test [%s] passed on site [%s], due to %s',
                            $checker->checking(),
                            $site->getName(),
                            $result->getMessage()
                        ));
                    }
                }
                return $checkResult;
            }, 'Analysing...');
        }

        if(count($failedChecks) === 0) {
            IO::success('Healthcheck complete, no issues found.');
            return 0;
        }

        $errors = [];
        foreach($failedChecks as $checkerName => $failedCheck) {
            foreach($failedCheck as $information) {
                $errors[] = sprintf(
                    '- Test [%s] failed on site [%s], due to %s',
                    $checkerName,
                    $information['site']->getName(),
                    $information['message']
                );
            }
        }
        IO::errors(array_merge([
            'Issues found during healthcheck. Please run healthcheck:fix to fix these problems.'
        ], $errors))
        ;
        return 1;

    }

    /**
     * Get all checkers
     *
     * @return array|Checker[]
     */
    public function getCheckers(): RewindableGenerator
    {
        return $this->app->tagged('healthcheck');
    }


}
