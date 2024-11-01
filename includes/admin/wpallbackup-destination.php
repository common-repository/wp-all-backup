<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?><div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"><h3><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> <?php _e('Destination', 'wpallbkp'); ?></h3></div>
    </div>
    <div class="panel-body">
<?php include plugin_dir_path(__FILE__) . 'Destination/wp-backup-destination.php'; ?>
    </div>
    <div class="panel-footer">
<div role="alert" class="alert alert-success"><h4>Get Flat 35% off on <a target="_blank" href="https://www.wpseeds.com/product/wp-all-backup/">Pro WP All Backup Plus Plugin.</a> Use Coupon code 'WPALLBACKUP35'</h4></div>
    </div>
</div>
