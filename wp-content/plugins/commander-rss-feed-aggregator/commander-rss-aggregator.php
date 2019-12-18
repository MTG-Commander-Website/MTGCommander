<?php

/**
 * Plugin Name: MTG Commander custom rss aggregator plugin
 * Plugin URI: n/a
 * Description: A custom RSS feed aggregator for the MTG Commander format website
 * Version: 0.3.0
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

function create_feed_item_array()
{
    $feedItems = array();
    $sites = array(
        array('https://magic.wizards.com/en/rss/rss.xml?tags=Commander&amp;lang=en', "/assets/nicol-bolas.jpg"),
        array('https://www.coolstuffinc.com/articles_feed.rss', "/assets/vaevictis-asmadi.jpg"),
        array('https://articles.edhrec.com/feed/', "/assets/palladia-mors.jpg"),
        array('http://www.channelfireball.com/feed/', "/assets/chromium.jpg"),
        array('https://articles.starcitygames.com/feed/', "/assets/arcades-sabboth.jpg")
    );

    foreach ($sites as $key => $siteInfo) {
        libxml_use_internal_errors(true);
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($siteInfo[0]);
        if (sizeOf(libxml_get_errors()) > 0) {
            libxml_clear_errors();
            if ($COMMANDER_RSS_DEBUG) {
                $output .= "ERROR: " . libxml_get_errors();
            }
            continue;
        }

        $xmlFeedItem = $xmlDoc->getElementsByTagName('item');
        $channelTitle = $xmlDoc->getElementsByTagName('title');

        foreach ($xmlFeedItem as $key => $node) {
            $item = array();
            $item['imageURL'] = $siteInfo[1];
            $item['title'] = $node->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
            $item['link'] = $node->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
            $desc = $node->getElementsByTagName('description')->item(0)->nodeValue;

            // if (strlen($desc) > 330) {
            //     $item['desc'] = substr($desc, 0, 327);
            //     $item['desc'] .= "[...]";
            // } else {
            $item['desc'] = $desc;
            // }

            $pubDate = $node->getElementsByTagName('pubDate')->length;
            if ($pubDate > 0) {
                $item['pub_date'] = substr($node->getElementsByTagName('pubDate')->item(0)->nodeValue, 0, 16);
                $item['sort_date'] = strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue);
            } else if ($xmlDoc->getElementsByTagName('pubDate')->length > 0) {
                //Not sure if this is ever hit now?
                $item['pub_date'] = substr($xmlDoc->getElementsByTagName('pubDate')->item(0)->nodeValue, 0, 16);
                $item['sort_date'] = strtotime($xmlDoc->getElementsByTagName('pubDate')->item(0)->nodeValue);
            }


            $creator = $node->getElementsByTagName('dc:creator')->length;
            if ($creator > 0) {
                $item['creator'] = $node->getElementsByTagName('dc:creator')->item(0)->nodeValue;
            }
            $item['source'] = $channelTitle->item(0)->nodeValue;

            if (feedFilter($item['title']) || feedFilter($item['desc']) || feedFilter($item['source'])) {
                array_push($feedItems, $item);
            }
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
	    	<a class="post-thumbnail" href="' . $item['link'] . '" title="' . $item['title'] . '" aria-hidden="true">
                <img width="558" height="372" src="' . $urlPrefix . $item['imageURL'] . '" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="Unlike that post…">	
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
