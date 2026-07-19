<?php

namespace App\DTO;

class TaskLabelData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $color,
        public readonly ?string $description,
    ) {}
}
