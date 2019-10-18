<?php

/**
 * Plugin Name: MTG Commander custom rss aggregator plugin
 * Plugin URI: n/a
 * Description: A custom RSS feed aggregator for the MTG Commander format website
 * Version: 0.0.1
 * Author: Andrew "Shoe" Lee
 * Author URI: http://mtgcommander.net
 */

define('PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLUGIN_PATH', plugin_dir_path(__FILE__));

// include(WP_RSS_RETRIEVER_PLUGIN_PATH . 'welcome-screen.php');

add_shortcode('commander-feed-aggregator', 'commander_feed_aggreagator_method');

class RssItem
{
    public $title = '';
    public $link = '';
    public $desc = '';
    public $pub_date = '';
    public $creator  = '';
    public $source = '';
}

function commander_feed_aggreagator_method($atts)
{
    return display_feed();
}

function display_feed()
{
    $feedItems = array();
    $finalFeed = "";
    $urls = array(
        'https://articles.edhrec.com/feed/',
        'https://magic.wizards.com/en/rss/rss.xml?tags=Commander&amp;lang=en',
        'https://www.coolstuffinc.com/articles_feed.rss',
        'http://www.starcitygames.com/rss/rssfeed.xml',
        'http://www.channelfireball.com/feed/'
    );

    foreach ($urls as $key => $url) {
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($url);

        $x = $xmlDoc->getElementsByTagName('item');
        $channelInfo = $xmlDoc->getElementsByTagName('channel');

        for ($i = 0; $i <= sizeOf($x)-1; $i++) {
            //Process EDHREC feed
            $item = new RssItem();
            $item->title = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
            $item->link = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
            $item->desc = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
            $item->pub_date = $x->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;
            // $item->creator = $x->item($i)->getElementsByTagName('dc:creator')->item(0)->childNodes->item(0)->nodeValue;
            $item->source = $channelInfo->item(0)->nodeValue;

            echo $item->title;
        }
    }

    return $finalFeed;
}
