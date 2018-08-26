<?php

namespace App\Console;

use Log;
use Symfony\Component\Process\Process;

/**
 * Class BaseCommand
 */
abstract class Command extends \Illuminate\Console\Command
{
    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * Splice the logger and replace the active handlers with
     * the handlers from the "cron" stack in config/logging.php
     *
     * Close out any of the existing handlers so we don't leave
     * file descriptors leaking around
     *
     * @param string $channel_name Channel name to grab the handlers from
     */
    public function redirectLoggingToStdout($channel_name): void
    {
        $logger = app(\Illuminate\Log\Logger::class);

        // Close the existing loggers
        try {
            $handlers = $logger->getHandlers();
            foreach ($handlers as $handler) {
                $handler->close();
            }
        } catch (\Exception $e) {
            $this->error('Error closing handlers: '.$e->getMessage());
        }

        // Open the handlers for the channel name,
        // and then set them to the main logger
        try {
            $logger->setHandlers(
                Log::channel($channel_name)->getHandlers()
            );
        } catch (\Exception $e) {
            $this->error('Couldn\'t splice the logger: '.$e->getMessage());
        }
    }

    /**
     * Streaming file reader
     *
     * @param $filename
     *
     * @return \Generator
     */
    public function readFile($filename): ?\Generator
    {
        $fp = fopen($filename, 'rb');
        while (($line = fgets($fp)) !== false) {
            $line = rtrim($line, "\r\n");
            if ($line[0] === ';') {
                continue;
            }

            yield $line;
        }

        fclose($fp);
    }

    /**
     * @param       $cmd
     * @param bool  $return
     * @param mixed $verbose
     *
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     *
     * @return string
     */
    public function runCommand($cmd, $return = false, $verbose = true): string
    {
        if (\is_array($cmd)) {
            $cmd = implode(' ', $cmd);
        }

        if ($verbose) {
            $this->info('Running "'.$cmd.'"');
        }

        $val = '';
        $process = new Process($cmd);
        $process->run(function ($type, $buffer) use ($return, &$val) {
            if ($return) {
                $val .= $buffer;
            } else {
                echo $buffer;
            }
        });

        return $val;
    }
}
