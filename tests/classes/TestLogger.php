<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Test;

use Psr\Log\AbstractLogger;
use Stringable;

class TestLogger extends AbstractLogger
{
    public function log($level, string|Stringable $message, array $context = []): void
    {
        echo sprintf('[TestLogger] level=%s message="%s"', $level, $message).PHP_EOL;
    }
}
