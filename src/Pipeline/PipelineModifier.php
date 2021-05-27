<?php

namespace OriginEngine\Pipeline;

class PipelineModifier
{

    private array $callbacks = [];

    /**
     * Modify the pipeline class before use
     *
     * @param string $pipelineName
     * @param \Closure $callback
     */
    public function extend(string $pipelineName, \Closure $callback)
    {
        if(!array_key_exists($pipelineName, $this->callbacks)) {
            $this->callbacks[$pipelineName] = [];
        }
        $this->callbacks[$pipelineName][] = $callback;
    }

    public function modify(Pipeline &$pipeline)
    {
        if(array_key_exists($pipeline->getAlias(), $this->callbacks)) {
            foreach($this->callbacks[$pipeline->getAlias()] as $callback) {
                $callback($pipeline);
            }
        }
    }

}
