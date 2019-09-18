<?php

use com\cminds\rssaggregator\plugin\taxonomies\CategoryTaxonomy;

?>
<form method="post" id="cmra-diagnostic-action1">

    <h3>Manual Processing</h3>

    <p>
        Select category to process and please be patient, this operation can take some time.
    </p>

    <?php wp_dropdown_categories(['taxonomy' => CategoryTaxonomy::TAXONOMY, 'hide_empty' => 0, 'show_option_none' => ' ', 'id' => 'cmra-diagnostic-action1-cat', 'orderby' => NULL]); ?>

    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cmra_diagnostic_action1'); ?>" />
    <input type="submit" name="submit" class="button button-primary" value="Process" id="cmra-diagnostic-action1-submit">

</form>
<script type="text/javascript">
    (function ($) {
        "use strict";
        $('#cmra-diagnostic-action1').on('submit', function () {
            $('#cmra-diagnostic-action1-submit').attr('disabled', 'disabled');
        });
    })(jQuery);
</script>
