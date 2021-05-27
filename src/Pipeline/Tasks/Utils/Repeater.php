<?php

namespace OriginEngine\Pipeline\Tasks\Utils;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class Repeater extends Task
{

    public function __construct(\Closure $callback, \Closure $undoCallback, ?array $items = null)
    {
        parent::__construct([
            'callback' => $callback,
            'undo-callback' => $undoCallback,
            'items' => $items
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        foreach($config->get('items', []) ?? [] as $item) {
            $config->get('callback')($item);
        }
        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        foreach(array_reverse($config->get('items', []) ?? []) as $item) {
            $config->get('undo-callback')($item);
        }
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Repeating over %u items', count($config->get('items') ?? []));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Undoing over %u items', count($config->get('items') ?? []));
    }
}