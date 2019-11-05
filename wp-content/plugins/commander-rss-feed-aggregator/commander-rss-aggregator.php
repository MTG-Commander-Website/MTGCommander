<?php

/**
 * Plugin Name: MTG Commander custom rss aggregator plugin
 * Plugin URI: n/a
 * Description: A custom RSS feed aggregator for the MTG Commander format website
 * Version: 0.0.1
 * Author: Andrew "Shoe" Lee
 * Author URI: http://mtgcommander.net
 */


add_shortcode('commander-feed-aggregator', 'commander_feed_aggreagator_method');
add_action('save_post', 'generateStaticRulesPage');

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
        $channelTitle = $xmlDoc->getElementsByTagName('title');

        for ($i = 0; $i <= sizeOf($x) - 1; $i++) {
            $item = array();
            $item['title'] = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
            $item['link'] = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
            $desc = $x->item($i)->getElementsByTagName('description')->item(0)->nodeValue;

            if(strlen($desc) > 330){
                            $item['desc'] = substr($desc, 0, 327);
                            $item['desc'] .= "[...]";
            }
            else {
                $item['desc'] = $desc;

            }

            $pubDate = $x->item($i)->getElementsByTagName('pubDate')->length;
            if ($pubDate > 0) {
                $item['pub_date'] = $x->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;
            } else if ($xmlDoc->getElementsByTagName('pubDate')->length > 0) {
                $item['pub_date'] = $xmlDoc->getElementsByTagName('pubDate')->item(0)->nodeValue;
            }


            $creator = $x->item($i)->getElementsByTagName('dc:creator')->length;
            if ($creator > 0) {
                $item['creator'] = $x->item($i)->getElementsByTagName('dc:creator')->item(0)->childNodes->item(0)->nodeValue;
            }
            $item['source'] = $channelTitle->item(0)->nodeValue;

            if (feedFilter($item['title']) || feedFilter($item['desc']) || feedFilter($item['source'])) {
                array_push($feedItems, $item);
            }
        }
    }
    //sort does not work
    // arsort($feedItems);
    $urlPrefix = get_site_url();
    $output = '<div class="rss-feed" >';
    $output .= '<div class="widget rss-item-wrapper">';
    //need SRC for unique rules image
    $output .= '<div class="rss-image-container"><img class="rss_item_image" src="../assets/phelddagrif.jpg"></div>';
    $output .= '<div>';
    $output .= '<div class="rss-title"><h2 class="widget-title">Commander Rules and Banned List</h2></div>';
    $output .= '<div class="rss-desc widget">';
    $output .= '<div><a href="';
    $output .= $urlPrefix . '/rules"><h2>Commander Rules</h2></a></div><br/><div><a href="';
    $output .= $urlPrefix . '/banned-list"><h2>Banned List</h2></a></div>';
    $output .= '</div>';
    $output .= '</div>';

    $output .= '</div>';
    $artRandomizerSeed = 0;
    foreach ($feedItems as $item) {
        $output .= '<div class="widget rss-item-wrapper">';
        $output .= '<div class="rss-image-container"><img class="rss_item_image" src="' . randomImagePath($artRandomizerSeed) . '"></div>';
        $artRandomizerSeed++;
        $output .= '<div>';
        $output .= '<div class="rss-title"><h2 class="widget-title"><a target="_blank" href="' . $item['link'] . '"title="' . $item['title'] . '">';
        $output .= $item['title'];
        $output .= '</a></h2></div>';
        $output .= '<div class="rss-desc widget">';
        $output .= '<div>' . $item['desc'] . '</div>';
        $output .= '<div> From ' . $item['source'] . '</div>';

        if (array_key_exists('creator', $item) != null) {
            $output .= '<div>by ' . $item['creator'] . '</div>';
        }
        if (array_key_exists('pubDate', $item) != null) {
            $output .= '<div>on ' . $item['pubDate'] . '</div>';
        }
        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';
    }
    $output .= '</div>';


    return $output;
}

function feedFilter($string)
{
    $string = strtoupper($string);
    if (strpos($string, 'COMMANDER') !== false || strpos($string, 'EDH') !== false) {
        return true;
    }
    return false;
}

function randomImagePath($seed)
{
    if ($seed % 5 == 0) {
        return "../assets/nicol-bolas.jpg";
    } else if ($seed % 4 == 0) {
        return "../assets/palladia-mors.jpg";
    } else if ($seed % 3 == 0) {
        return "../assets/chromium.jpg";
    } else if ($seed % 2 == 0) {
        return "../assets/arcades-sabboth.jpg";
    } else {
        return "../assets/vaevictis-asmadi.jpg";
    }
}

function generateStaticRulesPage(){
    global $wpdb; 
    $bannedList = $wpdb->get_var( 'SELECT post_content FROM `wp_posts` WHERE post_name = "banned-list" and post_status != "trash"'); 
    $rules = $wpdb->get_var( 'SELECT post_content FROM `wp_posts` WHERE post_name = "rules" and post_status != "trash"'); 

    $bannedListPage = fopen("banned-list.html", "w");
    $rulesPage = fopen("rules.html", "w");

    fwrite($bannedListPage, $bannedList);
    fwrite($rulesPage, $rules);
}