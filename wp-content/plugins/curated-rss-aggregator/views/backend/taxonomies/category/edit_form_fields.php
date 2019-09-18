<?php

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\ListTaxonomy;
use com\cminds\rssaggregator\plugin\helpers\HTMLHelper;
use com\cminds\rssaggregator\plugin\misc\SimplePieXMLNamespaces;
use com\cminds\rssaggregator\plugin\misc\UserAgents;
use \com\cminds\rssaggregator\plugin\helpers\SimplePieHelper;
?>

<tr class="form-field">
    <th scope="row" valign="top"><label for="term-feed-url">Feed URLs</label></th>
    <td>
        <textarea name="<?php echo sprintf( '%s_feed_url', App::PREFIX ); ?>" id="term-feed-url" rows="5" size="40" style="white-space: pre"><?php echo esc_attr( $feed_url ); ?></textarea>
        <p class="description">The Feed URLs - one entry per line.</p>
    </td>
</tr>
<tr class="form-field">
    <th scope="row" valign="top"><label for="term-feed-name">Feed name</label></th>
    <td>
        <input name="<?php echo sprintf( '%s_feed_name', App::PREFIX ); ?>" id="term-feed-name" type="text" value="<?php echo esc_attr( $feed_name ); ?>" size="40"/>
        <p class="description">The Feed name (used to display source of RSS links).</p>
    </td>
</tr>
<input name="<?php echo sprintf('%s_interval', App::PREFIX); ?>" id="term-interval" style="visibility:hidden;" type="hidden" value="3hours" />
<tr class="form-field">
    <th scope="row" valign="top"><label for="term-delete-after">Feed entries presentation duration</label></th>
    <td>
        <select name="<?php echo sprintf( '%s_delete_after', App::PREFIX ); ?>" id="term-delete-after" class="postform">
            <option value="">Never</option>
            <?php
            foreach ( array(
                '1 day'    => 86400,
                '2 days'   => 2 * 86400,
                '3 days'   => 3 * 86400,
                '1 week'   => 7 * 86400,
                '2 weeks'  => 14 * 86400,
                '1 month'  => 30 * 86400,
                '3 months' => 90 * 86400
            ) as $k => $v ):
                ?>
                <?php echo '<option value="' . $v . '"' . ($v == $delete_after ? 'selected="selected"' : '') . '>' . $k . '</option>'; ?>
            <?php endforeach; ?>
        </select>
        <p class="description">Feed entries older than selected period of time will be deleted.</p>
    </td>
</tr>
<tr class="form-field">
    <th scope="row" valign="top">Always refresh links before fetching</th>
    <td>
        <label>
            <input type="hidden" name="<?php echo sprintf( '%s_always_refresh_links', App::PREFIX ); ?>" value="0" />
            <input type="checkbox" name="<?php echo sprintf( '%s_always_refresh_links', App::PREFIX ); ?>" id="fix_relative_paths" value="1" <?php checked( '1', $always_refresh_links ); ?> />
        </label>
        <p class="description">After checking this the links will be refreshed before fetching (so only the items in the feed will be displayed).</p>
    </td>
</tr>
<tr class="form-field">
    <th scope="row" valign="top"><label for="term-list">Lists</label></th>
    <td>
        <div class="cmra-form-field-checkboxes">
            <?php
            wp_list_categories( array(
                'hide_empty'    => FALSE,
                'hierarchical'  => FALSE,
                'style'         => 'none',
                'pad_counts'    => 0,
                'taxonomy'      => ListTaxonomy::TAXONOMY,
                'selected_cats' => $list_id_arr,
                'walker'        => new Walker_Category_Checklist()
            ) );
            ?>
        </div>
        <!--<p class="description"></p>-->
    </td>
</tr>
<tr class="form-field">
    <th scope="row" valign="top"><label for="term-show-favicons">Favicons before links</label></th>
    <td>
        <select name="<?php echo sprintf( '%s_show_favicons', App::PREFIX ); ?>" id="term-show-favicons" class="postform">
            <?php
            foreach ( array(
                0 => 'Use global settings',
                1 => 'Show',
                2 => 'Hide'
            ) as $k => $v ):
                ?>
                <?php echo '<option value="' . $k . '"' . ($k == $show_favicons ? 'selected="selected"' : '') . '>' . $v . '</option>'; ?>
            <?php endforeach; ?>
        </select>
        <p class="description">Use this option to override global settings.</p>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top"><label for="term-user-agent">User Agent</label></th>
    <td>
        <select name="<?php echo sprintf( '%s_user_agent', App::PREFIX ); ?>" id="term-user-agent" class="postform">
            <option value="">Use global settings</option>
            <?php
            foreach ( UserAgents::getAll() as $v => $k ):
                ?>
                <?php echo '<option value="' . esc_attr( $k ) . '"' . ($k == $user_agent ? 'selected="selected"' : '') . '>' . esc_attr( $v ) . '</option>'; ?>
            <?php endforeach; ?>
        </select>
        <p class="description">Used to fetch RSS feed.</p>
    </td>
</tr>

<tr class="form-field form-field-refresh">
    <th scope="row" valign="top">&nbsp;</th>
    <td>
        <label><input type="checkbox" name="<?php echo sprintf( '%s_refresh', App::PREFIX ); ?>" value="1" /> Refresh all RSS links</label>
        <p class="description">All RSS links from this category will be deleted and fetched again. Refreshing usually takes couple minutes.</p>
    </td>
</tr>
