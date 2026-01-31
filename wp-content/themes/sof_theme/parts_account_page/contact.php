<div class="contact account-tab" data-tab="manager">
    <div class="top">
        <?php
        $metaPM = array( 'name_manager' => '', 'phone_manager' => '', 'email_manager' => '', 'vk_manager' => '', 'instagram_manager' => '', );
        $current_user = wp_get_current_user();
        $current_user_roles = array();
        foreach ($current_user->roles as $user_role) {
            $current_user_roles[] = $user_role;
        }
        if(in_array('shop_manager',$current_user_roles) || in_array('user_magazin',$current_user_roles)) {
            $metaPM = get_metadata('user',$current_user->ID);
        } else {
            $naturalPM = $users = get_users( array(
                'role' => 'natural_person_manager'
            ));
            if(count($naturalPM) > 0) {
                $metaPM = get_metadata('user',$naturalPM[0]->data->ID);
            }
        }
        ?>
        <h4>Ваш менеджер: <span> <?=$metaPM['name_manager'][0]?></span></h4>
        <div class="social">
            <h4>Соц. сети:</h4>
            <div>
                <a href="<?=$metaPM['vk_manager'][0]?>"><img src="<?= get_template_directory_uri().'/img/vk.png'; ?>" alt="VK"></a>
                <a href="<?=$metaPM['instagram_manager'][0]?>"><img src="<?= get_template_directory_uri().'/img/instagram.png'; ?>" alt="Instagram"></a>
            </div>
        </div><!-- end social -->
    </div><!-- end top -->
    <div class="bottom">
        <a href="tel:<?=$metaPM['phone_manager'][0]?>"><img src="<?= get_template_directory_uri().'/img/phone.png'; ?>" alt="Phone"><?=$metaPM['phone_manager'][0]?></a>
        <a href="mailto:<?=$metaPM['email_manager'][0]?>"><img src="<?= get_template_directory_uri().'/img/mail.png'; ?>" alt="Email"><?=$metaPM['email_manager'][0]?></a>
    </div><!-- end botom --
						</div><!-- end six -->
</div>