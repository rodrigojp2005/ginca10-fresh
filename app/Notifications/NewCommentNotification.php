<?php

namespace App\Notifications;

use App\Models\Comentario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Str;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comentario $comentario)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'gincana_id' => $this->comentario->gincana_id,
            'comentario_id' => $this->comentario->id,
            'conteudo' => $this->comentario->conteudo,
            'autor' => $this->comentario->user?->name,
            'gincana_nome' => $this->comentario->gincana?->nome,
        ];
    }

    public function toWebPush(object $notifiable, object $notification = null): WebPushMessage
    {
        $gincana = $this->comentario->gincana; // lazy load ok
        $user = $this->comentario->user;

        return (new WebPushMessage)
            ->title('Novo comentário em ' . ($gincana?->nome ?? 'uma gincana'))
            ->icon('/favicon.ico')
            ->body(($user?->name ?? 'Alguém') . ': ' . Str::limit($this->comentario->conteudo, 80))
            ->data([
                'gincana_id' => $this->comentario->gincana_id,
                'comentario_id' => $this->comentario->id,
            ]);
    }
}
