<?php


use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers\InstallController;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers\PageController;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers\UninstallController;
use WHMCS\Module\Addon\Setting;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Menu\AdminAreaMenu;

function SshRemoteServiceCommand_config()
{
    $configarray = [
        "name" => "ssh команды услуги для клиентов",
        "description" => "",
        "version" => "1",
        "author" => "service-voice",
        "fields" => [
            "DeleteTableWhenDisabled" => [
                "FriendlyName" => "Удалять данные модуля при отключении ?",
                "Type"         => "yesno",
                "Description"  => " Отметьте здесь дабы удалить данные модуля при отключении оного.",
            ]
        ]
    ];

    return $configarray;
}
function SshRemoteServiceCommand_activate()
{
    if (!empty($error = InstallController::createTableLog())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableDefaultButton())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableServiceButton())) {
        return $error;
    }
    if (!empty($error = InstallController::installServerModule())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно активирован',
    );
}

function SshRemoteServiceCommand_deactivate()
{
    if (!empty($dropTable = Setting::Module(ModuleConfig::getModuleName())->where('setting', '=', 'DeleteTableWhenDisabled')->first())) {
        if ($dropTable->value === 'on') {
            if (!empty($error = UninstallController::dropTable('mod_addon_ssh_remote_service_command_log'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_ssh_remote_service_command_default_button'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_ssh_remote_service_command_service_button'))) {
                return $error;
            }
        }
    }

    if (!empty($error = UninstallController::deleteServerModule())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно деактивирован'
    );
}

function SshRemoteServiceCommand_output($var)
{
    $PageController = new PageController($var);
    $PageController->setDefaultAction('index');
    $PageController->setSuffixTemplate('admin');
    $PageController->setMenuTemplate('include\navbar.tpl');
    $PageController->setBreadcrumbTemplate('include\breadcrumb.tpl');
    $PageController->setMenu((new AdminAreaMenu())->navbar());
    $PageController->run();
}

/*
 *
 *
 *         $ssh = new SSH2($ip);
        $key = new RSA();
        $key->loadKey($config[$userID]['instances'][$instance_id]['key']);

        if (!$ssh->login($login, $key)) {
            exit('Login Failed');
        }

        if (array_key_exists('update', $_REQUEST)) {

        } else {
            echo $ssh->exec('pwd');
        }
*/