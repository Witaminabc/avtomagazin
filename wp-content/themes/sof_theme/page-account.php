<?php
/**
 * Template name:  Account
 * Template Post Type: page
 */
get_header();
if(is_user_logged_in()){
$current_user = wp_get_current_user();

//Активная вкладка через ссылку
if(!empty($_GET['tab'])) $activeTab = $_GET['tab']; else $activeTab = 'profile';

/*
 * Навигационные вкладки
 * role - устанавливает доступ к вкладке в зависимости от роли пользователя
 */
$navTabs = array(
    array('role' => false,'title' => 'Профиль', 'nav-code' => 'profile'),
    array('role' => false,'title' => 'Заказы', 'nav-code' => 'orders'),
    array('role' => false,'title' => 'Контакты менеджера', 'nav-code' => 'manager'),
    array('role' => array('user_magazin','shop_manager'),'title' => 'Заказы клиентов', 'nav-code' => 'customer-orders'),
    array('role' => array('user_magazin'),'title' => 'Отгрузки', 'nav-code' => 'shipments'),
    array('role' => array('user_magazin'),'title' => 'Взаиморасчеты', 'nav-code' => 'settlements'),
    array('role' => array('user_magazin'),'title' => 'Задолженности', 'nav-code' => 'arrears'),
    array('role' => array('user_magazin'),'title' => 'Пункты самовывоза', 'nav-code' => 'pickup'),
    array('role' => array('user_magazin'),'title' => 'Ваши магазины', 'nav-code' => 'my-manager'),
);
// Собираем роли текущего пользователя
$current_user_roles = array();
foreach ($current_user->roles as $user_role) {
    $current_user_roles[] = $user_role;
}
?>
<main>
    <div class="area">
        <div class="container">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_home_url(); ?>">Главная</a></li>
                    <li><a href="<?php echo get_permalink(9); ?>">Личный кабинет</a></li>
                </ul>
            </div>
            <h2 class="main-headline"><?php the_title(); ?></h2>
            <div class="all-area">
                <aside class="all-links account-nav-tab-list">
                    <?php foreach ($navTabs as $key=>$navTab) {
                        $activeStatus = ($navTab['nav-code'] == $activeTab) ? 'active' : '';
                        if(!$navTab['role']) {
                            echo '<h4 data-nav-tab="' . $navTab['nav-code'] . '" class="account-nav-tab ' . $activeStatus . '">' . $navTab['title'] . '</h4>';
                        } else {
                            if(!empty($current_user_roles) && count(array_intersect($navTab['role'], $current_user_roles)) > 0) {
                                echo '<h4 data-nav-tab="' . $navTab['nav-code'] . '" class="account-nav-tab ' . $activeStatus . '">' . $navTab['title'] . '</h4>';
                            }
                        }
                    }?>
                    <h4><a href="<?=wp_logout_url("/");?>">Выйти из аккаунта</a></h4>
                </aside>
                <div class="all-blocks account-tab-list">
                    <? include_once 'parts_account_page/account-tab.php'; ?>
                    <? include_once 'parts_account_page/order-search.php'; ?>
                    <?php if(in_array('shop_manager',$current_user_roles) || in_array('user_magazin',$current_user_roles)) :?>
                        <? include_once 'parts_account_page/customer-orders.php'; ?>
                    <?php endif;?>
                    <?php if(in_array('user_magazin',$current_user_roles)) :?>
                        <? include_once 'parts_account_page/shipment.php'; ?>
                        <? include_once 'parts_account_page/mutual.php'; ?>
                        <? include_once 'parts_account_page/debt.php'; ?>
                        <? include_once 'parts_account_page/account-pickup.php'; ?>
                        <? include_once 'parts_account_page/my-shop.php'; ?>
                    <?php endif; ?>
                    <? include_once 'parts_account_page/contact.php'; ?>

                </div>
                <div id="order-detail" class="mainPop-up oneOfPopUp" style="display: none;">
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
                <?php } else {
                    echo'<p>Необходимо авторизоваться</p>';
                } ?>
            </div>
        </div>
</main>
<?php
get_footer();
?>
