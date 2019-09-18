<?php

use com\cminds\rssaggregator\App;
?>
<style type="text/css">
    .cminds-plugin-error,
    .cminds-plugin-error code{
        font-weight: normal;
        background: #F00 !important; 
        color: #FFF !important; 
    }
    .cminds-plugin-error code{
        font-weight: bold;
    }
</style>
<div>
    <span class="cminds-plugin-error"><?php echo App::PLUGIN_NAME; ?>: <?php echo $message; ?></span>
</div>