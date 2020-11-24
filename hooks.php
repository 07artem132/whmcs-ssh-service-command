<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 23:40
 *
 */

use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers\LogController;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\ServiceButtonModel;

add_hook('AdminAreaHeadOutput', 99999999, function ($vars) {
    try {
        if (!isset($_GET['module']) || $_GET['module'] != ModuleConfig::getModuleName()) {
            return null;
        }
        foreach (scandir(ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/templates/css/admin') as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            echo '<link rel="stylesheet" type="text/css" href="/modules/addons/' . ModuleConfig::getModuleName() . '/templates/css/admin/' . $item . '">';
        }

        foreach (scandir(ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/templates/js/admin') as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            echo '<script type="text/javascript" charset="utf8" src="/modules/addons/' . ModuleConfig::getModuleName() . '/templates/js/admin/' . $item . '"></script>';
        }
    } catch (Exception $e) {
        LogController::addError('AdminAreaHeadOutput', json_encode($vars), $e);
    }
});

add_hook('PreModuleTerminate', 1, function ($vars) {
    $result = ServiceButtonModel::find($vars['params']['serviceid']);
    if ($result != null) {
        $result->delete();
    }
});

add_hook('ServiceDelete', 1, function ($vars) {
    $result = ServiceButtonModel::find($vars['serviceid']);
    if ($result != null) {
        $result->delete();
    }
});
