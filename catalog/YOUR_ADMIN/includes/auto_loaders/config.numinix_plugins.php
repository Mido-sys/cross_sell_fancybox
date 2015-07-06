<?php
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
} 
  $autoLoadConfig[199][] = array('autoType'=>'class',
                               'loadFile'=>'numinix_plugins.php',
                               'classPath'=>DIR_WS_CLASSES);