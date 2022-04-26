<?php

namespace App\Core\Database;

use PDO;

class Param
{
    public function __construct(
        protected mixed $value,
        protected int $type = PDO::PARAM_STR,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return mixed Valor del parámetro
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return int Tipo del parámetro
     */
    public function getType(): int
    {
        return $this->type;
    }
}
