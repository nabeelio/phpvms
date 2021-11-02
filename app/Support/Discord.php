<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class Discord
{
    /**
     * Get a user's private channel ID from Discord
     *
     * @param string $discord_id
     */
    public static function getPrivateChannelId(string $discord_id)
    {
        /** @var HttpClient $httpClient */
        $httpClient = app(HttpClient::class);

        try {
            $response = $httpClient->post(
                'https://discord.com/api/users/@me/channels',
                [
                    'recipient_id' => $discord_id,
                ]
            );

            dd($response);
            return $response->id;
        } catch (\Exception $ex) {
            dd($ex);
            Log::error('Could not get private channel id for '.$discord_id.';'.$ex->getMessage());
            return '';
        }
    }
}
