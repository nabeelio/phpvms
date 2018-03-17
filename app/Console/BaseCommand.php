<?php

namespace App\Console;

use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Process\Process;

class BaseCommand extends Command
{
    /**
     * Splice the logger and replace the handler to direct
     * everything to stdout as well as the log
     */
    public function redirectLoggingToStdout()
    {
        $logger = app(\Illuminate\Log\Logger::class);
        $handlers = $logger->getHandlers();

        try {
            $handlers[] = new StreamHandler('php://stdout');
            $logger->setHandlers($handlers);
        } catch (\Exception $e) {
            $this->error('Couldn\'t splice the logger');
        }
    }

    /**
     * Streaming file reader
     * @param $filename
     * @return \Generator
     */
    public function readFile($filename)
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
     * @param $cmd
     * @param bool $return
     * @return string
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function runCommand($cmd, $return=false, $verbose=true)
    {
        if (\is_array($cmd)) {
            $cmd = join(' ', $cmd);
        }

        if($verbose) {
            $this->info('Running "' . $cmd . '"');
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
