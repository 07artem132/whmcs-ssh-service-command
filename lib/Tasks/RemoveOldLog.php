<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Tasks;

use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\LogModel;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Interfaces\TaskInterfaces;

class RemoveOldLog implements TaskInterfaces
{
    private $frequency = '0 * * * *';

    public $name = 'remove old log';

    function __construct()
    {
    }

    function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFrequency(): string
    {
        return $this->frequency;
    }

    function run(): void
    {
        $saveLast = 10;
        $count = LogModel::count();
        if ($count > $saveLast) {
            $logs = LogModel::skip($saveLast)->orderBy('id', 'desc')->limit(1000)->get();
            foreach ($logs as $log) {
                $log->delete();
            }
        }
    }
}