<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TimeParser
{
    /**
     * @param  null  $timezone
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function parseStringToDate(string $date, string $format = 'Y-m-d H:i:s', $timezone = null): string
    {
        return self::parseStringToCarbon($date, $timezone)
            ->format($format);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function parseStringToCarbon(string $date, $timezone = null): Carbon
    {
        if (empty($timezone)) {
            $timezone = (request()->get('timezone') | 0) / 60;
        }
        if (is_numeric($date)) {
            $date = Carbon::createFromTimestamp($date);
        } else {
            $date = Carbon::parse($date);
        }

        return $date
            ->setTimezone($timezone);
    }
}
