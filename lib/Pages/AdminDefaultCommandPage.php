<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Pages;

use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Interfaces\PageInterface;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\LogModel;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\ServiceButtonModel;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\ServiceDefaultModel;
use WHMCS\View\Menu\MenuFactory;

class AdminDefaultCommandPage implements PageInterface
{
    protected $templateName = 'admin_default_command.tpl';
    protected $vars = [];

    function __construct()
    {
       $this->vars['defaultButtons'] = ServiceDefaultModel::all();
    }

    function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * @return array
     */
    function getVars(): array
    {
        return $this->vars;
    }

    function getSubMenu(): ?MenuFactory
    {
        return null;
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): array
    {
        return [
            'Главная' => '',
        ];
    }
}