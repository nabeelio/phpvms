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
     * @param bool $return
     * @return string
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
        $process->run(function ($type, $buffer) use ($return, $val) {
            if ($return) {
                $val .= $buffer;
            } else {
                echo $buffer;
            }

            /*if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }*/
        });

        return $val;
    }
}
