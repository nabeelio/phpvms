<?php

namespace App\Notifications\Channels\Discord;

use Carbon\Carbon;

/**
 * Original is from https://gist.github.com/freekmurze/e4415090f650e070d3de8b905875cf78
 *
 * Markdown guide: https://birdie0.github.io/discord-webhooks-guide/other/discord_markdown.html
 */
class DiscordMessage
{
    const COLOR_SUCCESS = '#0B6623';
    const COLOR_WARNING = '#FD6A02';
    const COLOR_ERROR = '#ED2939';

    public $webhook_url;

    protected $title;
    protected $url;
    protected $description;
    protected $timestamp;
    protected $footer;
    protected $color;
    protected $author = [];
    protected $fields = [];

    /**
     * Supply the webhook URL that this should be going to
     */
    public function webhook(string $url): self
    {
        $this->webhook_url = $url;
        return $this;
    }

    /**
     * Title of the notification
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param array|string $descriptionLines
     */
    public function description($descriptionLines): self
    {
        if (!is_array($descriptionLines)) {
            $descriptionLines = [$descriptionLines];
        }

        $this->description = implode(PHP_EOL, $descriptionLines);
        return $this;
    }

    /**
     * Set the author information:
     * [
     *    'name'     => '',
     *    'url'      => '',
     *    'icon_url' => '',
     */
    public function author(array $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Set the fields
     */
    public function fields(array $fields): self
    {
        $this->fields = [];
        foreach ($fields as $name => $value) {
            $this->fields[] = [
                'name'   => '**'.$name.'**', // bold
                'value'  => $value,
                'inline' => true,
            ];
        }

        return $this;
    }

    public function footer(string $footer): self
    {
        $this->footer = $footer;
        return $this;
    }

    public function success(): self
    {
        $this->color = static::COLOR_SUCCESS;
        return $this;
    }

    public function warning(): self
    {
        $this->color = static::COLOR_WARNING;
        return $this;
    }

    public function error(): self
    {
        $this->color = static::COLOR_ERROR;
        return $this;
    }

    public function toArray(): array
    {
        $embeds = [
            'title'       => $this->title,
            'url'         => $this->url,
            'type'        => 'rich',
            'description' => $this->description,
            'author'      => $this->author,
            'timestamp'   => Carbon::now('UTC'),
        ];

        if (!empty($this->fields)) {
            $embeds['fields'] = $this->fields;
        }

        if (!empty($this->footer)) {
            $embeds['footer'] = [
                'text' => $this->footer,
            ];
        }

        return [
            'embeds' => [$embeds],
        ];
    }
}
