<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\PostUpdate as PostUpdatePipeline;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Setup\SetupManager;

class PostUpdate extends Command
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'post-update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make any final changes after an installation or update';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->runPipeline(new PostUpdatePipeline(), Directory::fromFullPath(sys_get_temp_dir()));
    }
}
