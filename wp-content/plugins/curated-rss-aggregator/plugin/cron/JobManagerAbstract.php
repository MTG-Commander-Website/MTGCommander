<?php

namespace com\cminds\rssaggregator\plugin\cron;

use com\cminds\rssaggregator\plugin\cron\Intervals;

abstract class JobManagerAbstract {

    public function __construct() {
        static::init();
    }

    public static function init() {
        add_filter('cron_schedules', array(__CLASS__, 'filterCronSchedules'));
    }

    public static function filterCronSchedules($schedules) {
        foreach (Intervals::Get() as $k => $v) {
            if (!isset($schedules[$k])) {
                $schedules[$k] = array(
                    'interval' => $v['interval'],
                    'display' => __($v['display'])
                );
            }
        }
        return $schedules;
    }

}
