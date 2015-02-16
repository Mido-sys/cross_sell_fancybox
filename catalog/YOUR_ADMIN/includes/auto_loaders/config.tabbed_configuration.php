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
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_tabbed_configuration.php')) {
    $autoLoadConfig[999][] = array(
        'autoType' => 'init_script',
        'loadFile' => 'init_tabbed_configuration.php'
    );
}