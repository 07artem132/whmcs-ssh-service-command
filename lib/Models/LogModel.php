<?php
namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Models;

use WHMCS\Model\AbstractModel;

class LogModel extends AbstractModel
{
    protected $table = "mod_addon_ssh_remote_service_command_log";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}