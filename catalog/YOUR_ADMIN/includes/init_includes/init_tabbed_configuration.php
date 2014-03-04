<?php
	if (!nmx_check_field(TABLE_CONFIGURATION, 'configuration_tab')) $db->Execute("ALTER TABLE " . TABLE_CONFIGURATION . " ADD configuration_tab varchar(32) NOT NULL DEFAULT 'General';");
	// delete installer to avoid duplicate installation
	unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_tabbed_configuration.php');