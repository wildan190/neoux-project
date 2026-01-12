<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ModelOperationNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public $model;
    public $action;
    public $modelName;
    public $customMessage;
    public $customUrl;

    public function __construct($model, string $action, ?string $customMessage = null, ?string $customUrl = null)
    {
        $this->model = $model;
        $this->action = $action; // 'created', 'updated', 'deleted'
        $this->modelName = class_basename($model);
        $this->customMessage = $customMessage;
        $this->customUrl = $customUrl;
    }

    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = Str::headline($this->modelName) . ' ' . ucfirst($this->action);

        $message = $this->customMessage;
        if (!$message) {
            $message = "A " . Str::headline($this->modelName) . " has been {$this->action}.";

            // Try to get a meaningful identifier
            $identifier = $this->model->number ?? $this->model->name ?? $this->model->title ?? '#' . $this->model->id;
            if ($identifier) {
                $message = "{$title}: {$identifier} has been {$this->action}.";
            }
        }

        return [
            'type' => 'model_operation',
            'title' => $title,
            'message' => $message,
            'url' => $this->customUrl ?? $this->resolveUrl(),
            'action_text' => 'View Details',
            'model_type' => $this->modelName,
            'model_id' => $this->model->id,
        ];
    }

    protected function resolveUrl()
    {
        // Try to guess the route
        // E.g. DebitNote -> procurement.debit-notes.show
        $kebabName = Str::kebab($this->modelName);
        $pluralName = Str::plural($kebabName);

        $routes = [
            "procurement.{$pluralName}.show",
            "{$pluralName}.show",
        ];

        foreach ($routes as $route) {
            if (\Route::has($route)) {
                return route($route, $this->model->id);
            }
        }

        return '#';
    }

    public function broadcastAs(): string
    {
        return 'Illuminate\Notifications\Events\BroadcastNotificationCreated';
    }
}
