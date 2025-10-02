<?php

namespace App\Exceptions;

use RuntimeException;

class MarksLimitExceededException extends RuntimeException
{
    protected int $currentMarks;

    public function __construct(int $currentMarks, string $message)
    {
        parent::__construct($message);
        $this->currentMarks = $currentMarks;
    }

    public function getCurrentMarks(): int
    {
        return $this->currentMarks;
    }
}
