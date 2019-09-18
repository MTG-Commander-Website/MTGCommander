<?php

use com\cminds\rssaggregator\plugin\shortcodes\Shortcode;
?>

<article class="cm-shortcode-desc">
    <header>
        <h4>[<?php echo Shortcode::SHORTCODE; ?>]</h4>
        <span>Show List of Categories or Single Category</span>
    </header>
    <div class="cm-shortcode-desc-inner">
        <h5>Parameters</h5>
        <ul>
            <li><strong>list</strong> — slug or list of slugs of lists</li>
            <li><strong>category</strong> — slug or list of slugs of categories</li>
            <li><strong>tag</strong> —  is slug or list of slugs of tags</li>
        </ul>
        <h5>Usage</h5>
        <ol>
            <li>
                to display list use
                <code>[<?php echo Shortcode::SHORTCODE; ?>]</code> with <code>list</code>, <code>category</code> and <code>tag</code> attributes (you can use then at the same time to customize your list &mdash; they works like filters) where:
                <ul>
                    <li>
                        <code>list</code> is slug or list of slugs of <a href="edit-tags.php?taxonomy=cmra_list">lists</a>, e.g.: <code>[<?php echo Shortcode::SHORTCODE; ?> list=list1,list2,...]</code>,
                    </li>
                    <li>
                        <code>category</code> is slug or list of slugs of <a href="edit-tags.php?taxonomy=cmra_category">categories</a>, e.g.: <code>[<?php echo Shortcode::SHORTCODE; ?> category=category1,category2,...]</code>,
                    </li>
                    <li>
                        <code>tag</code> is slug or list of slugs of <a href="edit-tags.php?taxonomy=cmra_tag">tags</a>, e.g.: <code>[<?php echo Shortcode::SHORTCODE; ?> tag=tag1,tag2,...]</code>,
                    </li>
                </ul>
            </li>
            <li>
                to display single category (without search and filter on the top of the list) use
                <code>[<?php echo Shortcode::SHORTCODE; ?> category=category1]</code> where <code>category</code> attribute is slug of the <a href="edit-tags.php?taxonomy=cmra_category">category</a>.
            </li>
        </ol>
        <p>
            To overwrite global setting of max number of links use <code>max_links</code> attribute (set 0 to remove limit) e.g.: <code>[<?php echo Shortcode::SHORTCODE; ?> category=category1 max_links=10]</code>.
        </p>
    </div>
</article>