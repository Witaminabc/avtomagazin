<div class="account-tab" data-tab="profile" id="account-tab">
    <div class="profile user-form-1">
        <form class="left part" id="profile-form">
            <h5>Обращение</h5>
            <div>
                <label>
                    Имя
                    <input type="text" name="first-name" value="<?=$current_user->user_firstname?>" placeholder="Введите имя" id="first-name" required>
                </label>
            </div>
            <div>
                <label>
                    Фамилия
                    <input type="text" name="last-name" value="<?=$current_user->user_lastname?>" placeholder="Введите фамилию" id="last-name">
                </label>
            </div>
            <button type="submit">Сохранить</button>
        </form><!-- end left part -->
        <form class="left part" id="pass_form" action="javascript:void(0)" method="POST">
            <h5>Изменить пароль</h5>
            <div>
                <input type="password" name="old_password" placeholder="Введите старый пароль" class="for-look-pas" id="oldPas">
                <img src="<?= get_template_directory_uri().'/img/eye.png'; ?>" alt="Look" class="look-pas">
            </div>
            <div>
                <input type="password" name="new_password" placeholder="Введите новый пароль" class="for-look-pas" id="newPas">
                <img src="<?= get_template_directory_uri().'/img/eye.png'; ?>" alt="Look" class="look-pas">
                <input type="hidden" name="action" value="changepassword">
            </div>
            <div class="button">
                <a href="#" id="save_pass">Сохранить</a>
            </div>
        </form><!-- end left part -->
        <form class="right part" id="email_form" action="javascript:void(0)" method="POST">
            <h5>Изменить почту</h5>
            <h6>Ваша почта: <span><?php echo $current_user->user_email; ?></span></h6>
            <div>
                <input type="email" name="email" placeholder="Введите новую почту" id="soloEmail">
                <input type="hidden" name="action" value="resetemail">
            </div>
            <div class="button">
                <a href="javascript:void(0)" id="save_email">Сохранить</a>
            </div>
        </form>
        <form class="left part" id="phone_form" action="javascript:void(0)" method="POST">
            <h5>Изменить контактный телефон</h5>
            <h6>Ваш телефон: <span id="your_phone"><?php echo get_user_meta($current_user->ID, 'phone', true); ?></span></h6>
            <div>
                <input type="text" name="phone" id="phone3" class="form-control">
                <input type="hidden" name="action" value="resetphone">
            </div>
            <div class="button">
                <a href="javascript:void(0)" id="save_phone">Сохранить</a>
            </div>
        </form>
        <?php if(!in_array('shop_manager',$current_user_roles)) :?>
            <?php
            $pickupPoints = get_all_pickup_with_coord();
            $defaultPoint = get_user_meta($current_user->ID, 'default_pickup', true);
            ?>
            <form class="right part" id="default-pickup-form">
                <h5>Пункт самовывоза по умолчанию</h5>
                <label>
                    <select name="pickupDefault" class="form-control">
                        <option value="" label=""></option>
                        <?php
                        if(count($pickupPoints)) {
                            foreach ($pickupPoints as $pickupPoint) {
                                $pickupPoint->the_post();
                                $pickupTitle = get_the_title();
                                $pointMeta = get_post_meta(get_the_ID());
                                $selected = ($defaultPoint == $pickupTitle) ? 'selected' : '';
                                if (isset($pointMeta["active_point"]) && $pointMeta["active_point"][0] == '1') {
                                    echo '<option value="' . $pickupTitle . '" label="' . $pickupTitle . '" ' . $selected . '></option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </label>
                <button type="submit">Сохранить</button>
            </form>
        <?php endif;?>
        <?php if(in_array('user_magazin',$current_user_roles) && false) :?>
            <form class="bottom-right part" id="address_form" action="javascript:void(0)" method="POST">
                <h5>Добавить адрес доставки</h5>
                <?php
                $address_limit = get_option('my_address_limit');
                $str = '';
                ?>
                <?php for($x = 1; $x <= $address_limit; $x++): ?>
                    <?php $str .= $x.' '; ?>
                    <?php if($x == 1): ?>
                        <h6>Активный адрес доставки:<span><?php echo get_user_meta($current_user->ID, 'address'.$x, true); ?></span></h6>
                        <div>
                            <input type="text" name="<?php echo $x; ?>" value="<?php echo get_user_meta($current_user->ID, 'address'.$x, true); ?>" placeholder="Введите новый адрес доставки">
                        </div>
                    <?php endif; ?>
                    <?php if($x > 1): ?>
                        <?php if(empty(get_user_meta($current_user->ID, 'address'.$x, true))): ?>
                            <div class="more-addres" style="display: none;">
                                <input type="text" name="<?php echo $x; ?>" placeholder="Введите новый адрес доставки">
                            </div>
                            <div class="plus-address">
                                <p><span>+ </span> добавить еще один адрес</p>
                            </div>
                            <?php break; ?>
                        <?php elseif(!empty(get_user_meta($current_user->ID, 'address'.$x, true))): ?>
                            <h6>Адрес доставки:<span><?php echo get_user_meta($current_user->ID, 'address'.$x, true); ?></span></h6>
                            <div>
                                <input type="text" name="<?php echo $x; ?>" value="<?php echo get_user_meta($current_user->ID, 'address'.$x, true); ?>" placeholder="Введите новый адрес доставки">
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endfor; ?>
                <div class="button">
                    <input type="hidden" name="action" value="addaddress">
                    <input type="hidden" name="x" value="<?php echo trim($str); ?>">
                    <a href="javascript:void(0)" id="save_address">Сохранить</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>