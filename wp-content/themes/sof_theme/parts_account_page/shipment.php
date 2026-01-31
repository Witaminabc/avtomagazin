<div class="shipment account-tab" data-tab="shipments">
    <div class="mCustomScrollbar all-table" data-mcs-theme="dark" data-mcs-axis="x">
        <table>
            <thead>
            <tr>
                <th>№ заказа</th>
                <th>Название заказа</th>
                <th>Кол-во</th>
                <th>Стоимость</th>
                <th>Статус заказа</th>
                <th>Сроки оплаты</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>00012</td>
                <td>Моторное масло <span>«Mobil»</span></td>
                <td>100 шт.</td>
                <td>10 000 р.</td>
                <td> <img src="<?= get_template_directory_uri().'/img/indicator.png'; ?>" alt="indicator">Отгружен</td>
                <td>01.09.2018</td>
                <td>
                    <div>
                        <a href="#">Скачать pdf</a>
                        <a href="javascript:void(0);" class="openUrQr">Посмотреть QR-код</a>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <img src="<?= get_template_directory_uri().'/img/left-arrow.png'; ?>" alt="Back">
        <ul>
            <li>
                <p class="active">1</p>
            </li>
            <li>2</li>
            <li>3</li>
        </ul>
        <img src="<?= get_template_directory_uri().'/img/right-arrow.png'; ?>" alt="Forward">
    </div>
</div>