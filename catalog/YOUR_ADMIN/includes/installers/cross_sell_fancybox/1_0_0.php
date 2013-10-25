<?php
$configuration = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'Cross Sell Fancybox Configuration' ORDER BY configuration_group_id ASC;");
if ($configuration->RecordCount() > 0) {
  while (!$configuration->EOF) {
    $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = " . $configuration->fields['configuration_group_id'] . ";");
    $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = " . $configuration->fields['configuration_group_id'] . ";");
    $configuration->MoveNext();
  }
}
$db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = '';");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('Cross Sell Fancybox Configuration', 'Set Cross Sell Fancybox Options', '1', '1');");
$configuration_group_id = $db->Insert_ID();

$db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = " . $configuration_group_id . " WHERE configuration_group_id = " . $configuration_group_id . ";");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES
            (NULL, 'Version', 'CSFB_VERSION', '1.0.0', 'Version installed:', " . $configuration_group_id . ", 0, NOW(), NULL, NULL), 
            (NULL, 'Status', 'CSFB_STATUS', 'false', 'Enable Cross Sell Fancybox?', " . $configuration_group_id . ", 1, NOW(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
            (NULL, 'Cross Sell Link Selectors', 'CSFB_SELECTORS', '.centerBoxContentsCrossSell a', 'Define the selector(s) for the cross sold product links separated by commas (note: default example is for Cross Sell Advanced)', " . $configuration_group_id . ", 20, NOW(), NULL, NULL),
            (NULL, 'Cross Sell Load Selectors', 'CSFB_LOAD_SELECTORS', '#productGeneral', 'Define the id of the div that should load in the fancybox (note: you can surround your content with a new div and specify the id of that div here)', " . $configuration_group_id . ", 20, NOW(), NULL, NULL);");

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0
  // delete configuration menu
  $db->Execute("DELETE FROM admin_pages WHERE page_key = 'configCrossSellFancybox' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists('configCrossSellFancybox')) {
    $configuration = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'CSFB_VERSION' LIMIT 1;");
    $configuration_group_id = $configuration->fields['configuration_group_id'];
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page('configCrossSellFancybox',
                              'BOX_CONFIGURATION_CROSS_SELL_FANCYBOX', 
                              'FILENAME_CONFIGURATION',
                              'gID=' . $configuration_group_id, 
                              'configuration', 
                              'Y',
                              $configuration_group_id);
        
      $messageStack->add('Enabled Cross Sell Fancybox Configuration menu.', 'success');
    }
  }
}

$messageStack->add('Installed Cross Sell Fancybox v1.0.0', 'success');