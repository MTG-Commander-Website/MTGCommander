<?php

/**
 * Plugin Name: MTG Commander custom rss aggregator plugin
 * Plugin URI: n/a
 * Description: A custom RSS feed aggregator for the MTG Commander format website
 * Version: 0.3.2
 * Author: Andrew "Shoe" Lee
 * Author URI: http://mtgcommander.net
 */


$COMMANDER_RSS_DEBUG = false;

add_shortcode('commander-rss-aggregator', 'insert_feed');
add_action('save_post', 'generateStaticRulesPage');
register_activation_hook(__FILE__, 'activate_feed_generation');
add_action('generate_feed', 'generate_feed_html');

function activate_feed_generation()
{
    if (!wp_next_scheduled('generate_feed')) {
        wp_schedule_event(time(), 'daily', 'generate_feed');
    }
}

function generate_feed_html()
{

    $feedItems = create_feed_item_array();
    $sortedFeedItems = sort_feed_by_date($feedItems);
    $output = generate_feed_dom($sortedFeedItems);
    save_feed_html($output);
}

function default_processor($node, $channelTitle) {
    $item = array();
    $item['title'] = $node->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
    $item['desc'] = $node->getElementsByTagName('description')->item(0)->nodeValue;
    $item['source'] = $channelTitle->item(0)->nodeValue;
    if (!feedFilter($item['title']) && !feedFilter($item['desc']) && !feedFilter($item['source'])) {
        // Skip this item
        return NULL;
    }

    $item['link'] = $node->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;

    $pubDate = $node->getElementsByTagName('pubDate')->length;
    if ($pubDate > 0) {
        $item['pub_date'] = substr($node->getElementsByTagName('pubDate')->item(0)->nodeValue, 0, 16);
        $item['sort_date'] = strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue);
    } else if ($xmlDoc->getElementsByTagName('pubDate')->length > 0) {
        //Not sure if this is ever hit now?
        $item['pub_date'] = substr($xmlDoc->getElementsByTagName('pubDate')->item(0)->nodeValue, 0, 16);
        $item['sort_date'] = strtotime($xmlDoc->getElementsByTagName('pubDate')->item(0)->nodeValue);
    }

    $creator_tags = $node->getElementsByTagName('creator');
    foreach ($creator_tags as $tag) {
        if (array_key_exists('creator', $item)) {
            error_log("Found more than one creator tag for ".$item['link']);
        }
        $item['creator'] = $tag->nodeValue;
    }

    return $item;
}

function edhrec_processor($node, $channelTitle) {
    $item = default_processor($node, $channelTitle);
    if (is_null($item)) {
        return NULL;
    }
    if (array_key_exists('creator', $item) && strpos('Community Spotlight', $item['creator']) !== false) {
        // Skip EDHRec's "Community spotlight" articles
        return NULL;
    }
    $title_fragments = explode("|",$item['title']);
    $item['title'] = $title_fragments[0];
    return $item;
}

function create_feed_item_array()
{
    $feedItems = array();
    $sites = array(
        array('https://magic.wizards.com/en/rss/rss.xml?tags=Commander&amp;lang=en',
              '/assets/1200px-Wizards_of_the_Coast_logo.svg.png', 'default_processor'),
        array('https://www.coolstuffinc.com/articles_feed.rss', '/assets/csi_logo.jpg', 'default_processor'),
        array('https://articles.edhrec.com/feed/', '/assets/edhrec_logo.png', 'edhrec_processor'),
        array('http://www.channelfireball.com/feed/', '/assets/cfb_logo.png', 'default_processor'),
        array('https://articles.starcitygames.com/feed/', '/assets/scg_logo.jpg', 'default_processor'),
        array('http://staging.mtgcommander.net/index.php/feed/', '/assets/Logo-Design-small.png', 'default_processor'),
    );

    foreach ($sites as $key => $siteInfo) {
        libxml_use_internal_errors(true);
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($siteInfo[0]);
        if (sizeOf(libxml_get_errors()) > 0) {
            libxml_clear_errors();
            continue;
        }

        $xmlFeedItem = $xmlDoc->getElementsByTagName('item');
        $channelTitle = $xmlDoc->getElementsByTagName('title');

        foreach ($xmlFeedItem as $key => $node) {
            // Invoke per-feed processing
            $item = call_user_func($siteInfo[2], $node, $channelTitle);
            if (is_null($item)) {
                continue;
            }
            $item['imageURL'] = $siteInfo[1];
            array_push($feedItems, $item);
        }
    }

    return $feedItems;
}

function sort_feed_by_date($feedItems)
{
    foreach ($feedItems as $key => $feedItem) {
        $sortDate[$key] = $feedItem['sort_date'];
    }
    array_multisort($sortDate, SORT_DESC, $feedItems);
    return $feedItems;
}

function generate_feed_dom($feedItems)
{
    $output = '<div class="rss-feed">';
    $urlPrefix = get_site_url();
    $artRandomizerSeed = 0;
    foreach ($feedItems as $item) {
        $creator = "";
        $pubDate = "";

        if (array_key_exists('creator', $item) != null) {
            $creator = '<span class="byline"><span class="author vcard">' . $item['creator'] . '</span>';
        }

        $output .='<article class="post type-post status-publish format-standard has-post-thumbnail hentry">
                <a class="post-thumbnail feed-logo" href="' . $item['link'] . '" title="' . $item['title'] . '" aria-hidden="true">
                <img src="' . $urlPrefix . $item['imageURL'] . '" class="attachment-post-thumbnail size-post-thumbnail">        
            </a>
            <header class="entry-header">
                <h1 class="entry-title"><a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a></h1>
                        <div class="entry-meta">
                    <span class="entry-date"><a href="' . $item['link'] . '" title="' . $item['title'] . '"><time class="entry-date">' . $item['pub_date'] . '</time></a></span>'
                    . $creator .
                    '<span class="byline">' . $item['source'] . '</span>
                        </div>
            </header>
                <div class="entry-header">
                        <p>' . $item['desc'] . '</p>
                </div>
        </article>';
    }
    $output .= '</div>';
    return $output;
}

function insert_feed()
{
    $feedFile = fopen(ABSPATH . "assets/feed.html", "r");
    $feedHtml = fread($feedFile, fileSize(ABSPATH . "assets/feed.html"));
    fclose($feedFile);
    return  $feedHtml;
}

function save_feed_html($content)
{
    $feedHTML = fopen(ABSPATH . "assets/feed.html", "w");
    fwrite($feedHTML, $content);
    fclose($feedHTML);
}

function feedFilter($string)
{
    $string = strtoupper($string);
    if (strpos($string, 'COMMANDER') !== false || strpos($string, 'EDH') !== false) {
        return true;
    }
    return false;
}

function generateStaticRulesPage()
{
    global $wpdb;
    $bannedList = $wpdb->get_var('SELECT post_content FROM `wp_posts` WHERE post_name = "banned-list" and post_status != "trash"');
    $rules = $wpdb->get_var('SELECT post_content FROM `wp_posts` WHERE post_name = "rules" and post_status != "trash"');

    $bannedListPage = fopen(ABSPATH . "assets/banned-list.html", "w");
    $rulesPage = fopen(ABSPATH . "assets/rules.html", "w");

    fwrite($bannedListPage, $bannedList);
    fwrite($rulesPage, $rules);

    fclose($bannedListPage);
    fclose($rulesPage);
}
