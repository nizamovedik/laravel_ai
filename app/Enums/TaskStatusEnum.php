<?php

namespace App\Enums;

enum TaskStatusEnum: int
{
    case NEW = 1;
    case IN_PROGRESS = 2;
    case REVIEW = 3;
    case DONE = 4;
    case CLOSED = 5;
    case ON_HOLD = 6;

    /**
     * Возвращает человекочитаемое название статуса
     */
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

    /**
     * Возвращает slug для URL или системного использования
     */
    public function slug(): string
    {
        return match ($this) {
            self::NEW => 'new',
            self::IN_PROGRESS => 'in_progress',
            self::REVIEW => 'review',
            self::DONE => 'done',
            self::CLOSED => 'closed',
            self::ON_HOLD => 'on_hold',
        };
    }

    /**
     * Проверяет, разрешён ли переход из текущего статуса в целевой
     */
    public function canTransitionTo(TaskStatusEnum $targetStatus): bool
    {
        // Определяем разрешённые переходы для каждого статуса
        return match ($this) {
            // Из "Новой" можно перейти в "В работу" или "Отложена"
            self::NEW => in_array($targetStatus, [
                self::IN_PROGRESS,
                self::ON_HOLD,
            ]),

            // Из "В работе" можно перейти в "На ревью" или "Отложена"
            self::IN_PROGRESS => in_array($targetStatus, [
                self::REVIEW,
                self::ON_HOLD,
            ]),

            // Из "На ревью" можно перейти в "В работу" (если есть замечания) или "Готово"
            self::REVIEW => in_array($targetStatus, [
                self::IN_PROGRESS,
                self::DONE,
            ]),

            // Из "Готово" можно перейти в "Закрыта" или вернуть в "На ревью" (если ошибка)
            self::DONE => in_array($targetStatus, [
                self::CLOSED,
                self::REVIEW,
            ]),

            // "Закрыта" — финальный статус. Никуда нельзя перейти.
            self::CLOSED => false,

            // Из "Отложена" можно вернуть в "Новую" или "В работу"
            self::ON_HOLD => in_array($targetStatus, [
                self::NEW,
                self::IN_PROGRESS,
            ]),
        };
    }

    /**
     * Проверяет, является ли статус финальным (завершающим)
     */
    public function isFinal(): bool
    {
        return match ($this) {
            self::CLOSED => true,
            default => false,
        };
    }

    /**
     * Проверяет, является ли статус активным (в процессе работы)
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::IN_PROGRESS, self::REVIEW => true,
            default => false,
        };
    }

    /**
     * Проверяет, можно ли начать работу над задачей
     */
    public function isStartable(): bool
    {
        return $this === self::NEW || $this === self::ON_HOLD;
    }

    /**
     * Проверяет, можно ли завершить задачу (перевести в "Готово" или "Закрыта")
     */
    public function isCompletable(): bool
    {
        return $this === self::REVIEW || $this === self::DONE;
    }
}
