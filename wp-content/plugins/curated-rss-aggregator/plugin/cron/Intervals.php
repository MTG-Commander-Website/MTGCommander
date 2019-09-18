<?php

namespace com\cminds\rssaggregator\plugin\cron;

abstract class Intervals {

    public static function Get() {

        return array(
            '5min' => array(
                'interval' => 5 * 60,
                'display' => 'Once every 5 minutes'
            ),
            '15min' => array(
                'interval' => 15 * 60,
                'display' => 'Once every 15 minutes'
            ),
            '3hours' => array(
                'interval' => 3 * 60 * 60,
                'display' => 'Once every 3 hours'
            )
        );
    }

}
