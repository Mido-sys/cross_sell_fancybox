<?php
// this file is ONLY used by the cross sell 
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');

if (isset($_GET['products_id'])) {
	$_GET['main_page'] = zen_get_info_page($_GET['products_id']);
	$_SESSION['navigation']->set_snapshot();
}
require_once('includes/application_bottom.php');
?>