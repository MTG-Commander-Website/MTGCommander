<?php

$cminds_plugin_config = array(
    'plugin-is-pro' => FALSE,
    'plugin-has-addons' => FALSE,
    'plugin-version' => com\cminds\rssaggregator\App::VERSION,
    'plugin-abbrev' => com\cminds\rssaggregator\App::PREFIX,
    'plugin-short-slug' => com\cminds\rssaggregator\App::SLUG,
    'plugin-parent-short-slug' => '',
    'plugin-affiliate' => '',
    'plugin-show-guide' => TRUE,
    'plugin-guide-text' => '<ol>
    <li>Create a list. Each list can contain many categories with different RSS feeds. For example, “Sports”. <a href="https://creativeminds.helpscoutdocs.com/article/886-cm-curated-rss-aggregator-cmcra-categories" target="_blank">Read more</a>.</li>
    <li>Create a category and assign with the desired feeds and assign it to the list. For example, “Football”. <a href="https://creativeminds.helpscoutdocs.com/article/886-cm-curated-rss-aggregator-cmcra-categories" target="_blank">Read more</a>.</li>
    <li>Add the shortcode <code>[cm_rss_aggregator]</code> to a page, specifying the lists or categories to show. For instance, the following would only show the "Test List" list:  <code>[cm_rss_aggregator list="Test List"]</code>. <a href="https://creativeminds.helpscoutdocs.com/article/884-cm-curated-rss-aggregator-cmcra-shortcodes" target="_blank">Read more</a>.</li>
    <li>Troubleshooting: WordPress can take a few minutes to load content of new feeds. <a href="https://creativeminds.helpscoutdocs.com/article/1582-curated-rss-aggregator-troubleshooting" target="_blank">Read more</a>.</li>
    </ol>
    <a href="https://creativeminds.helpscoutdocs.com/article/888-cm-curated-rss-aggregator-cmcra-how-it-works" target="_blank">Check the Quick Start guide for more details</a>',
    'plugin-guide-video-height'      => 240,
    'plugin-guide-videos'            => array(
      array( 'title' => 'Installation tutorial', 'video_id' => '180470423' ),
    ),
    'plugin-upgrade-text'           => 'Good Reasons to Upgrade to Pro',
    'plugin-upgrade-text-list'      => array(
        array( 'title' => 'Introduction to the RSS Aggregator', 'video_time' => '0:00' ),
        array( 'title' => 'Search and filter main list', 'video_time' => '0:32' ),
        array( 'title' => 'Many more options while adding a feed', 'video_time' => '0:41' ),
        array( 'title' => 'Filter feed by keywords', 'video_time' => '0:58' ),
        array( 'title' => 'Feed processing interval', 'video_time' => '1:34' ),
        array( 'title' => 'Feed expiration time', 'video_time' => '1:40' ),
        array( 'title' => 'Feed multiple categories', 'video_time' => '1:47' ),
    ),
    'plugin-upgrade-video-height'   => 240,
    'plugin-upgrade-videos'         => array(
        array( 'title' => 'RSS Aggregator Premium Features', 'video_id' => '176107663' ),
    ),
    'plugin-guide-videos'            => array(
        array( 'title' => 'Installation tutorial', 'video_id' => '180470423' ),
    ),
    'plugin-redirect-after-install' => admin_url('admin.php?page=cmra-options'),
    'plugin-file' => com\cminds\rssaggregator\App::PLUGIN_FILE,
    'plugin-dir-path' => plugin_dir_path(com\cminds\rssaggregator\App::PLUGIN_FILE),
    'plugin-dir-url' => plugin_dir_url(com\cminds\rssaggregator\App::PLUGIN_FILE),
    'plugin-basename' => plugin_basename(com\cminds\rssaggregator\App::PLUGIN_FILE),
    'plugin-icon' => '',
    'plugin-name' => com\cminds\rssaggregator\App::PLUGIN_NAME_EXTENDED,
    'plugin-license-name' => com\cminds\rssaggregator\App::PLUGIN_NAME_EXTENDED,
    'plugin-slug' => '',
    'plugin-menu-item' => com\cminds\rssaggregator\App::SLUG,
    'plugin-textdomain' => com\cminds\rssaggregator\App::SLUG,
    'plugin-userguide-key' => '882-rss-aggregator-cmcra',
    'plugin-store-url' => 'https://www.cminds.com/wordpress-plugins-library/curated-wordpress-rss-aggregator-plugin-by-creativeminds/',
    'plugin-support-url' => '',
    'plugin-review-url' => '',
    'plugin-changelog-url' => 'https://www.cminds.com/wordpress-plugins-library/curated-wordpress-rss-aggregator-plugin-by-creativeminds/#changelog',
    'plugin-licensing-aliases' => array(com\cminds\rssaggregator\App::PLUGIN_NAME_EXTENDED),
    'plugin-compare-table' => '
            <div class="pricing-table" id="pricing-table"><h2 style="padding-left:10px;">Upgrade The Curated RSS Aggregator Plugin:</h2>
                <ul>
                    <li class="heading" style="background-color:red;">Current Edition</li>
                    <li class="price">FREE<br /></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>ONE list of curated links</li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Place anywhere using a shortcode</li>
                   <hr>
                    Other CreativeMinds Offerings
                    <hr>
                 <a href="https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress/" target="blank"><img src="' . plugin_dir_url( __FILE__ ). 'views/Hound2.png"  width="220"></a><br><br><br>
                <a href="https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership/" target="blank"><img src="' . plugin_dir_url( __FILE__ ). 'views/banner_yearly-membership_220px.png"  width="220"></a><br>
                 </ul>

                <ul>
                   <li class="heading">Pro<a href="https://www.cminds.com/wordpress-plugins-library/curated-wordpress-rss-aggregator-plugin-by-creativeminds/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$39.00<br /> <span style="font-size:14px;">(For one Year / 2 Sites)<br />Additional pricing options available <a href="https://www.cminds.com/wordpress-plugins-library/curated-wordpress-rss-aggregator-plugin-by-creativeminds/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/downloads/cm-curated-wordpress-rss-aggregator/?edd_action=add_to_cart&download_id=114995&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free Version Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free features are supported in the pro"></span></li>
 <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Create multiple lists<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="User can create multiple lists of aggregated RSS feeds and place them on any on page or posts"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Filtering options<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Add positive or negative keywords to each feed. Keywords will be matched against RSS feed content and only items which match positive keywords or do not contain negative keywords will be imported"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Update Interval<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Per each RSS feed define the update interval. The plugin will refresh the feed content based on the set interval"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Added tags support<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Tags are placed near RSS feed items with background color. User can filter list items by tags. User can set color to each tag. Tags are defined by keywords found in item content."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Add fast filtering support<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="User can filter list items by text, categories or tags. All relevant items will be shown upon filtering."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Add category background color<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="User can choose to set a background color to each category of feed items."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Styling options<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="User can define several options for the look and feel of the list such as tooltip color, background color, font size and more"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Last update date<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="For each list show last update date"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Number of items included in list<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title=" For each list show the number of items included in it"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Remove Items from feed<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Manually remove items from the feed to manually curated the list of items in the feed"></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Subtitle content extractor<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support reading the content of the RSS link subtitle (appear below the link title) from any existing RSS fields. User can define the field to extract the information from per each category. For example a price of an item."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>List Ordering options<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Order categories within each list using a drag and drop interface."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Order Links in Category<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title=" Ability to define how links are ordered in category ."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Image and Favicon<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Each item will show a favicon from the source it is taken and an image in case it is found in the content of the imported RSS content."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Mark new items<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="You can define in the plugin setting which items in the RSS feed will be automatically marked with a specific tag such as New. The tag is time based so after a defined amount of time the tag will be automaticly removed."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Category widget and shortcode<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support widget to display a category with all feed items and a shortcode to be placed on any post or pages showing all related RSS feed links related to category."></span></li>
<li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Wide Images<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Featured image found in feed can appear in a wide format to attract more attention."></span></li>
                 <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We will also provide on-site support."></span><br />
                         <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the license period, you can update the plugin as many times as needed and receive any version release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="Once license expires, If you choose not to renew the plugin license, you can still continue to use it as long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 40% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="Once license expires, If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
               </ul>
            </div>',
);
