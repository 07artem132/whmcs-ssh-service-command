<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Menu;

use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;
use WHMCS\View\Menu\MenuFactory;

class AdminAreaMenu extends MenuFactory
{
    protected $rootItemName = "custom nav bar";

    public function navbar()
    {
        return $this->loader->load($this->buildMenuStructure($this->getNavBarStructure()));
    }

    protected function getNavBarStructure()
    {
        $menuItems = [
            [
                "name" => "index",
                "label" => 'Список услуг',
                "uri" => ModuleConfig::getModuleLink() . "&action=index",
                "order" => 1,
                "attributes" => [
                    "class" => !array_key_exists('action', $_GET) || $_GET['action'] === 'index' ? 'active' : ''
                ]
            ],
            [
                "name" => "default_command",
                "label" => 'Стандартные команды',
                "uri" => ModuleConfig::getModuleLink() . "&action=default_command",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'default_command' ? 'active' : ''
                ]
            ],
            [
                "name" => "cron",
                "label" => 'Крон',
                "uri" => ModuleConfig::getModuleLink() . "&action=cron",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'cron' ? 'active' : ''
                ]
            ],
            [
                "name" => "log",
                "label" => 'Лог',
                "uri" => ModuleConfig::getModuleLink() . "&action=log",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'log' ? 'active' : ''
                ]
            ],
        ];

        return $menuItems;
    }

}