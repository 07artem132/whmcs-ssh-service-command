<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\HtmlHelper;

use WHMCS\Module\Addon\SshRemoteServiceCommand\Abstracts\ItemTextConfigAbstracts;

class ItemTextHtmlHelper extends ItemTextConfigAbstracts
{
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