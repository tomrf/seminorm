<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Statement;

trait DeleteTrait
{
    public function deleteFrom(string $table): static
    {
        $this->setTable($table);
        $this->setStatement('DELETE FROM');

        return $this;
    }
}
