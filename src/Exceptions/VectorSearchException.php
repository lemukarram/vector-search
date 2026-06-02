<?php

namespace LeMukarram\VectorSearch\Exceptions;

use Exception;

class VectorSearchException extends Exception
{
    public static function apiError(string $driver, string $message, int $code = 0): self
    {
        return new self("[$driver] API Error: $message", $code);
    }

    public static function configError(string $message): self
    {
        return new self("Configuration Error: $message");
    }

    public static function driverNotFound(string $driver): self
    {
        return new self("Driver [$driver] not found.");
    }
}
