<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="tableServiceList" width="100%" class="datatable no-margin">
            <thead>
            <tr>
                <th>
                    Клиент
                </th>
                <th>
                    Услуга
                </th>
                <th>
                    Статус услуги
                </th>
                <th>
                    Оплачена до
                </th>
                <th>
                    Сервер
                </th>
                <th>
                    Добавлен
                </th>
                <th>
                    Изменен
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach $services as $service}
                <tr class="product">
                    <td>
                        <a href="clientssummary.php?userid={$service.client_id}">{$service.client_name}</a>
                    </td>
                    <td>
                        {if !empty($service.service_url)}
                            <a href="{$service.service_url}">{$service.product_name}</a>
                        {else}
                            {$service.product_name}
                        {/if}
                    </td>
                    <td>
                        {$service.service_status}
                    </td>
                    <td>
                        {if $service.service_expire eq '0000-00-00'}
                            Не применимо
                        {else}
                            {$service.service_expire}
                        {/if}
                    </td>
                    <td>
                        {$service.host}
                    </td>
                    <td>
                        {$service.created_at}
                    </td>
                    <td>
                        {$service.updated_at}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>


