<?php
/*
 * Overall class for numinix premium/subscription plugins. Soemday we will encode this but until that point it'll just be visable
 */
class nxPluginLicCheck {
    
    // this get the information about the store
    function nxPluginLicCheck() {
        $this->server_domain = md5($_SERVER['SERVER_NAME']);
        $this->store_owner = STORE_OWNER;
        $this->admin_email = STORE_OWNER_EMAIL_ADDRESS;
        $this->store_phone = STORE_TELEPHONE_CUSTSERVICE;
        $this->store_name = STORE_NAME;
    }

    // this is where the plugin key from the init file is added to strt the process
    function getPluginKey($plugin_key) {
        $this->plugin_key = $plugin_key;
    }

    // this takes the plugin key and splits into it's need parts
    function getPluginFacts() {
        $plugin_key_array = explode(":", $this->plugin_key);
        $this->version_key = base64_decode($plugin_key_array[0]);
        $this->installer_folder = DIR_FS_ADMIN . 'includes/installers/' . base64_decode($plugin_key_array[1]);
        $this->enable = base64_decode($plugin_key_array[2]);
        $this->plugin_id = base64_decode($plugin_key_array[3]);
        $this->plugin_name = base64_decode($plugin_key_array[4]);
        $this->license_key = str_replace("_VERSION", "", $this->version_key) . '_KEY';
        $this->installers = scandir($this->installer_folder, 1);
        $this->files_version = substr($this->installers[0], 0, -4);
        sort($this->installers);
        if (defined($this->version_key)) {
            $this->installed_version = constant($this->version_key);
        }
    }

    // this pulls the current lic "on file"
    function pullLocalLic() {
        // no license can't pull it!
        if (!defined($this->license_key)) {
            $this->current_key_defined = 0;
            return false;
        }
        // license but it's empty that doesn't do any good now does it?
        if (constant($this->license_key) == '') {
            $this->current_key_defined = 0;
            return false;
        }
        // ohh it does exist lets let every one know
        $this->current_key_defined = 1;
        // the key should look kinda like this
        //[EXPIRES: UNIX TIME]:[Domain]:[KILL SWITCH ACTIVE]:[MAX_VERSION]:[RENEW DATE]
        //lets grab it and split it into it's parts
        $current_lic_array = explode(':', constant($this->license_key));
        $this->loc_expires = base64_decode($current_lic_array[0]);
        $this->loc_domain = $current_lic_array[1];
        $this->loc_kill_active = base64_decode($current_lic_array[2]);
        $this->loc_max_version = base64_decode($current_lic_array[3]);
        $this->loc_renew_date = base64_decode($current_lic_array[4]);
        return true;
    }

    // here we are gettign the json payload to use to request the license from numinix.com
    function createJsonData() {
        $this->json_data = array(
            'verison_key' => $this->version_key,
            'plugin_id' => $this->plugin_id,
            'files_version' => $this->files_verison,
            'installed_version' => $this->installed_version,
            'domain' => $_SERVER['SERVER_NAME'],
            'store_owner' => $this->store_owner,
            'admin_email' => $this->admin_email,
            'customer_phone' => $this->store_phone,
            'store_name' => $this->store_name,
            'cyper' => md5('numinix'),
            'current_key_defined' => $this->current_key_defined,
        );
    }

    // Now it's time to check with the mother ship if there is a valid plugin license
    function pullRemoteLic() {
        $this->createJsonData();
        $url = 'https://www.numinix.com/module_license/check.php';
        $ch = curl_init($url);
        $jsonDataEncoded = json_encode($this->json_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $raw_result = curl_exec($ch);
        $this->json_result = json_decode($raw_result);
        $this->addJsonResultVars();
        $this->saveNewLicKey();
    }

    // ok so now we got the respoinse let's split this up into some usable variables
    function addJsonResultVars() {
        $result = $this->json_result;
        $this->max_version_allowed = $result->max_version;
        $this->expires_date = $result->expires;
        $this->renew_date = $result->renew_date;
        $this->disable_active = $result->disable_active;
        $this->new_lic_key = $result->license_key;
    }

    // someone has been naughty...lets disable the plugin....
    function disablePlugin() {
        global $db;
        if (constant($this->enable) == 'true') {
            $db->Execute('UPDATE ' . TABLE_CONFIGURATION . " SET configuration_value='false' WHERE configuration_key='" . $this->enable . "'");
            $this->showMessage('This module has been disabled becuase You don\'t have an active/valid license for ', '427', 'error');
        }
    }

    // the license isn't valid...
    function pluginLicInvalid() {
        $this->showMessage('Please contact <a href="numinix.com">numinix.com</a> there is an error when verifing your license', '458', 'error');
    }

    // plugin license expired...maybe you should have renewed
    function pluginExpired() {
        $this->disablePlugin();
        $this->showMessage('Your License Has Expired Please Contact <a href="numinix.com">numinix.com</a>', '447', 'error');
    }

    // ok...your tryign to pull a fast one...but guess what changing domains doesn't mean you can use the same license...
    function domainMismatched() {
        $this->disablePlugin();
        $this->showMessage('Please contact <a href="numinix.com">numinix.com</a> there is an error when verifing your license', '477', 'error');
    }

    // wait this verison is newer than what you should have...where'd you get these files from? huh?
    function versionNotAllowed() {
        $this->showMessage('Please contact <a href="numinix.com">numinix.com</a> there is an error when verifing your license', '457', 'error');
    }

    // We don't have a change oil light in zencart, so lets let them know it's time to renew.
    function warnExpires() {
        if (strtotime(date()+"+1 week") > strtotime($this->loc_expires) && strtotime($this->loc_expires) > date()) {
            if ($this->loc_kill_active == '1') {
                $this->showMessage($this->plugin_name . ' is about to expire, please renew or setup automatic renewing before this module is disabled.', '', 'caution');
            } else {
                $this->showMessage($this->plugin_name . ' is about to expire, renew now to get future updates', '', 'caution');
            }
            $this->pullRemoteLic();
        }
    }

    // just because it's easier to show the messgaes with a function, let's cheat and use this
    function showMessage($message = 'Please contact <a href="numinix.com">numinix.com</a> there is an error when verifing your license ', $code = '', $level = 'warning') {
        global $messageStack;
        if (strpos($_SERVER["SCRIPT_NAME"], 'configuration') == false && $code != '458') {
            return false;
        }
        $complete_message = $message . ' for <b>' . $this->plugin_name . '</b>';
        if ($code != '') {
            $complete_message .= ' Code: ' . $code;
        }
        $messageStack->add($complete_message, $level);
    }

    // ok you have passed the tests, now it's actually, finally time to install.
    function runInstaller() {
        global $db, $messageStack;
        $configuration_group_id = '';
        if (defined($this->version_key)) {
            $current_version = constant($this->version_key);
        } else {
            $current_version = "0.0.0";
            $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('" . $this->plugin_name . "', 'Set " . $module_name . " Options', '1', '1');");
            $configuration_group_id = $db->Insert_ID();
            $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = " . $configuration_group_id . " WHERE configuration_group_id = " . $configuration_group_id . ";");
            $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES
                    ('Version', '" . $this->version_key . "', '0.0.0', 'Version installed:', " . $configuration_group_id . ", 0, NOW(), NOW(), NULL, NULL);");
        }
        if ($configuration_group_id == '') {
            $config = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key= '" . $this->version_key . "'");
            $configuration_group_id = $config->fields['configuration_group_id'];
        }
        if (version_compare($this->files_version, $current_version) > 0) {
            foreach ($this->installers as $installer) {
                $installer_version = substr($installer, 0, -4);
                if (version_compare($this->files_version, $installer_version) >= 0 && version_compare($current_version, $installer_version) < 0) {
                    include($this->installer_folder . '/' . $installer);
                    $current_version = str_replace("_", ".", $installer_version);
                    $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . $current_version . "' WHERE configuration_key = '" . $this->version_key . "' LIMIT 1;");
                    $messageStack->add("Installed " . $this->plugin_name . " v" . $current_version, 'success');
                }
            }
        }
        $db->Execute("UPDATE ".TABLE_CONFIGURATION." SET configuration_group_id='".$configuration_group_id."' WHERE configuration_key='".$this->license_key."'");
    }

    // well we got a new license key from the mother ship so lets keep it safe onboard
    function saveNewLicKey() {
        global $db;
        if(!defined($this->license_key)){
            $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, sort_order, last_modified, date_added, use_function, set_function) VALUES
                    ('Module License Key', '" . $this->license_key . "', '" . $this->new_lic_key . "', 'License Key this will need to be renewed with your subscription', 0, NOW(), NOW(), 'zen_cfg_password_display', 'zen_cfg_password_input(');");
        }
        else{
        $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value= '" . $this->new_lic_key . "' WHERE configuration_key='".$this->license_key."';");
        }
    }
    
    // is the lic that is here valid?
    function checkLicValid() {
        if ($this->loc_max_version == '0.0.0') {
            $this->pluginLicInvalid();
            return false;
        }
        //expired check
        if (strtotime($this->loc_expires) < time()) {
            $this->pluginExpired();
            return false;
        }
        // domain incorrect check
        if ($this->server_domain != $this->loc_domain) {
            $this->domainMismatched();
            return false;
        }
        // files too new check
        if (version_compare($this->files_version, $this->loc_max_version) > 0) {
            $this->versionNotAllowed();
            return false;
        }
        // ok permission to proceed
        return true;
    }

    // This is the function that does all the other function stuff, otherwise the init installer might look a little...ok alot messy
    function nxPluginLicense($module_license) {
        // load plugin key
        $this->getPluginKey($module_license);
        // get all the info we need
        $this->getPluginFacts();
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $this->runInstaller();
            return;
        }
        // lets hope not everything is false 
        switch (false) {
            //can't get local license...lets call and see where it's at
            case $this->pullLocalLic():
                $this->pullRemoteLic();
                break;
            // humm hte license isn't valid maybe we should go and check again.
            case $this->checkLicValid():
                $this->pullRemoteLic();
                break;
            // ok it pased the test, lets let them know if it's going to expire...and actually do what we are supose to do and install the module
            default:
                $this->warnExpires();
                $this->runInstaller();
        }
    }

}
