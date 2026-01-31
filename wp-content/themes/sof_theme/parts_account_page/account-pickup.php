<div class="account-pickup account-tab all-table" data-tab="pickup">
    <div class="top">
        <h4>Ваши пункты самовывоза:</h4>
    </div>
    <div class="mCustomScrollbar table-mutual" data-mcs-theme="dark" data-mcs-axis="x">
        <table>
            <thead>
            <tr>
                <th>Доступен при заказе</th>
                <th>Адрес</th>
                <th>Координаты</th>
                <th>Менеджер магазина</th>
                <th>Контактное лицо</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php

            //Точки самовывоза для Юридического лица
            if(in_array('user_magazin',$current_user_roles)) {
                $legalPickupPoints = get_all_pickup_with_coord(get_current_user_id());
            }
            foreach ($legalPickupPoints as $point):?>
                <?php $point->the_post();?>
                <?php
                $pointMeta = get_post_meta(get_the_ID());
                ?>
                <tr data-pickup-id="<?=get_the_ID()?>">
                    <td>
                        <input type="checkbox" data-pickup-id="<?=get_the_ID()?>" class="changeActivePoint" <?=(isset($pointMeta["active_point"]) && $pointMeta["active_point"][0] == '1') ? 'checked' : ''?>>
                    </td>
                    <td><?=get_the_title()?></td>
                    <td><?=$pointMeta["coords"][0]?></td>
                    <td><?php if(isset($pointMeta["magazin"])) {
                            $userMagazinId = $pointMeta["magazin"][0];
                            echo get_user_by( 'id', $userMagazinId )->display_name.' (id - '.$userMagazinId.')';
                        }?>
                    </td>
                    <td><?=(isset($pointMeta["fio-yuridicheskogo-lica"])) ? $pointMeta["fio-yuridicheskogo-lica"][0] : ''?>
                        <br>
                        <?=(isset($pointMeta["telefon-yuridicheskogo-lica"])) ? $pointMeta["telefon-yuridicheskogo-lica"][0] : ''?>
                    </td>
                    <td><a id="account-pickup-delete" href="#">Удалить</a></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <h4>Добавить новый пункт самовывоза:</h4>
    <div class="account-pickup-add user-form-1">
        <p>Выберите точку на карте</p>
        <div id="accountPickupMap" style="width: 100%; height: 500px"></div>
        <form>
            <h5>Новый пункт выдачи</h5>
            <input type="text" id="pickupAddress" name="pickupAddress" placeholder="Адрес" readonly required>
            <input type="text" id="pickupCoords" name="pickupCoords" placeholder="Координаты" readonly required>
            <label>
                Менеджер магазина
                <select id="pickupShop" name="pickupShop" required>
                    <option></option>

                </select>
            </label>
            <input type="text" id="pickupYlName" name="pickupYlName" placeholder="ФИО Контактного лица" required>
            <input type="tel" id="pickupYlPhone" name="pickupYlPhone" placeholder="Телефон Контактного лица" required>
            <button type="submit">Добавить</button>
        </form>
    </div>
</div>