<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Configs;

class SmartyConfig
{
    public static function GetTemplateDir()
    {
        return ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/templates/';
    }

    public static function GetCompileDir()
    {
        global $templates_compiledir;

        return $templates_compiledir;
    }

}