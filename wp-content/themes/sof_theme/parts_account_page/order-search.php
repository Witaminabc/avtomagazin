<div class="orders account-tab all-table user-form-1" data-tab="orders">
    <form id="account-order-search">
        <h5>Выберите интервал</h5>
        <label>
            <input type="text" name="date" data-range="true" value="<?=date("d.m.Y",time()-604800).' - '.date("d.m.Y",time())?>" data-multiple-dates-separator=" - " class="datepicker-here" required readonly />
        </label>
        <p class="form-error"></p>
        <button type="submit">Загрузить</button>
    </form>
    <div class="mCustomScrollbar table-mutual" data-mcs-theme="dark" data-mcs-axis="x">
        <table id="account-order-list">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Номер</th>
                <th>Сумма</th>
                <th>Подробнее</th>
                <th>PDF выгрузка</th>
                <th>Ссылка на QR-код</th>
                <th>Статус заказа</th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>