<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
$notifier_file_url = NOTIFIER_XML_FILE;

function wp_db_backup_format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

if (function_exists('curl_init')) { // if cURL is available, use it...
    $ch = curl_init($notifier_file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $notifier_data = curl_exec($ch);
    curl_close($ch);
} else {
    @$notifier_data = file_get_contents($notifier_file_url); // ...if not, use the common file_get_contents()
}
if (strpos((string) $notifier_data, '<notifier>') === false) {
    $notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog>';
}

// Load the remote XML data into a variable and return it
if (!empty($xml)) {
    @$xml = simplexml_load_string($notifier_data);
    if (WPALLBK_VERSION == $xml->latest) {
        $alert = '<strong>No Alert</strong><br/>';
        $changelog = '';
        $changelogMsg = "<strong>No Message</strong>";
    } else {
        @$alert = '<strong><a href="http://www.wpseeds.com/blog/category/update/" title="Change Log" target="_blank">Plugin Updates</a></strong><br/>
                <strong>There is a new version of the <br/>WP ALL Backup Plus plugin available.</strong>
                 You have version ' . WPALLBK_VERSION . ' Update to version ' . $xml->latest . '.';
        @$changelog = $xml->changelog;
        @$changelogMsg = '<strong>New Version Availabel</strong>
                <a href="http://www.wpseeds.com/member" target="_blank">Download Version' . $xml->latest . ' from Member area</a>';
        echo '<style>.glyphicon.glyphicon-bell {
                    color: red !important;
                }</style>';
    }
} else {
    $alert = '<strong>No Alert</strong><br/>';
    $changelog = '';
    $changelogMsg = "<strong>No Message</strong>";
}
?>
<?php if (isset($_GET['notification'])) { ?>
    <div class="row">
        <div class="col-md-offset-4 col-xs-8 col-sm-8 col-md-8">
            <?php if ($_GET['notification'] == 'create') {
                    $BackupList = get_option('wp_all_backup_backups');
 		                $downloadBackup = end($BackupList);
 		                $backupLink = '<a href="' . $downloadBackup['url'] . '" style="color: #21759B;">'. __('Click Here to Download Backup.', 'wpdbbkp').'</a>';
                 ?>
                <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                  <?php _e('Backup created Successfully. '.$backupLink, 'wpallbkp'); ?>
              </div>
            <?php } ?>
            <?php if ($_GET['notification'] == 'restore') { ?>
              <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
              <?php _e('Backup Restore Successfully', 'wpallbkp'); ?>
            </div>
            <?php } ?>
            <?php if ($_GET['notification'] == 'delete') { ?>
              <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
              <?php _e('Backup deleted Successfully', 'wpallbkp'); ?>
            </div>
            <?php } ?>
        </div>
    </div>
<?php
} $upload_dir = wp_upload_dir();
$dir = $upload_dir['basedir'] . '/' . WPALLBK_BACKUPS_DIR;
if (!is_dir($dir)) {
    $dir = $upload_dir['basedir'];
}
if (is_dir($dir) && !is_writable($dir)) {
    ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span></button> <h4>WP All Backup Plus</h4>
                <p>Error: Permission denied, make sure you have write permission for <?php echo $dir ?> folder</p>  </div>
            </button>
        </div>
    </div><?php } ?>
<div class="row">

  <div class="col-xs-8 col-md-6 col-sm-4">
    <div id="backup_process" style="display:none">
      <img width="50" height="50" src="<?php echo WPALLBK_PLUGIN_URL . '/assets/images/icon_loading.gif'; ?>">
    </div>
  </div>
    <div class="col-xs-4 col-sm-4 col-md-6 text-right">

        <!-- Single button -->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle label-success" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <li class="list-group-item">WP Seeds Released New <a href="http://www.wpseeds.com/product/profile-pro/" target="_blank">Profile Pro</a>plugin.Use Coupon code <strong>'WPSEEDS25'</strong> and Get Flat 25% off on All WPSeeds Plugin and Themes.</li>
            </ul>
        </div>
        <!-- Single button -->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-bell" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu list-group pull-right">
                <li  class="list-group-item "><?php _e($alert, 'wpallbkp'); ?></li>
                <?php if (!empty($changelog)) { ?>
                    <li  class="list-group-item "><?php _e($changelog, 'wpallbkp'); ?></li>
<?php } ?>
            </ul>
        </div>

        <!-- Single button Setting-->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#" >
                        <p><?php _e('Backup Type :', 'wpallbkp'); ?>
                            <?php
                            _e(ucfirst(get_option('wp_all_backup_type')), 'wpallbkp');
                            ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="#" >
                        <p><?php _e('Schedule :', 'wpallbkp'); ?>
                            <?php
                            $settings = get_option('wp_all_backup_options');
                            if (isset($settings['enable_autobackups']) && $settings['enable_autobackups'] == '1') {
                                _e('Enabled -', 'wpallbkp');
                                _e(ucfirst($settings['autobackup_frequency']), 'wpallbkp');
                            } else {
                                _e('Disabled', 'wpallbkp');
                            }
                            ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li >
                    <a href="#" >
                        <p><?php _e('Exclude Folders and files ', 'wpallbkp'); ?></p>
<?php echo get_option('wp_all_backup_exclude_dir'); ?></p>

                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li><a href="#">
                        <a href="#" >
                            <p><?php _e('Keep No of backup :', 'wpallbkp'); ?>
                                <?php
                                if (get_option('wp_all_backup_max_backups') == '0') {
                                    _e('Unlimited', 'wpallbkp');
                                } else {
                                    echo get_option('wp_all_backup_max_backups');
                                }
                                ?></p>
                        </a>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="#" >
                        <p><?php _e('Backup Log :', 'wpallbkp'); ?>
                            <?php
                            if (get_option('wp_all_backup_enable_log') == '1') {
                                _e('Enabled', 'wpallbkp');
                            } else {
                                _e('Disabled', 'wpallbkp');
                            }
                            ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?php echo site_url() ?>/wp-admin/admin.php?page=wpallbackup-settings" title="<?php _e('Change Setting', 'wpallbkp'); ?>"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?php _e('Change Setting', 'wpallbkp'); ?></a>
                </li>
            </ul>
        </div>

        <!-- Single button Author-->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="https://walkeprashant.wordpress.com/about-me/" target="_blank" >
                        <h5 ><?php _e('Plugin Author', 'wpallbkp'); ?></h5>
                        <p><?php _e('Prashant Walke', 'wpallbkp'); ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li >
                    <a href="http://www.wpseeds.com/wp-all-backup/" target="_blank" >
                        <h5 ><?php _e('Plugin URL', 'wpallbkp'); ?></h5>
                    </a>
                </li>
                <li >
                    <a href="http://www.wpseeds.com/blog/category/update/wp-all-backup/" target="_blank" >
                        <h5 ><?php _e('Change Log', 'wpallbkp'); ?> </h5>
                    </a>
                </li>
                <li >
                    <a href="http://www.wpseeds.com/wp-all-backup/" target="_blank" >
                        <h5 ><?php _e('Documentation', 'wpallbkp'); ?></h5>
                    </a>
                </li>
                <li >
                    <a href="http://www.wpseeds.com/support/" target="_blank" >
                        <h5 ><?php _e('Support', 'wpallbkp'); ?></h5>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>
<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"><h3><span class="glyphicon glyphicon-list" aria-hidden="true"></span> Backups</h3></div>
    </div>
    <div class="panel-body">
        <div>

            <h3><a title="Create New Backup" href="<?php echo site_url() ?>/wp-admin/admin.php?page=wpallbackup-listing&action=create" id="create_backup" class="btn btn-success"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Create New Backup</a></h3></a>
            <a href="<?php echo site_url() ?>/wp-admin/admin.php?page=wpallbackup-settings" title="Setting"><span class="glyphicon glyphicon-cog" aria-hidden="true">Setting</span></a>
            <a href="<?php echo site_url() ?>/wp-admin/admin.php?page=wpallbackup-destination" title="Destination"><span class="glyphicon glyphicon-upload" aria-hidden="true">Destination</span></a>
            <a href="<?php echo site_url() ?>/wp-admin/admin.php?page=wpallbackup-help" title="How to create backup" target="_blank"><span class=" glyphicon glyphicon-question-sign" aria-hidden="true">Help</span></a>


        </div>
        <div><hr>
            <?php
            $options = get_option('wp_all_backup_backups');
            if ($options) {

                echo ' <div class="table-responsive">
                                <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline" role="grid">

                                <table class="table table-striped table-bordered table-hover display" id="example">
                                    <thead>';
                echo '<tr class="wpdb-header">';
                echo '<th class="manage-column" scope="col" width="10%" style="text-align: center;">SL No</th>';
                echo '<th class="manage-column" scope="col" width="25%">Date</th>';
                echo '<th class="manage-column" scope="col" width="5%">Destination</th>';
                echo '<th class="manage-column" scope="col" width="10%">Type</th>';
                echo '<th class="manage-column" scope="col" style="text-align: center; width="5%">Log</th>';
                echo '<th class="manage-column" scope="col" width="15%">Backup File</th>';
                echo '<th class="manage-column" scope="col" width="10%">Size</th>';
                echo '<th class="manage-column" scope="col" width="10%"></th>';
                echo '<th class="manage-column" scope="col" width="10%"></th>';
                echo '</tr>';
                echo '</thead>';

                echo '<tbody>';
                $count = 1;
                $destination_icon = array(
                    'Local'=>'glyphicon glyphicon-home',
                    'FTP'=>'glyphicon glyphicon-folder-open',
                    'S3'=>'glyphicon glyphicon-cloud-upload',
                    'Drive'=>'glyphicon glyphicon-hdd',
                    'DropBox'=>'glyphicon glyphicon-inbox'
                );
                foreach ($options as $option) {
                    $strClass = ( 0 == $option['size'] ) ? 'text-danger' : 'wpall_download';
                    echo '<tr class="' . ((($count % 2) == 0) ? $strClass .' alternate' : $strClass) . '">';
                    echo '<td style="text-align: center;">' . $count . '</td>';
                    echo '<td><span style="display:none">' . date('Y M jS h:i:s A', $option['date']) .'</span>' . date('jS, F Y', $option['date']) . '<br />' . date('h:i:s A', $option['date']) . '</td>';
                    echo '<td>';
                    if (!empty($option['destination'])) {
                        $destination=(explode(",",$option['destination']));
                        foreach ($destination as $dest) {
                            $key = trim($dest);
                            if (!empty($dest) && array_key_exists($key,$destination_icon)) {
                                echo '<span class="' . $destination_icon[$key] . '" title="' . $dest . '"></span> ';
                            }
                        }
                    }
                    echo '</td>';
                    echo '<td>';
                    if (!empty($option['type'])) {
                        echo ucwords($option['type']);
                    }
                    echo '</td>';
                    echo '<td>';
                    if (!empty($option['logfile'])) {
                        echo '<a href="' . $option['logfile'] . '" target="_blank" style="color: #21759B;" title="Log File">';
                        echo '<span class="glyphicon glyphicon-file"></span></a>';
                    }
                    echo '</td>';
                    echo '<td>';
                    echo '<a href="' . $option['url'] . '" style="color: #21759B;" target="_blank" title="Backup File">';
                    echo '<span class="glyphicon glyphicon-download-alt"></span> Download</a></td>';
                    echo '<td>' . wp_db_backup_format_bytes($option['size']) . '</td>';
                    echo '<td><a href="' . site_url() . '/wp-admin/admin.php?page=wpallbackup-listing&action=removebackup&index=' . ($count - 1) . '" class="button-secondary"><span style="color:red" class="glyphicon glyphicon-remove"></span>Remove<a/></td>';
                    echo '<td><a href="' . site_url() . '/wp-admin/admin.php?page=wpallbackup-listing&action=restorebackup&index=' . ($count - 1) . '" class="button-secondary"><span class="glyphicon glyphicon-refresh" style="color:blue"></span>Restore<a/></td>';
                    echo '</tr>';
                    $count++;
                }
                echo '</tbody>';

                echo ' </table>
                                </div>
                                  </div>';
            } else {
                echo '<p>No Backups Created!</p>';
            }

            echo '</div>'
            ;
            ?>
        </div>

        <div class="panel-footer"><div role="alert" class="alert alert-success"><h4>Get Flat 35% off on <a target="_blank" href="https://www.wpseeds.com/product/wp-all-backup/">Pro WP All Backup Plus Plugin.</a> Use Coupon code 'WPALLBACKUP35'</h4></div></div>
    </div>
    <script>
        var $jq = jQuery.noConflict();
        $jq(document).ready(function () {
            $jq('.popoverid').popover();
            var table = $jq('#example').DataTable();
            $jq("#create_backup").click(function() {
            $jq('#backup_process').show(); // or you want .toggle() ?
    });
        });

    </script>
