<?php

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
// add upgrade script
$cross_sell_fancybox_version = (defined('CSFB_VERSION') ? CSFB_VERSION : 'new');
$current_version = '1.0.0';
while ($cross_sell_fancybox_version != $current_version) {
    switch ($cross_sell_fancybox_version) {
        case 'new':
            // perform upgrade
            if (file_exists(DIR_WS_INCLUDES . 'installers/cross_sell_fancybox/1_0_0.php')) {
                include_once(DIR_WS_INCLUDES . 'installers/cross_sell_fancybox/1_0_0.php');
                $cross_sell_fancybox_version = '1.0.0';
                break;
            } else {
                break 2;
            }
        case '1.0.0':
            // perform upgrade
            if (file_exists(DIR_WS_INCLUDES . 'installers/cross_sell_fancybox/1_0_1.php')) {
                include_once(DIR_WS_INCLUDES . 'installers/cross_sell_fancybox/1_0_1.php');
                $cross_sell_fancybox_version = '1.0.1';
                break;
            } else {
                break 2;
            }
        case '1.0.1':
            // perform upgrade
            if (file_exists(DIR_WS_INCLUDES . 'installers/cross_sell_fancybox/1_0_2.php')) {
                include_once(DIR_WS_INCLUDES . 'installers/cross_sell_fancybox/1_0_2.php');
                $cross_sell_fancybox_version = '1.0.2';
                break;
            } else {
                break 2;
            }
        default:
            $cross_sell_fancybox_version = $current_version;
            // break all the loops
            break 2;
    }
}