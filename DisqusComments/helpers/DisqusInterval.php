<?php

/**
 * Class DisqusInterval
 * Getting URLs for pages with comments using urlMap in CSV format
 */
class DisqusInterval
{

    /* Constants for Disqus API intervals */
    const INTERVAL_HOUR = '1h';
    const INTERVAL_SIX_HOURS = '6h';
    const INTERVAL_TWENTY_HOURS = '12h';
    const INTERVAL_DAY = '1d';
    const INTERVAL_THREE_DAYS = '3d';
    const INTERVAL_WEEK = '7d';
    const INTERVAL_MONTH = '30d';
    const INTERVAL_THREE_MONTHS = '90d';

    /* Intervals from Disqus API in seconds */
    public static $intervalsInSeconds = array(
        self::INTERVAL_HOUR => 3600,
        self::INTERVAL_SIX_HOURS => 21600,
        self::INTERVAL_TWENTY_HOURS => 43200,
        self::INTERVAL_DAY => 86400,
        self::INTERVAL_THREE_DAYS => 259200,
        self::INTERVAL_WEEK => 604800,
        self::INTERVAL_MONTH => 2592000,
        self::INTERVAL_THREE_MONTHS => 7776000,
    );

    /**
     * Return the closest Disqus API interval for specified time in seconds
     * @param int $seconds
     * @return string
     */
    public static function getIntervalBySeconds($seconds)
    {
        $result = self::INTERVAL_TWENTY_HOURS;
        if ((integer)$seconds > 0) {
            foreach (self::$intervalsInSeconds as $interval => $countSeconds) {
                $result = $interval;
                if ((integer)$seconds <= $countSeconds) {
                    break;
                }
            }
        }
        return $result;
    }

}