<?php

namespace WHMCS\Module\Addon\SshRemoteServiceCommand\Models;

use WHMCS\Database\Capsule;
use WHMCS\Domain\Domain;
use WHMCS\Model\AbstractModel;
use WHMCS\Product\Addon;
use WHMCS\Product\Group;
use WHMCS\Product\Product;
use WHMCS\Service\Service;
use WHMCS\User\Client;

class ServiceButtonModel extends AbstractModel
{
    protected $table = "mod_addon_ssh_remote_service_command_service_button";
    protected $primaryKey = 'service_id';
    public $incrementing = true;
    protected $fillable = [
        'service_id'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getClientNameAttribute(): string
    {
        $client = Client::findOrFail($this->client_id);
        return $client->firstname . ' ' . $client->lastname;
    }

    public function getProductNameAttribute(): string
    {
        try {
            $rel_id = Service::findOrFail($this->service_id)->packageid;
            $product = Product::findOrFail($rel_id);
            return Group::find($product->gid)->name . '\\' . $product->name;
        } catch (\Throwable $e) {
            return 'Вероятно удален продукт';
        }
    }

    public function getServiceStatusAttribute(): string
    {
        try {
            return Service::findOrFail($this->service_id)->domainstatus;
        } catch (\Throwable $e) {
            return 'Вероятно удален продукт';
        }
    }

    public function getServiceExpireAttribute(): string
    {
        try {
            return Service::findOrFail($this->service_id)->nextduedate;
        } catch (\Throwable $e) {
            return 'Вероятно удален продукт';
        }
    }

    public function getServiceUrlAttribute(): string
    {
        return 'clientsservices.php?productselect=' . $this->service_id;
    }

    public function getClientIdAttribute(): int
    {
        return (int)Service::findOrFail($this->service_id)->userid;
    }

    public function getButtonsAttribute(): array
    {
        $buttons = explode(PHP_EOL, $this->attributes['buttons']);
        for ($i = 0; $i < count($buttons); $i++) {
            if ($buttons[$i] == "") {
                unset($buttons[$i]);
                continue;
            }
            $result = explode(';', $buttons[$i]);
            $buttons[$i] = ['name' => base64_decode($result[0]), 'action' => base64_decode($result[1])];
        }
        return $buttons;
    }

    public function setButtonsAttribute($value): void
    {
        $this->attributes['buttons'] = '';
        for ($i = 1; $i <= intval($this->attributes['button_count']); $i++) {
            if (array_key_exists('button_' . $i . '_name', $value))
                $this->attributes['buttons'] .= base64_encode($value['button_' . $i . '_name']) . ';' . base64_encode($value['button_' . $i . '_action']) . PHP_EOL;
        }
    }

}