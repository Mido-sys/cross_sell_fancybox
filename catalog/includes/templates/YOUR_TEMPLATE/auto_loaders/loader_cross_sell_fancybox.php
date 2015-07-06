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

/**
 * NOTE: You can use php files for both javascript and css.
 *
 * Global variables must be declared global as they are referenced inside the loader class
 *
 * They must be coded like so:
 * Javascript:
 * <script language="javascript" type="text/javascript">
 * <?php // php code goes here ?>
 * </script>
 *
 * CSS:
 * <style type="text/css">
 * <?php // php code goes here ?>
 * </style>
 */
if (CSFB_STATUS == 'true') {
    $loaders[] = array('conditions' => array('pages' => array(FILENAME_PRODUCT_INFO)),
        'jscript_files' => array(
        '//code.jquery.com/jquery-1.11.3.min.js' => 1,
            'jquery/jquery.fancybox.js' => 2,
            'jquery/jquery_aatc_product_info.php' => 3,
            'jquery/jquery_cross_sell_fancybox.php' => 4
        ),
        'css_files' => array(
            'auto_loaders/jquery.fancybox.css' => 1
        )
    );
}