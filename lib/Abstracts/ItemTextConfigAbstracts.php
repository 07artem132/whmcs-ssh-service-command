<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Abstracts;


abstract class ItemTextConfigAbstracts extends ItemConfigAbstracts
{
    protected $required = false;
    protected $type = 'text';

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
}