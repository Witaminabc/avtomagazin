<div class="customer-orders account-tab all-table user-form-1" data-tab="customer-orders">
    <form id="account-customer-orders-search">
        <h5>Выберите интервал</h5>
        <label>
            <input type="text" name="date" data-range="true" value="<?=date("d.m.Y",time()-604800).' - '.date("d.m.Y",time())?>" data-multiple-dates-separator=" - " class="datepicker-here" required readonly />
        </label>
        <p class="form-error"></p>
        <button type="submit">Загрузить</button>
    </form>
    <div id="account-customer-orders-list">
    </div>
    <div id="customer-orders-detail" class="mainPop-up oneOfPopUp" style="display: none;">
        <h3>Информация по заказу <span class="order-id"></span></h3>
        <table class="order-detail-products">
            <thead>
            <tr>
                <th colspan="3"><h4>Товары</h4></th>
            </tr>
            <tr>
                <th>Название</th>
                <th>Количество</th>
                <th>Сумма</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <table class="order-detail-info">
            <thead>
            <tr>
                <th colspan="3"><h4>Клиент</h4></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>