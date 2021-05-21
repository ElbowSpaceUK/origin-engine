<?php


namespace App\Core\Helpers\Terminal;


use App\Core\Contracts\Helpers\Terminal\Executor as ExecutorContract;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class ShellExecutor implements ExecutorContract
{

    private ?WorkingDirectory $workingDirectory;

    public function __construct(WorkingDirectory $workingDirectory = null)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function cd(WorkingDirectory $workingDirectory): ExecutorContract
    {
        $this->workingDirectory = $workingDirectory;

        return $this;
    }

    protected function formatCommand(string $command): string
    {
        if(!$this->workingDirectory) {
            throw new \Exception(sprintf('Cannot call command without working directory: [%s]', $command));
        }

        return sprintf(
            'cd %s; %s 2>&1',
            $this->workingDirectory->path(),
            $command
        );
    }

    public function execute(string $command): ?string
    {
        exec(
            $this->formatCommand($command),
            $output,
            $resultCode
        );

        $output = implode(PHP_EOL, $output);

        if($resultCode > 0) {
            throw new \Exception(
                'Shell command failed: ' . PHP_EOL . PHP_EOL . $output,
                $resultCode
            );
        }

        return $output;
    }

}
