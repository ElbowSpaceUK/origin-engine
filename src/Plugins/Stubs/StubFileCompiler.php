<?php

namespace OriginEngine\Plugins\Stubs;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Plugins\Stubs\Entities\CompiledStub;
use Faker\Provider\File;

class StubFileCompiler
{

    public function compile(Entities\StubFile $stubFile, array $data = []): CompiledStub
    {
        $compiledStubContent = $this->getCompiledString($stubFile, $data);

        return (new CompiledStub())
            ->setStubFile($stubFile)
            ->setContent($compiledStubContent);
    }

    private function getCompiledString(Entities\StubFile $stubFile, array $data): string
    {
        // TODO refactor as pipeline?
        if(!Filesystem::create()->exists($stubFile->getStubPath())) {
            throw new \Exception(sprintf(
                'The stub file [%s] does not exist', $stubFile->getStubPath()
            ));
        }
        $content = file_get_contents($stubFile->getStubPath());

        // Convert <?php and ? > to <TEMPORARY_PHP_START_TAG?, ?TEMPORARY_PHP_END_TAG>
        $content = str_replace(
            ['<?php', '?>'],
            ['<TEMPORARY_PHP_START_TAG?', '?TEMPORARY_PHP_END_TAG>'],
            $content
        );

        // Convert $[ ] to <?php ? >
        $content = preg_replace('/\[\[(.*?)]]/', '<?php $1 ?>', $content);

        // Convert {{  }} to <?php echo xx ? >
        $content = preg_replace('/{{(.*?)}}/', 'echo $1;', $content);

        // Make the variables available to the scope and eval the stub.
        try {
            $output = $this->doGeneration($data, $content);
        } catch (\ParseError $e) {
            IO::error('Content could not be parsed');
            IO::writeln($content);
            throw $e;
        }

        // Convert temp php tags back to normal.
        $output = str_replace(
            ['<TEMPORARY_PHP_START_TAG?', '?TEMPORARY_PHP_END_TAG>'],
            ['<?php', '?>'],
            $output
        );

        return $output;
    }

    private function doGeneration(array $data, string $code)
    {
        $tmpFile = tmpfile();
        $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
        file_put_contents($tmpFilePath, $code);

        foreach($data as $key => $value) {
            ${$key} = $value;
        }

        ob_start(); // begin collecting output
        include $tmpFilePath;
        return ob_get_clean(); // retrieve output from myfile.php, stop buffering
    }
}
