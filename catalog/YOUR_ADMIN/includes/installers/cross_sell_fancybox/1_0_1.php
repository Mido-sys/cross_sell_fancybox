<?php

$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.0.1' WHERE configuration_key = 'CSFB_VERSION' LIMIT 1;");
  
$messageStack->add('Installed Cross Sell Fancybox v1.0.1', 'success');