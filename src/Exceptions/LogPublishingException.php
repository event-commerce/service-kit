<?php

namespace EventSoft\ServiceKit\Exceptions;

class LogPublishingException extends ServiceKitException
{
    public static function connectionFailed(string $reason): self
    {
        return new self("Log publishing connection failed: {$reason}");
    }

    public static function publishFailed(string $reason): self
    {
        return new self("Log publishing failed: {$reason}");
    }
}

