<div class="my-shop account-tab all-table " data-tab="my-manager">
    <div class="top">
        <h4>Ваши магазины:</h4>
    </div>
    <div id="my-manager-list">

    </div>
    <h4>Регистрация нового менеджера:</h4>
    <div class="user-form-1">
        <form id="add-new-manager">
            <label>
                <input type="text" name="login" placeholder="Логин*" required>
            </label>
            <label>
            <input type="text" name="email" placeholder="Email*" required>
            </label>
            <label>
                <input type="password" name="password" placeholder="Пароль*" required>
            </label>
            <label>
                <input type="password" name="confirm_password" placeholder="Подтверждение пароля*" required>
            </label>
            <label>
                <input type="text" name="phone" placeholder="Телефон">
            </label>
            <button type="submit">Зарегистрировать</button>
        </form>
    </div>
</div>
<script>
    function getMyShop() {
        if($('#my-manager-list').length > 0) {
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'get_my_manager',
                },
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    if(message['status'] === 'success') {
                        $('#my-manager-list').html(message['manager']);
                    } else {
                    }
                }
            });
        }
    }
    $(document).ready(function () {
        getMyShop();
        $('#add-new-manager').submit(function (e) {
            e.preventDefault();
            $form = $(this);
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: $form.serialize()+'&action=add_new_manager',
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    showUniversalPopup('Регистрация нового менеджера',message['text']);
                    if(message['status'] === 'success') {
                        getMyShop();
                        getSelectPickupShop();
                    }
                }
            });
        })
    });
</script>