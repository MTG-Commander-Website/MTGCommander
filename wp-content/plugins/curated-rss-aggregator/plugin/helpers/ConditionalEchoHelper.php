<?php

namespace com\cminds\rssaggregator\plugin\helpers;

class ConditionalEchoHelper {

    public static $isShowOutput = FALSE;
    public static $dateFormat = 'Y-m-d H:i:s';
    public static $lineFormat = "%s %s\n";

    public static function sprintf() {
        if (!static::$isShowOutput) {
            return;
        }
        $args = func_get_args();
        $s = vsprintf(array_shift($args), $args);
        echo sprintf(static::$lineFormat, date(static::$dateFormat), $s);
    }

}
