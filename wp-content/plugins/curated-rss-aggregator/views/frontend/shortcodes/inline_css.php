<?php

use com\cminds\rssaggregator\plugin\options\Options;
?>

<?php if (Options::getOption('tooltip_text_color')): ?>
    .style-cmra-tooltip.opentip-container .opentip { color: <?php echo Options::getOption('tooltip_text_color'); ?> !important; }
<?php endif; ?>

<?php if (Options::getOption('category_font_size')): ?>
    .cmra .cmra-header { font-size: <?php echo Options::getOption('category_font_size'); ?> !important; }
<?php endif; ?>
<?php if (Options::getOption('category_background_color')): ?>
    .cmra .cmra-header { background: <?php echo Options::getOption('category_background_color'); ?> !important; padding-left:5px; }
<?php endif; ?>
<?php if (Options::getOption('category_text_color')): ?>
    .cmra .cmra-header { color: <?php echo Options::getOption('category_text_color'); ?> !important; }
<?php endif; ?>

<?php if (Options::getOption('link_font_size')): ?>
    .cmra .cmra-link { font-size: <?php echo Options::getOption('link_font_size'); ?> !important; }
    .cmra .cmra-link-checkbox { font-size: <?php echo Options::getOption('link_font_size'); ?> !important; }
<?php endif; ?>
<?php if (Options::getOption('link_subtitle_font_size')): ?>
    .cmra .cmra-link-subtitle { font-size: <?php echo Options::getOption('link_subtitle_font_size'); ?> !important; }
<?php endif; ?>
<?php if (Options::getOption('link_hover_color')): ?>
    .cmra .cmra-category-link-list-entry:hover { background: <?php echo Options::getOption('link_hover_color'); ?> !important; }
<?php endif; ?>
<?php if (Options::getOption('link_image_height')): ?>
    .cmra .cmra-link-image{ height: <?php echo Options::getOption('link_image_height'); ?> !important; }
<?php endif; ?>
<?php if (Options::getOption('link_image_width')): ?>
    .cmra .cmra-link-image{ width: <?php echo Options::getOption('link_image_width'); ?> !important; }
<?php endif; ?>
    