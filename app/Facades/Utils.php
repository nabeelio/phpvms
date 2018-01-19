<?php

namespace App\Facades;

use GuzzleHttp\Client;
use \Illuminate\Support\Facades\Facade;

class Utils extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'utils';
    }

    /**
     * Download a URI. If a file is given, it will save the downloaded
     * content into that file
     * @param $uri
     * @param null $file
     * @return string
     * @throws \RuntimeException
     */
    public static function downloadUrl($uri, $file=null)
    {
        $opts = [];
        if($file !== null) {
            $opts['sink'] = $file;
        }

        $client = new Client();
        $response = $client->request('GET', $uri, $opts);

        $body = $response->getBody()->getContents();
        if($response->getHeader('content-type') === 'application/json') {
            $body = \GuzzleHttp\json_decode($body);
        }

        return $body;
    }


    /**
     * Returns a 40 character API key that a user can use
     * @return string
     */
    public static function generateApiKey()
    {
        $key = substr(sha1(time() . mt_rand()), 0, 20);
        return $key;
    }

    public static function minutesToTimeParts($minutes): array
    {
        $hours = floor($minutes / 60);
        $minutes %= 60;

        return ['h' => $hours, 'm' => $minutes];
    }

    public static function minutesToTimeString($minutes): string
    {
        $hm = self::minutesToTimeParts($minutes);
        return $hm['h']. 'h '. $hm['m'] . 'm';
    }

    /**
     * Convert seconds to an array of hours, minutes, seconds
     * @param $seconds
     * @return array['h', 'm', 's']
     */
    public static function secondsToTimeParts($seconds): array
    {
        $dtF = new \DateTime('@0', new \DateTimeZone('UTC'));
        $dtT = new \DateTime("@$seconds", new \DateTimeZone('UTC'));

        $t = $dtF->diff($dtT);

        $retval = [];
        $retval['h'] = (int) $t->format('%h');
        $retval['m'] = (int) $t->format('%i');
        $retval['s'] = (int) $t->format('%s');

        return $retval;
    }

    /**
     * Convert seconds to HH MM format
     * @param $seconds
     * @param bool $incl_sec
     * @return string
     */
    public static function secondsToTimeString($seconds, $incl_sec=false): string
    {
        $hms = self::secondsToTimeParts($seconds);
        $format = $hms['h'].'h '.$hms['m'].'m';
        if($incl_sec) {
            $format .= ' '.$hms['s'].'s';
        }

        return $format;
    }

    /**
     * @param $minutes
     * @return float|int
     */
    public static function minutesToSeconds($minutes)
    {
        return $minutes * 60;
    }

    /**
     * Convert the seconds to minutes and then round it up
     * @param $seconds
     * @return float|int
     */
    public static function secondsToMinutes($seconds)
    {
        return ceil($seconds/60);
    }

    /**
     * Convert hours to minutes. Pretty complex
     * @param $minutes
     * @return float|int
     */
    public static function minutesToHours($minutes)
    {
        return $minutes/60;
    }

    /**
     * @param $hours
     * @param null $minutes
     * @return float|int
     */
    public static function hoursToMinutes($hours, $minutes=null)
    {
        $total = (int) $hours * 60;
        if($minutes) {
            $total += (int) $minutes;
        }

        return $total;
    }

    /**
     * Bitwise operator for setting days of week to integer field
     * @param int $datefield initial datefield
     * @param array $day_enums Array of values from config("enum.days")
     * @return int
     */
    public static function setDays(int $datefield, array $day_enums) {
        foreach($day_enums as $day) {
            $datefield |= $day;
        }

        return $datefield;
    }

    /**
     * Bit check if a day exists within a integer bitfield
     * @param int $datefield datefield from database
     * @param int $day_enum Value from config("enum.days")
     * @return bool
     */
    public static function hasDay(int $datefield, int $day_enum) {
        return ($datefield & $day_enum) === $datefield;
    }
}
