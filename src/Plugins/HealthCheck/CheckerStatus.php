<?php

namespace OriginEngine\Plugins\HealthCheck;

class CheckerStatus
{

    private string $message;
    private bool $status;

    public function __construct(bool $status, string $message)
    {
        $this->message = $message;
        $this->status = $status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public static function failedDueTo(string $message = 'the check failing'): CheckerStatus
    {
        return new static(false, $message);
    }

    public static function succeededDueTo(string $message = 'the check passing'): CheckerStatus
    {
        return new static(true, $message);
    }

}
