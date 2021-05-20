<?php

namespace App\Console\Commands;

use App;
use App\Contracts\Command;

class EmailTest extends Command
{
    protected $signature = 'phpvms:email-test';
    protected $description = 'Send a test notification to admins';

    /**
     * Run dev related commands
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function handle()
    {
        /** @var App\Notifications\EventHandler $eventHandler */
        $eventHandler = app(App\Notifications\EventHandler::class);

        $news = new App\Models\News();
        $news->user_id = 1;
        $news->subject = 'Test News';
        $news->body = 'Test Body';
        $news->save();

        $newsEvent = new App\Events\NewsAdded($news);
        $eventHandler->onNewsAdded($newsEvent);
    }
}
