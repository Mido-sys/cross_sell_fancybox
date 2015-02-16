<?php

/*
 * 
 * @package Cross Sell Fancy Box
 * @copyright Numinix Web Development
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * 
 */
global $sniffer;
if (!$sniffer->field_exists(TABLE_CONFIGURATION, 'configuration_tab'))
    $db->Execute("ALTER TABLE " . TABLE_CONFIGURATION . " ADD configuration_tab varchar(32) NOT NULL DEFAULT 'General';");

// delete installer to avoid duplicate installation
unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_tabbed_configuration.php');
