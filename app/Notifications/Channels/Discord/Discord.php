<?php

namespace App\Notifications\Channels\Discord;

use App\Contracts\Notification;
use App\Support\HttpClient;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class Discord
{
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function send($notifiable, Notification $notification)
    {
        /** @var DiscordMessage $message */
        $message = $notification->toDiscordChannel($notifiable);
        $data = $message->toArray();

        // Send notification to the $notifiable instance...
        try {
            $this->httpClient->post($message->webhook_url, $data);
        } catch (RequestException $e) {
            $response = Psr7\Message::toString($e->getResponse());
            Log::error('Error sending Discord notification: '.$e->getMessage().', '.$response);
        }
    }
}
