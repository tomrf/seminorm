<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Clause;

use InvalidArgumentException;

trait SetTrait
{
    public function set(string $column, float|int|string $value): static
    {
        $key = trim($column);

        $this->set[$key] = [
            'column' => $key,
            'value' => $value,
            'raw' => false,
        ];

        return $this;
    }

    public function setRaw(string $column, string $expression): static
    {
        $key = trim($column);

        $this->set[$key] = [
            'column' => $key,
            'value' => $expression,
            'raw' => true,
        ];

        return $this;
    }

    /**
     * @param array<string, null|float|int|string> $values
     *
     * @throws InvalidArgumentException
     */
    public function setFromArray(array $values): static
    {
        foreach ($values as $column => $value) {
            if (!\is_string($column)) {
                throw new InvalidArgumentException(
                    'Column name (array key) must be string, got '.\gettype($column)
                );
            }

            if (null === $value) {
                $this->setRaw($column, 'NULL');

                continue;
            }

            $this->set($column, $value);
        }

        return $this;
    }

}
