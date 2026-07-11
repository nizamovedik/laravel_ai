<?php

namespace App\DTO;

class CommentData
{
    public function __construct(
        public readonly int $userId,
        public readonly string $body,
        public readonly string $commentableType,
        public readonly int $commentableId,
    ) {}
}
