<?php

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\ListTaxonomy;
use com\cminds\rssaggregator\plugin\helpers\HTMLHelper;
use com\cminds\rssaggregator\plugin\misc\SimplePieXMLNamespaces;
use com\cminds\rssaggregator\plugin\misc\UserAgents;
use \com\cminds\rssaggregator\plugin\helpers\SimplePieHelper;
?>

<div class="form-field">
    <label for="term-feed-url">Feed URLs</label>
    <textarea name="<?php echo sprintf('%s_feed_url', App::PREFIX); ?>" id="term-feed-url" rows="5" size="40" style="white-space: pre"></textarea>
    <p>The Feed URLs - one entry per line.</p>
</div>
<div class="form-field">
    <label for="term-feed-url">Feed name</label>
    <input name="<?php echo sprintf('%s_feed_name', App::PREFIX); ?>" id="term-feed-name" type="text" value="" size="40" />
    <p>The Feed name (used to display source of RSS links).</p>
</div>
<input name="<?php echo sprintf('%s_interval', App::PREFIX); ?>" id="term-interval" style="visibility:hidden;" type="hidden" value="3hours" />
<div class="form-field">
    <label for="term-detele-after">Feed entries presentation duration</label>
    <select name="<?php echo sprintf('%s_delete_after', App::PREFIX); ?>" id="term-delete-after" class="postform">
        <option value="">Never</option>
        <?php
        foreach (array(
    '1 day' => 86400,
    '2 days' => 2 * 86400,
    '3 days' => 3 * 86400,
    '1 week' => 7 * 86400,
    '2 weeks' => 14 * 86400,
    '1 month' => 30 * 86400,
    '3 months' => 90 * 86400
        ) as $k => $v):
            ?>
            <?php echo '<option value="' . $v . '"' . ($k == '1 week' ? 'selected="selected"' : '') . '>' . $k . '</option>'; ?>
        <?php endforeach; ?>
    </select>
    <p>Feed entries older than selected period of time will be deleted.</p>
</div>
<div class="form-field">
    <label>
        <input type="hidden" name="<?php echo sprintf('%s_always_refresh_links', App::PREFIX); ?>" value="0" />
        <input type="checkbox" name="<?php echo sprintf('%s_always_refresh_links', App::PREFIX); ?>" id="fix_relative_paths" value="1" />
        Always refresh links before fetching
    </label>
    <p>After checking this the links will be refreshed before fetching (so only the items in the feed will be displayed).</p>
</div>
<div class="form-field form-required">
    <label for="term-tags">Lists</label>
    <div class="cmra-form-field-checkboxes">
        <?php
        wp_list_categories(array(
            'hide_empty' => FALSE,
            'hierarchical' => FALSE,
            'style' => 'none',
            'pad_counts' => 0,
            'taxonomy' => ListTaxonomy::TAXONOMY,
            'walker' => new Walker_Category_Checklist()
        ));
        ?>
    </div>
</div>
<div class="form-field">
    <label for="term-show-favicons">Favicons before links</label>
    <select name="<?php echo sprintf('%s_show_favicons', App::PREFIX); ?>" id="term-show-favicons" class="postform">
        <option value="0">Use global settings</option>
        <option value="1">Show</option>
        <option value="2">Hide</option>
    </select>
    <p>Use this option to override global settings.</p>
</div>
<div class="form-field">
    <label for="term-user-agent">User Agent</label>
    <select name="<?php echo sprintf('%s_user_agent', App::PREFIX); ?>" id="term-user-agent" class="postform">
        <option value="">Use global settings</option>
        <?php
        foreach (UserAgents::getAll() as $key => $value) {
            echo sprintf('<option value="%s" %s> %s</option>', esc_attr($value), false ? 'selected="selected"' : '', esc_attr($key));
        }
        ?>
    </select>
    <p>Used to fetch RSS feed.</p>
</div>
