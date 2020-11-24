<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Abstracts;

use WHMCS\Module\Addon\SshRemoteServiceCommand\Configs\ModuleConfig;

abstract class ItemCheckboxConfigAbstracts extends ItemConfigAbstracts
{
    protected $type = 'checkbox';
    protected $val = null;

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'label' => $this->getLabel(),
            'class' => $this->getClass(),
            'type' => $this->getType(),
            'required' => $this->isRequired(),
            'val' => $this->getVal(),
        ];
    }
    public function setVal($val)
    {
        $this->val = $val;
        return $this;
    }

    public function getVal()
    {
        return $this->val;
    }

}