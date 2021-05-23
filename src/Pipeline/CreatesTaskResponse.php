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

    public function writeDebug(string $message): void
    {
        $this->writeMessage('debug', $message);
    }

    public function writeInfo(string $message): void
    {
        $this->writeMessage('info', $message);
    }

    public function writeWarning(string $message): void
    {
        $this->writeMessage('warning', $message);
    }

    public function writeError(string $message): void
    {
        $this->writeMessage('error', $message);
    }

    public function writeSuccess(string $message): void
    {
        $this->writeMessage('success', $message);
    }

    public function export(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    public function succeeded(array $data): TaskResponse
    {
        foreach($this->data as $key => $value) {
            $this->export($key, $value);
        }
        return $this->createResponse(true);
    }

    public function failed(array $data): TaskResponse
    {
        foreach($this->data as $key => $value) {
            $this->export($key, $value);
        }
        return $this->createResponse(false);
    }

    private function createResponse(bool $status): TaskResponse
    {
        $taskResponse = new TaskResponse();

        $taskResponse->setData([]);
        $taskResponse->setMessages($this->messages);
        $taskResponse->setSuccess($status);

        return $taskResponse;
    }

}
