<div class="row pull-right" style="margin-right: 4px;">
    <div class="col-md-3">
        <div class="text-right">
            <!-- Split button -->
            <div class="btn-group">
                <form role="form" method="get" style="display: inline;">
                    <input type="hidden" name="module" value="SshRemoteServiceCommand">
                    <input type="hidden" name="action" value="add_default_command">
                    <input type="submit" class="btn btn-success btn-sm" value="Добавить шаблон">
                </form>
            </div>
        </div>
    </div>
</div><div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="tableCommandList" width="100%" class="datatable no-margin">
            <thead>
            <tr>
                <th>
                    Имя
                </th>
                <th>
                    Команда
                </th>
                <th>
                    Добавлена
                </th>
                <th>
                    Изменена
                </th>
                <th style="width: 2%;"></th>
            </tr>
            </thead>
            <tbody>
            {foreach $defaultButtons as $defaultButton}
                <tr class="product">
                    <td>
                        {$defaultButton.name}
                    </td>
                    <td>
                        {$defaultButton.command}
                    </td>
                    <td>
                        {$defaultButton.created_at}
                    </td>
                    <td>
                        {$defaultButton.updated_at}
                    </td>
                    <td>
                        <a href="addonmodules.php?module=SshRemoteServiceCommand&action=delete&id={$defaultButton.id}"
                           title="Удалить запись"
                           onClick="return window.confirm('Вы точно хотите удалить документ {$defaultButton.name} ?');"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>


