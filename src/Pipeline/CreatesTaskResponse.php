<?php

namespace OriginEngine\Pipeline;

trait CreatesTaskResponse
{

    private array $messages = [
        'debug' => [],
        'info' => [],
        'warning' => [],
        'error' => [],
        'success' => []
    ];

    private array $data = [];

    private function writeMessage(string $level, string $message)
    {
        $this->messages[$level][] = $message;
    }

    protected function writeDebug(string $message): void
    {
        $this->writeMessage('debug', $message);
    }

    protected function writeInfo(string $message): void
    {
        $this->writeMessage('info', $message);
    }

    protected function writeWarning(string $message): void
    {
        $this->writeMessage('warning', $message);
    }

    protected function writeError(string $message): void
    {
        $this->writeMessage('error', $message);
    }

    protected function writeSuccess(string $message): void
    {
        $this->writeMessage('success', $message);
    }

    protected function export(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function succeeded(array $data = []): TaskResponse
    {
        foreach($data as $key => $value) {
            $this->export($key, $value);
        }
        return $this->createResponse(true);
    }

    protected function failed(array $data = []): TaskResponse
    {
        foreach($data as $key => $value) {
            $this->export($key, $value);
        }
        return $this->createResponse(false);
    }

    private function createResponse(bool $status): TaskResponse
    {
        $taskResponse = new TaskResponse();

        $taskResponse->setData($this->data);
        $taskResponse->setMessages($this->messages);
        $taskResponse->setSuccess($status);

        return $taskResponse;
    }

}
