<?php

namespace App\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BaseCommand extends Command
{

    /**
     * Streaming file read
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
     */
    public function runCommand($cmd)
    {
        if (is_array($cmd))
            $cmd = join(' ', $cmd);

        $this->info('Running "' . $cmd . '"');

        $process = new Process($cmd);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
        });
    }
}
