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

$autoLoadConfig[999][] = array(
    'autoType' => 'init_script',
    'loadFile' => 'init_cross_sell_fancybox_config.php'
);
