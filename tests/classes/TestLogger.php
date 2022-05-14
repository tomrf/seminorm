<?php

declare(strict_types=1);

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    public function log($level, string|Stringable $message, array $context = []): void
    {
        echo sprintf('[TestLogger] level=%s message="%s"', $level, $message).PHP_EOL;
    }
}
