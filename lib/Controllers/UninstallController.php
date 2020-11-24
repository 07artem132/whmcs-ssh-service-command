<?php


namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;

class UninstallController
{
    public static function deleteServerModule()
    {
        $serverModulePath = ModuleConfig::getBaseFullPath() . '/serverModule';
        $targetPath = ModuleConfig::getWhmcsRootDir() . '/modules/servers/sshCommandRemoteServer';

        if (unlink($targetPath)) {
            return [];
        } else {
            return [
                'status' => 'error',
                'description' => 'При удалении символической ссылки возникла ошибка. Цель: ' .
                    $serverModulePath . ' Ссылка:' . $targetPath
            ];
        }
    }

    public static function dropTable($tableName)
    {
        try {
            Capsule::schema()->dropIfExists($tableName);
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf("Ошибка при удалении таблицы %s: %s", $tableName, $e->getMessage())
            );
        }

        return [];
    }
}