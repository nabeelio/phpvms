<?php

namespace App\Notifications\Channels\Discord;

use App\Contracts\Notification;
use App\Support\HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Log;

class DiscordWebhook
{
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toDiscordChannel($notifiable);
        if ($message === null || empty($message->webhook_url)) {
            Log::debug('Discord notifications not configured, skipping');
            return;
        }

        try {
            $data = $message->toArray();
            $this->httpClient->post($message->webhook_url, $data);
        } catch (RequestException $e) {
            $request = Psr7\Message::toString($e->getRequest());
            $response = Psr7\Message::toString($e->getResponse());
            Log::error('Error sending Discord notification: request: '.$e->getMessage().', '.$request);
            Log::error('Error sending Discord notification: response: '.$response);
        }
    }
}
