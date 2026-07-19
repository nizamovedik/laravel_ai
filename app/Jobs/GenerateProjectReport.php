<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateProjectReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Project $project;

    public int $userId;

    public function __construct(Project $project, int $userId)
    {
        $this->project = $project;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $tasks = Task::with(['priority', 'assignee', 'creator'])
            ->where('project_id', $this->project->id)
            ->get();

        $filename = "report_project_{$this->project->id}_".now()->format('Y-m-d_H-i-s').'.csv';
        $path = 'reports/'.$filename;

        $csvContent = $this->generateCsv($tasks);
        Storage::put($path, $csvContent);

        Log::info("📊 Отчёт по проекту '{$this->project->name}' готов", [
            'project_id' => $this->project->id,
            'user_id' => $this->userId,
            'path' => $path,
        ]);
    }

    private function generateCsv($tasks): string
    {
        $headers = [
            'ID',
            'Название',
            'Статус',
            'Приоритет',
            'Исполнитель',
            'Создатель',
            'Дедлайн',
            'Создана',
        ];

        $rows = [];

        foreach ($tasks as $task) {
            // ✅ Статус уже является объектом TaskStatusEnum (благодаря касту в модели)
            // Просто вызываем метод label() у Enum
            $statusLabel = $task->status?->label() ?? (string) $task->status;

            $rows[] = [
                $task->id,
                $task->title,
                $statusLabel,
                $task->priority?->name ?? 'Не указан',
                $task->assignee?->name ?? 'Не назначен',
                $task->creator?->name ?? 'Неизвестно',
                $task->deadline_at?->format('d.m.Y') ?? 'Не указан',
                $task->created_at?->format('d.m.Y H:i') ?? 'Неизвестно',
            ];
        }

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $headers, ';');

        foreach ($rows as $row) {
            fputcsv($csv, $row, ';');
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $content;
    }
}
