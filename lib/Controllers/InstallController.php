<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers;

use Illuminate\Database\Schema\Blueprint;
use WHMCS\Database\Capsule;
use Exception;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;

class InstallController
{
    public static function createTableLog()
    {
        try {
            $tbl_name = 'mod_addon_ssh_remote_service_command_log';
            if (!Capsule::schema()->hasTable($tbl_name)) {
                Capsule::schema()->create($tbl_name, function ($table) {
                    /** @var Blueprint $table */
                    $table->increments('id');
                    $table->boolean('status');
                    $table->string('module');
                    $table->text('message');
                    $table->timestamps();
                });
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('Ошибка при создании таблицы: %s , %s', $tbl_name, $e->getMessage())
            );
        }
        return [];
    }

    public static function createTableDefaultButton()
    {
        try {
            $tbl_name = 'mod_addon_ssh_remote_service_command_default_button';
            if (!Capsule::schema()->hasTable($tbl_name)) {
                Capsule::schema()->create($tbl_name, function ($table) {
                    /** @var Blueprint $table */
                    $table->increments('id');
                    $table->string('name');
                    $table->string('command');
                    $table->timestamps();
                });
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('Ошибка при создании таблицы: %s , %s', $tbl_name, $e->getMessage())
            );
        }
        return [];
    }
    public static function createTableServiceButton()
    {
        try {
            $tbl_name = 'mod_addon_ssh_remote_service_command_service_button';
            if (!Capsule::schema()->hasTable($tbl_name)) {
                Capsule::schema()->create($tbl_name, function ($table) {
                    /** @var Blueprint $table */
                    $table->integer('service_id');
                    $table->string('host');
                    $table->string('port');
                    $table->string('login');
                    $table->smallInteger('login_type');
                    $table->string('password');
                    $table->text('key');
                    $table->string('key_password');
                    $table->smallInteger('button_count');
                    $table->text('buttons');
                    $table->timestamps();
                    $table->primary('service_id','id_primary');
                });
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('Ошибка при создании таблицы: %s , %s', $tbl_name, $e->getMessage())
            );
        }
        return [];
    }

    public static function installServerModule()
    {
        $serverModulePath = ModuleConfig::getBaseFullPath() . '/serverModule';
        $targetPath = ModuleConfig::getWhmcsRootDir() . '/modules/servers/sshCommandRemoteServer';

        if (symlink($serverModulePath, $targetPath)) {
            return [];
        } else {
            return [
                'status' => 'error',
                'description' => 'При создании символической ссылки возникла ошибка. Цель: ' .
                    $serverModulePath . ' Ссылка:' . $targetPath
            ];
        }
    }
}