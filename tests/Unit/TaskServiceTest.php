<?php

use App\Enums\TaskStatusEnum;
use App\Events\TaskStatusChanged;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->task = Task::factory()->create([
        'status_id' => TaskStatusEnum::NEW->value,
        'creator_id' => $this->user->id,
    ]);
    $this->taskService = app(TaskService::class);
});

test('can transition from NEW to IN_PROGRESS', function () {
    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::IN_PROGRESS,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::IN_PROGRESS);
});

test('can transition from NEW to ON_HOLD', function () {
    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::ON_HOLD,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::ON_HOLD);
});

test('cannot transition from NEW to REVIEW directly', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Невозможно перевести задачу');

    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::REVIEW,
        $this->user->id
    );
});

test('can transition from IN_PROGRESS to REVIEW', function () {
    $this->task->update(['status_id' => TaskStatusEnum::IN_PROGRESS->value]);

    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::REVIEW,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::REVIEW);
});

test('can transition from REVIEW to DONE', function () {
    $this->task->update(['status_id' => TaskStatusEnum::REVIEW->value]);

    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::DONE,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::DONE);
});

test('can transition from DONE to CLOSED', function () {
    $this->task->update(['status_id' => TaskStatusEnum::DONE->value]);

    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::CLOSED,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::CLOSED);
});

test('cannot transition from CLOSED to any status', function () {
    $this->task->update(['status_id' => TaskStatusEnum::CLOSED->value]);

    $this->expectException(InvalidArgumentException::class);

    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::NEW,
        $this->user->id
    );
});

test('can transition from ON_HOLD to NEW', function () {
    $this->task->update(['status_id' => TaskStatusEnum::ON_HOLD->value]);

    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::NEW,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::NEW);
});

test('can transition from DONE back to REVIEW', function () {
    $this->task->update(['status_id' => TaskStatusEnum::DONE->value]);

    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::REVIEW,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::REVIEW);
});

test('can transition from REVIEW back to IN_PROGRESS', function () {
    $this->task->update(['status_id' => TaskStatusEnum::REVIEW->value]);

    $result = $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::IN_PROGRESS,
        $this->user->id
    );

    expect($result)->toBeTrue();
    expect($this->task->fresh()->status_id)->toBe(TaskStatusEnum::IN_PROGRESS);
});

test('started_at is set when transitioning to IN_PROGRESS', function () {
    $this->task->update(['started_at' => null]);

    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::IN_PROGRESS,
        $this->user->id
    );

    expect($this->task->fresh()->started_at)->not->toBeNull();
});

test('completed_at is set when transitioning to DONE', function () {
    $this->task->update([
        'completed_at' => null,
        'status_id' => TaskStatusEnum::REVIEW->value,
    ]);

    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::DONE,
        $this->user->id
    );

    expect($this->task->fresh()->completed_at)->not->toBeNull();
});

test('completed_at is set when transitioning to CLOSED', function () {
    $this->task->update([
        'completed_at' => null,
        'status_id' => TaskStatusEnum::DONE->value,
    ]);

    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::CLOSED,
        $this->user->id
    );

    expect($this->task->fresh()->completed_at)->not->toBeNull();
});

test('TaskStatusChanged event is dispatched', function () {
    Event::fake();

    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::IN_PROGRESS,
        $this->user->id
    );

    Event::assertDispatched(TaskStatusChanged::class, function ($event) {
        return $event->task->id === $this->task->id
            && $event->oldStatus === TaskStatusEnum::NEW
            && $event->newStatus === TaskStatusEnum::IN_PROGRESS
            && $event->changedByUserId === $this->user->id;
    });
});

test('history is logged when status changes', function () {
    $this->taskService->changeStatus(
        $this->task,
        TaskStatusEnum::IN_PROGRESS,
        $this->user->id
    );

    $this->assertDatabaseHas('task_histories', [
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
        'field' => 'status',
        'old_value' => TaskStatusEnum::NEW->label(),
        'new_value' => TaskStatusEnum::IN_PROGRESS->label(),
    ]);
});
