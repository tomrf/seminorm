<?php

namespace Tomrf\Seminorm\QueryBuilder\Trait\Clause;

trait OnDuplicateKeyTrait {
    public function onDuplicateKey(string $expression): static
    {
        $this->onDuplicateKey = trim($expression);

        return $this;
    }
}
