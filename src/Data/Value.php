<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Data;

use Stringable;

class Value implements Stringable
{
    protected string $type;

    public function __construct(
        protected string|int|float|bool|null $data
    ) {
        $this->type = \gettype($data);
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public function asString(): string
    {
        return (string) $this->data;
    }

    public function asInt(): int
    {
        return (int) $this->data;
    }

    public function asFloat(): float
    {
        return (float) $this->data;
    }

    public function asBool(): bool
    {
        return (bool) ($this->data);
    }

    public function isNumeric(): bool
    {
        return is_numeric($this->data);
    }

    public function isInt(): bool
    {
        return \is_int($this->data);
    }

    public function isString(): bool
    {
        return \is_string($this->data);
    }

    public function isBool(): bool
    {
        return \is_bool($this->data);
    }

    public function isNull(): bool
    {
        return null === $this->data;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
