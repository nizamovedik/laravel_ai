<?php

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use Illuminate\Support\Facades\Log;

class SendTaskNotification
{
    public function handle(TaskStatusChanged $event): void
    {
        // TODO: позже заменим на реальную отправку (email, телеграм)
        Log::info("Уведомление: Задача '{$event->task->title}' сменила статус с '{$event->oldStatus->label()}' на '{$event->newStatus->label()}'");
    }
}
