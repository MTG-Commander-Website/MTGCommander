<?php

namespace com\cminds\rssaggregator\plugin\cron;

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\cron\FetchFeedJob;
use com\cminds\rssaggregator\plugin\cron\DeleteOutdatedJob;
use com\cminds\rssaggregator\plugin\cron\DeleteAbandonedJob;

class FeedJobManager extends JobManagerAbstract {

    const HOOK = 'cmra_fetch_feed_hook';

    public static function init() {
        parent::init();
        add_action(static::HOOK, array(__CLASS__, 'actionFetchFeedHook'));
    }

    public static function scheduleEvent($term_id, $timestamp = NULL) {
        $interval = get_term_meta($term_id, sprintf('%s_interval', App::PREFIX), TRUE);
        static::clearScheduledHook($term_id);
        if ($interval) {
            wp_schedule_event($timestamp ? : time(), $interval, static::HOOK, array($term_id));
        }
    }

    public static function clearScheduledHook($term_id) {
        wp_clear_scheduled_hook(static::HOOK, array($term_id));
    }

    public static function actionFetchFeedHook($term_id) {
        new DeleteOutdatedJob($term_id);
        new FetchFeedJob($term_id);
        new DeleteAbandonedJob(50);
    }

}
