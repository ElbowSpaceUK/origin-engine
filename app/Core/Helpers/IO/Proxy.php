<?php

namespace App\Core\Helpers\IO;

use Illuminate\Console\OutputStyle;

class Proxy
{

    /**
     * @var OutputStyle
     */
    private OutputStyle $output;

    public function __construct(OutputStyle $output)
    {
        $this->output = $output;
    }

    public function error(string $line)
    {
        $this->errors([$line]);
    }

    public function errors(array $line)
    {
        $this->output->error($line);
    }

    public function info(string $line)
    {
        $this->infos([$line]);
    }

    public function infos(array $line)
    {
        $this->output->info($line);
    }

    public function warning(string $line)
    {
        $this->warnings([$line]);
    }

    public function warnings(array $lines)
    {
        $this->output->warning($lines);
    }

    public function success(string $line)
    {
        $this->successes([$line]);
    }

    public function successes(array $lines)
    {
        $this->output->success($lines);
    }

    public function writeln(string $line)
    {
        $this->output->writeln($line);
    }

    public function writelns(array $lines)
    {
        $this->output->writeln($lines);
    }

    public function confirm(string $message, bool $default = false)
    {
        return $this->output->confirm($message, $default);
    }

    public function task(string $title, \Closure $task = null, $loadingText = 'loading...')
    {
        $this->output->write("$title: <comment>{$loadingText}</comment>");

        if ($task === null) {
            $result = true;
        } else {
            try {
                $result = $task() === false ? false : true;
            } catch (\Exception $taskException) {
                $result = false;
            }
        }

        if ($this->output->isDecorated()) { // Determines if we can use escape sequences
            // Move the cursor to the beginning of the line
            $this->output->write("\x0D");

            // Erase the line
            $this->output->write("\x1B[2K");
        } else {
            $this->output->writeln(''); // Make sure we first close the previous line
        }

        $this->output->writeln(
            "$title: ".($result ? '<info>✔</info>' : '<error>failed</error>')
        );

        if (isset($taskException)) {
            throw $taskException;
        }

        return $result;
    }

    public function choice(string $question, array $choices = [], $default = null)
    {
        return $this->output->choice($question, $choices, $default);
    }

    public function ask(string $question, $default = null, \Closure $validator = null)
    {
        return $this->output->ask($question, $default, $validator);
    }

    public function progressStart(int $count)
    {
        $this->output->progressStart($count);
    }

    public function progressStep(int $step = 1)
    {
        $this->output->progressAdvance($step);
    }

    public function progressFinish()
    {
        $this->output->progressFinish();
    }

}
