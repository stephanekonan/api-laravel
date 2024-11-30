<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FcmService;
use App\Models\Post;
use App\Models\Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPostEventNotification implements ShouldQueue
{
    use Dispatchable;

    protected $postOrEvent;
    protected $users;

    public function __construct($postOrEvent)
    {
        $this->postOrEvent = $postOrEvent;
        $this->users = User::where('role', 'client')->get(); // Récupère tous les clients
    }

    public function handle(FcmService $fcmService)
    {
        foreach ($this->users as $user) {
            $deviceToken = $user->fcm_token; // Assurez-vous que chaque utilisateur a un token FCM

            // Initialiser le titre et le corps de la notification
            $title = "Nouveau contenu disponible";
            $body = "";

            if ($this->postOrEvent instanceof Post) {
                $body = "Un nouveau post a été ajouté : " . $this->postOrEvent->title;
            } elseif ($this->postOrEvent instanceof Event) {
                $body = "Un nouveau événement a été ajouté : " . $this->postOrEvent->title;
            }

            // Envoie la notification si un token FCM est présent
            if ($body && $deviceToken) {
                $data = [
                    'type' => $this->postOrEvent instanceof Post ? 'post' : 'event',
                    'post_event_id' => $this->postOrEvent->id
                ];

                // Envoie de la notification via le service FCM
                $fcmService->sendNotification($deviceToken, $title, $body, $data);
            }
        }
    }
}
