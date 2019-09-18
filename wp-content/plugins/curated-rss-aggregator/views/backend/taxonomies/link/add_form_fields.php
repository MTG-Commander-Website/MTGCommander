<?php

use com\cminds\rssaggregator\App;
use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;
?>

<div class="form-field term-subtitle-wrap">
    <label for="term-subtitle">Subtitle</label>
    <input name="<?php echo sprintf('%s_subtitle', App::PREFIX); ?>" id="term-subtitle" type="text" value="" size="40" />
    <p>The subtitle is displayed under the name.</p>
</div>
<div class="form-field">
    <label for="term-url">URL</label>
    <input name="<?php echo sprintf('%s_url', App::PREFIX); ?>" id="term-url" type="url" value="" size="40"/>
    <p>The link is target address.</p>
</div>
<div class="form-field">
    <label for="term-image-url">Image URL</label>
    <input name="<?php echo sprintf('%s_image_url', App::PREFIX); ?>" id="term-image-url" type="url" value="" size="40" />
    <p>The image is displayed next to the name. Size can be change in plugin options.</p>
</div>
<div class="form-field form-required">
    <label for="term-category_id">Category</label>
    <?php
    wp_dropdown_categories(array(
        'show_option_none' => 'Select category',
        'name' => sprintf('%s_category', App::PREFIX),
        'id' => 'term-category_id',
        'taxonomy' => CategoryTaxonomy::TAXONOMY,
        'hide_empty' => FALSE,
        'hierarchical' => TRUE,
        'orderby' => NULL
    ));
    ?>
    <p>The category group your links.</p>
</div>
<div class="form-field">
    <label>
        <input type="checkbox" id="term-show-checkbox" onchange="jQuery(this).next().val(this.checked ? 1 : 0)" />
        <input type="hidden" id="term-show-checkbox-hidden" name="<?php echo sprintf('%s_show_checkbox', App::PREFIX); ?>" value="0" />
        Show checkbox
    </label>
    <p>The checkbox before link which allow users mark visited address.</p>
</div>
