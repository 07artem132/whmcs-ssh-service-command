<?php

use WHMCS\Module\Addon\SshRemoteServiceCommand\Controllers\LogController;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\ServiceButtonModel;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use WHMCS\Module\Addon\SshRemoteServiceCommand\Models\ServiceDefaultModel;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function sshCommandRemoteServer_MetaData()
{
    return array(
        'DisplayName' => 'ssh command remote server',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => false, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '10011', // Default Non-SSL Connection Port
    );
}

function generateButtons($model, $prefix, $callback)
{
    $buttons = [];
    $button_index = 0;
    foreach ($model['buttons'] as $button) {
        $functionName = 'sshCommandRemoteServer_ssh_run_' . $prefix . $button_index;
        $eval = 'function ' . $functionName . ' (array $params){ return ' . $callback . '($params,' . $button_index . ');}';
        if (!array_key_exists($button['name'], $buttons)) {
            $buttons[$button['name']] = 'ssh_run_' . $prefix . $button_index;
            if (!function_exists($functionName))
                eval($eval);
            $button_index++;
            continue;
        }
        $i = 1;
        while (true) {
            if (!array_key_exists($i . ' ' . $button['name'], $buttons)) {
                $buttons[$i . ' ' . $button['name']] = 'ssh_run_' . $prefix . $button_index;
                if (!function_exists($functionName))
                    eval($eval);
                $button_index++;
                break;
            } else
                ++$i;
        }
    }
    return $buttons;
}

function run_ssh_comand($command, $model, $detal = false)
{
    $ssh = new SSH2($model->host);


    if ($model->login_type === 0)
        if (!$ssh->login($model->login, $model->password)) {
            LogController::addError(__FUNCTION__, 'service_id->' . $model->service_id . ' ' . $model->host . '->ошибка авторизации с паролем');
            return ['error' => 'Ошибка, обратитесь в тех поддержку.'];
        }

    if ($model->login_type === 1) {
        $key = new RSA();
        if ($model->key_password != '')
            $key->setPassword($model->key_password);
        $key->loadKey($model->key);
        if (!$ssh->login($model->login, $key)) {
            LogController::addError(__FUNCTION__, 'service_id->' . $model->service_id . ' ' . $model->host . '->ошибка авторизации с ключем');
            return ['error' => 'Ошибка, обратитесь в тех поддержку.'];
        }
    }
    $response = $ssh->exec($command);
    if ($response === false) {
        LogController::addError(__FUNCTION__, 'service_id->' . $model->service_id . ' ' . $model->host . '->ошибка во время выполнения команды->' . $command);
        return ['error' => 'Ошибка, обратитесь в тех поддержку.'];
    }
    LogController::addSuccess(__FUNCTION__, 'service_id->' . $model->service_id . ' comand->' . $command . PHP_EOL . 'response->' . $response);
    if (!$detal)
        return 'success';

    return ['success' => 'comand->' . $command . PHP_EOL . 'response->' . $response];
}

function sshCommandRemoteServer_ClientAreaCustomButtonArray(array $params)
{
    $model = ServiceButtonModel::find($params['serviceid']);
    $buttons = [];
    if ($model === null) {
        return [];
    }
    $model = $model->toArray();
    $buttons = generateButtons($model, 'client_', 'sshCommandRemoteServer_ssh_client_run');
    return $buttons;
}

function sshCommandRemoteServer_AdminCustomButtonArray(array $params)
{
    $model = ServiceButtonModel::find($params['serviceid']);
    $buttons = [];
    if ($model === null) {
        return [];
    }
    $model = $model->toArray();
    $buttons = generateButtons($model, '', 'sshCommandRemoteServer_ssh_run');
    return $buttons;
}

function sshCommandRemoteServer_ssh_run(array $params, int $id)
{
    $model = ServiceButtonModel::find($params['serviceid']);
    if ($model == null)
        return ['error' => 'model not found'];
    $command = htmlspecialchars_decode($model->buttons[$id]['action']);
    return run_ssh_comand($command, $model, true);
}

function sshCommandRemoteServer_ssh_client_run(array $params, int $id)
{
    try {
        $model = ServiceButtonModel::find($params['serviceid']);

        if ($model == null)
            return ['error' => 'model not found'];
        $command = htmlspecialchars_decode($model->buttons[$id]['action']);
        return run_ssh_comand($command, $model, false);
    } catch (Throwable $e) {
        LogController::addError(__FUNCTION__, $params['serviceid'] . '-> ошибка во время выполнения команды->' . $command, $e);

        return ['error' => 'Ошибка, обратитесь в тех поддержку.'];
    }
}

function sshCommandRemoteServer_AdminServicesTabFields(array $params)
{
    try {
        $config = [];
        $model = ServiceButtonModel::find($params['serviceid']);
        $defaultModel = ServiceDefaultModel::all();
        $defaultName = $defaultModel->pluck('name');
        $defaultComand = $defaultModel->pluck('command');
        if ($model !== null)
            $config = $model->toArray();
        $fields['ip/домен сервера'] = '<input type="text" name="sshCommandRemoteServer_host" value="' . htmlspecialchars($config['host']) . '" />';
        $fields['ssh порт'] = '<input type="text" name="sshCommandRemoteServer_ssh_port" value="' . htmlspecialchars($config['port']) . '" />';
        $fields['ssh логин'] = '<input type="text" name="sshCommandRemoteServer_login" value="' . htmlspecialchars($config['login']) . '" />';
        $fields['тип логина ssh'] = '<select style="width: 169px;" onchange="sshCommandRemoteServer_change(this)" name="ssh_login_type">';
        if (!array_key_exists('login_type', $config)) {
            $fields['тип логина ssh'] .= '<option value="0"  selected>пароль</option>';
            $fields['тип логина ssh'] .= '<option value="1" >ключ</option>';
        } else {
            if ($config['login_type'] == 0)
                $fields['тип логина ssh'] .= '<option value="0" selected>пароль</option>';
            else
                $fields['тип логина ssh'] .= '<option value="0" >пароль</option>';

            if ($config['login_type'] == 1)
                $fields['тип логина ssh'] .= '<option value="1" selected>ключ</option>';
            else
                $fields['тип логина ssh'] .= '<option value="1" >ключ</option>';
        }
        $fields['тип логина ssh'] .= '</select>';
        $fields['ssh пароль'] = '<input type="text" name="sshCommandRemoteServer_password" value="' . htmlspecialchars($config['password']) . '" />';
        $fields['ssh ключ (только OpenSSL формат)'] = '<textarea rows="10" cols="45" name="sshCommandRemoteServer_ssh_key">' . htmlspecialchars($config['key']) . '</textarea>';
        $fields['Пароль для ssh ключа'] = '<input type="text" name="sshCommandRemoteServer_ssh_key_pass" value="' . htmlspecialchars($config['key_password']) . '" /> Только если ключ защищен паролем';
        $fields['Кол-во кнопок'] = '<input type="number" name="sshCommandRemoteServer_button_count"  value="' . htmlspecialchars($config['button_count']) . '"/>';
        $fields['js'] = '<script>$(document).ready(function() {sshCommandRemoteServer_change($("select[name=\'ssh_login_type\']")[0]);function sshCommandRemoteServer_change(selectObject){if(parseInt(selectObject.value)===0){$("textarea[name=\'sshCommandRemoteServer_ssh_key\']").parent().parent().hide();$("input[name=\'sshCommandRemoteServer_ssh_key_pass\']").parent().parent().hide();$("input[name=\'sshCommandRemoteServer_password\']").parent().parent().show();}else {$("input[name=\'sshCommandRemoteServer_password\']").parent().parent().hide();$("textarea[name=\'sshCommandRemoteServer_ssh_key\']").parent().parent().show();$("input[name=\'sshCommandRemoteServer_ssh_key_pass\']").parent().parent().show();}} $(".select-auto-new-tag").select2({tags: true,width:200,});$(".form tr td:contains(\'js\')").parent().remove();});</script>';
        for ($i = 1; $i <= intval($config['button_count']); $i++) {
            $fields['Кнопка ' . $i . ' имя'] = '<select style="width: 169px;" class="select-auto-new-tag"  name="button_' . $i . '_name">';
            if ($config['buttons'][$i - 1]['name'] !== "") {
                $fields['Кнопка ' . $i . ' имя'] .= '<option value="' . $config['buttons'][$i - 1]['name'] . '" selected>';
                $fields['Кнопка ' . $i . ' имя'] .= $config['buttons'][$i - 1]['name'];
                $fields['Кнопка ' . $i . ' имя'] .= '</option>';
            }
            foreach ($defaultName as $item) {
                if ($config['buttons'][$i - 1]['name'] == $item)
                    continue;
                $fields['Кнопка ' . $i . ' имя'] .= '<option value="' . $item . '">';
                $fields['Кнопка ' . $i . ' имя'] .= $item;
                $fields['Кнопка ' . $i . ' имя'] .= '</option>';
            }
            $fields['Кнопка ' . $i . ' имя'] .= '</select>';

            $fields['Кнопка ' . $i . ' действие'] = '<select style="width: 169px;" class="select-auto-new-tag" name="button_' . $i . '_action">';
            if ($config['buttons'][$i - 1]['action'] !== "") {
                $fields['Кнопка ' . $i . ' действие'] .= '<option value="' . $config['buttons'][$i - 1]['action'] . '" selected>';
                $fields['Кнопка ' . $i . ' действие'] .= $config['buttons'][$i - 1]['action'];
                $fields['Кнопка ' . $i . ' действие'] .= '</option>';
            }
            foreach ($defaultComand as $item) {
                if ($config['buttons'][$i - 1]['action'] == $item)
                    continue;
                $fields['Кнопка ' . $i . ' действие'] .= '<option value="' . $item . '">';
                $fields['Кнопка ' . $i . ' действие'] .= $item;
                $fields['Кнопка ' . $i . ' действие'] .= '</option>';
            }
            $fields['Кнопка ' . $i . ' действие'] .= ' </select>';
        }

        return $fields;
    } catch (Exception $e) {

    }
    return [];
}

function sshCommandRemoteServer_AdminServicesTabFieldsSave(array $params)
{
    $model = ServiceButtonModel::firstOrNew(['service_id' => $params['serviceid']]);
    $model->host = $_POST['sshCommandRemoteServer_host'];
    $model->port = $_POST['sshCommandRemoteServer_ssh_port'];
    $model->login = $_POST['sshCommandRemoteServer_login'];
    $model->password = $_POST['sshCommandRemoteServer_password'];
    $model->key = $_POST['sshCommandRemoteServer_ssh_key'];
    $model->key_password = $_POST['sshCommandRemoteServer_ssh_key_pass'];
    $model->button_count = $_POST['sshCommandRemoteServer_button_count'];
    $model->login_type = $_POST['ssh_login_type'];
    if ($model->button_count != 0) {
        $model->buttons = array_slice(array_filter($_POST, function ($key) {
            return stripos($key, '_name') !== false || stripos($key, '_action') !== false;
        }, ARRAY_FILTER_USE_KEY), 0, $model->button_count * 2);
    }
    $model->saveOrFail();
}
