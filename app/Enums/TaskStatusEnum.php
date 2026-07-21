<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case REVIEW = 'review';
    case DONE = 'done';
    case CLOSED = 'closed';
    case ON_HOLD = 'on_hold';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Новая',
            self::IN_PROGRESS => 'В работе',
            self::REVIEW => 'На ревью',
            self::DONE => 'Готово',
            self::CLOSED => 'Закрыта',
            self::ON_HOLD => 'Отложена',
        };
    }

    public function canTransitionTo(TaskStatusEnum $targetStatus): bool
    {
        return match ($this) {
            self::NEW => in_array($targetStatus, [self::IN_PROGRESS, self::ON_HOLD]),
            self::IN_PROGRESS => in_array($targetStatus, [self::REVIEW, self::ON_HOLD]),
            self::REVIEW => in_array($targetStatus, [self::IN_PROGRESS, self::DONE]),
            self::DONE => in_array($targetStatus, [self::CLOSED, self::REVIEW]),
            self::CLOSED => false,
            self::ON_HOLD => in_array($targetStatus, [self::NEW, self::IN_PROGRESS]),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => '#3b82f6',
            self::IN_PROGRESS => '#f59e0b',
            self::REVIEW => '#8b5cf6',
            self::DONE => '#10b981',
            self::CLOSED => '#6b7280',
            self::ON_HOLD => '#ef4444',
        };
    }
}
