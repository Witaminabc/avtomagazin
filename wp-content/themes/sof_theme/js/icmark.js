/*
    develop by icmark.ru 2021
    (c) vafonin@icmark.ru
 */

function showUniversalPopup($head,$text) {
    $('.universal-popup .headline-popUp').html($head);
    $('.universal-popup p').html($text);
    $('.universal-popup').css('display','block');
    $('.bg-black').css('display','block');
}
function getUrlVar(){
    var urlVar = window.location.search; // получаем параметры из урла
    var arrayVar = []; // массив для хранения переменных
    var valueAndKey = []; // массив для временного хранения значения и имени переменной
    var resultArray = []; // массив для хранения переменных
    arrayVar = (urlVar.substr(1)).split('&'); // разбираем урл на параметры
    if(arrayVar[0]=="") return false; // если нет переменных в урле
    for (i = 0; i < arrayVar.length; i ++) { // перебираем все переменные из урла
        valueAndKey = arrayVar[i].split('='); // пишем в массив имя переменной и ее значение
        resultArray[valueAndKey[0]] = valueAndKey[1]; // пишем в итоговый массив имя переменной и ее значение
    }
    return resultArray; // возвращаем результат
}

$(document).ready(function () {
    /*
        Обработка фильтра с маркой автомобилля на главной странице
     */
    $urlParams = getUrlVar();
    $input = $('#mark-of-car .dd-selected-value');
    if(typeof $urlParams['car'] != "undefined" && typeof $input != "undefined") {
        $input.val($urlParams['car']);
        $('#mark-of-car .dd-selected').append('<label class="dd-selected-text">'+$urlParams['car']+'</label>');
    }

    /*
        Корзина
     */
    //Яндекс карта
    if($('#basket-map').length != 0) {
        //Проверяем доступные товары в пункт по умолчанию
        checkProductDelivery();
        //Собираем точки доставки
        createMap();
    }

    //Создание карты
    function createMap() {
        $deliveryPoints = {
            "type": "FeatureCollection",
            "features": []
        };
        $i = 0;
        $('#pickup option').each(function () {
            $title = $(this).val();
            $coords = $(this).attr('data-point-coords');
            if(typeof $coords != "undefined" && $title != '') {
                $deliveryPoints.features.push({
                    'type': "Feature",
                    'id': $i,
                    "geometry": {
                        "type": "Point",
                        "coordinates": $coords.split(','),
                    },
                    "properties": {
                        "iconCaption": $title,
                        "clusterCaption": $title,
                        "balloonContentHeader": $title,
                        "balloonContentBody": '<a data-map-point="'+$coords+'" class="deliveryMapChoicePoint" href="#">Выбрать этот пункт</a>',
                    }
                });
                $i++;
            }
        });

        ymaps.ready(init);

        function init() {
            var myMap = new ymaps.Map('basket-map', {
                    center: [56.838013, 60.597465],
                    zoom: 10
                }, {
                    searchControlProvider: 'yandex#search'
                }),
                objectManager = new ymaps.ObjectManager({
                    // Чтобы метки начали кластеризоваться, выставляем опцию.
                    clusterize: true,
                    // ObjectManager принимает те же опции, что и кластеризатор.
                    gridSize: 32,
                    clusterDisableClickZoom: true
                });

            // Чтобы задать опции одиночным объектам и кластерам,
            // обратимся к дочерним коллекциям ObjectManager.
            objectManager.objects.options.set('preset', 'islands#blueDeliveryIcon');
            objectManager.clusters.options.set('preset', 'islands#blueClusterIcons');
            myMap.geoObjects.add(objectManager);
            objectManager.add($deliveryPoints);
        }
    }

    function checkProductDelivery() {
        if (!$(".first-step").hasClass("corporate")) {
            $selectedPoint = $('#pickup option:selected').data("point-coords");
            let isNotAvailable = false;
            if ($('input[name=change-delivery]:checked').val() == 'Самовывоз' && $selectedPoint.length > 0) {
                $("div.one-product-basket").each(function () {
                    let check = $(this).attr('data-pickup').split(";").indexOf($selectedPoint);
                    if (check === -1) {
                        $(this).find(".product-error").show();
                        if (!isNotAvailable)
                            isNotAvailable = true;
                    } else {
                        $(this).find(".product-error").hide();
                    }
                })
            } else {
                isNotAvailable = true;
            }

            if (isNotAvailable) {
                $("#next-first-step").addClass('not_available_button');
            } else {
                $("#next-first-step").removeClass('not_available_button');
            }
        } else {
            $("div.one-product-basket").find(".product-error").hide();
            $("#next-first-step").removeClass('not_available_button');
        }
    }

    $('#pickup').on('change',function () {
        checkProductDelivery();
    });
    $('#self').on('change',function () {
        checkProductDelivery();
    });
    $('#mainAdress').on('change',function () {
        $("#next-first-step").removeClass('not_available_button');
    });
    $(document).on('click','.deliveryMapChoicePoint',function (e) {
        e.preventDefault();

        //Изменяем значение в селекторе
        $pointTitle = $(this).attr('data-map-point');
        $('#pickup option[data-point-coords="'+$pointTitle+'"]').prop('selected', true);

        checkProductDelivery();

        //Закрываем маркер/окно на карте
        if ($(this).prev('ymaps').length != 0) {
            $closeBtn = $(this).prev('ymaps').attr('class').replace('-content__header', '__close');
        } else {
            $closeBtn = $(this).parent('ymaps').attr('class').split(' ')[0].replace('-b-cluster-tabs__item-body', '-balloon__close-button');
        }
        $('.' + $closeBtn).trigger('click');

    });
    /*
        Страница Аккаунт
     */


    function sleep(milliseconds) {
        const date = Date.now();
        let currentDate = null;
        do {
            currentDate = Date.now();
        } while (currentDate - date < milliseconds);
    }
    //Карта для добавления точек
    if($('#accountPickupMap').length != 0) {
        ymaps.ready(init);
        var newPoint = {};
        function init() {
            var myPlacemark,
                myMap = new ymaps.Map('accountPickupMap', {
                    center: [56.838013,60.597465],
                    zoom: 7,
                }, {
                    searchControlProvider: 'yandex#search'
                });

            // Слушаем клик на карте.
            myMap.events.add('click', function (e) {
                var coords = e.get('coords');
                newPoint.coords = coords;
                // Если метка уже создана – просто передвигаем ее.
                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                }
                // Если нет – создаем.
                else {
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.add(myPlacemark);
                    // Слушаем событие окончания перетаскивания на метке.
                    myPlacemark.events.add('dragend', function () {
                        getAddress(myPlacemark.geometry.getCoordinates());
                    });
                }
                getAddress(coords);
                setTimeout(function () {
                    $('.account-pickup-add form #pickupAddress').val(newPoint.address);
                    $('.account-pickup-add form #pickupCoords').val(newPoint.coords.join(','));
                },1000);
            });

            // Создание метки.
            function createPlacemark(coords) {
                return new ymaps.Placemark(coords, {
                    iconCaption: 'поиск...'
                }, {
                    preset: 'islands#violetDotIconWithCaption',
                    draggable: true
                });
            }

            // Определяем адрес по координатам (обратное геокодирование).
            function getAddress(coords) {
                var oldAddress = $('#pickupAddress').val();
                myPlacemark.properties.set('iconCaption', 'поиск...');
                ymaps.geocode(coords).then(function (res) {
                    var firstGeoObject = res.geoObjects.get(0);

                    myPlacemark.properties
                        .set({
                            // Формируем строку с данными об объекте.
                            iconCaption: [
                                // Название населенного пункта или вышестоящее административно-территориальное образование.
                                firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                                firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                            ].filter(Boolean).join(', '),
                            // В качестве контента балуна задаем строку с адресом объекта.
                            balloonContent: firstGeoObject.getAddressLine()
                        });
                    newPoint.address = firstGeoObject.getAddressLine();
                });
            }
        }
    }

    // Загрузка ВТТ для добавление точек замовывоза
    function getSelectPickupShop () {
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'get_select_pickup_shop',
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                if(message['status'] == 'success') {
                    $('#pickupShop').html('<option></option>');
                    $('#pickupShop').append(message['pickupShopSelector']);
                } else {
                    alert('Произошла ошибка при загрузке ВТТ');
                }
            }
        });
    }
    getSelectPickupShop();
    //Открытие выбраного таба
    function checkActiveNavTab() {
        $tabId = $('.account-nav-tab-list .account-nav-tab.active').attr('data-nav-tab');
        $('.account-tab-list .account-tab.active').removeClass('active');
        $('.account-tab-list .account-tab[data-tab="'+$tabId+'"]').addClass('active');
    }

    checkActiveNavTab();

    //Навигационные табы
    $('.account-nav-tab-list .account-nav-tab').on('click',function () {
        $('.account-nav-tab-list .account-nav-tab.active').removeClass('active');
        $(this).addClass('active');
        checkActiveNavTab();
    });

    //Добавление нового пункта выдачи
    $('.account-pickup-add form').submit(function (e) {
        e.preventDefault();
        $form = $(this);
        $pickupAddress = $form.find('#pickupAddress').val();
        $pickupCoords = $form.find('#pickupCoords').val();
        $pickupShop = $form.find('#pickupShop').val();
        $pickupShopName = $form.find('#pickupShop option:selected').text();
        $pickupYlName = $form.find('#pickupYlName').val();
        $pickupYlPhone = $form.find('#pickupYlPhone').val();
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'new_pickup',
                pickupAddress: $pickupAddress, pickupCoords: $pickupCoords, pickupShop: $pickupShop, pickupYlName: $pickupYlName, pickupYlPhone: $pickupYlPhone,
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                if(message['status'] == 'success') {
                    $('.pickupSuccessAdd').css('display','block');
                    $('.bg-black').css('display','block');
                    $('.account-pickup tbody').append('' +
                        '<tr data-pickup-id="'+message['post_id']+'">' +
                        '<td> <input type="checkbox" data-pickup-id="'+message['post_id']+'" class="changeActivePoint" checked></td>' +
                        '<td>'+$pickupAddress+'</td>' +
                        '<td>'+$pickupCoords+'</td>' +
                        '<td>'+$pickupShopName+'</td>' +
                        '<td>'+$pickupYlName+'<br>'+$pickupYlPhone+'</td>' +
                        '<td><a id="account-pickup-delete" href="#">Удалить</a></td>' +
                        '</tr>');
                    getSelectPickupShop();
                } else {
                    alert('Произошла ошибка, пункт не добавлен');
                    $('.pickupErrorAdd').css('display','block');
                    $('.bg-black').css('display','block');
                }
            }
        });
    });

    //Удаление пункта выдачи
    $(document).on('click','#account-pickup-delete',function (e) {
        e.preventDefault();
        $pickup = $(this).parents('tr');
        $pickupID = $pickup.attr('data-pickup-id');
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'remove_pickup',
                pickupID: $pickupID,
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                if(message['status'] == 'success') {
                    $('.pickupSuccessRemove').css('display','block');
                    $('.bg-black').css('display','block');
                    $pickup.remove();
                    getSelectPickupShop();
                } else {
                    $('.pickupSuccessRemove').css('display','block');
                    $('.bg-black').css('display','block');
                }
            }
        });
    });


    //Получение списка заказов
    $('#account-customer-orders-search').submit(function (e) {
        e.preventDefault();
        $form = $(this);
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'get_customer_orders_by_date',
                date: $form.find('[name="date"]').val(),
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                $('#account-customer-orders-list').html('');
                if(message['status'] == 'success') {
                    $('#account-customer-orders-list').append(message['orders']);
                    $('#account-customer-orders-search .form-error').text('');
                } else {
                    $('#account-customer-orders-search .form-error').text(message['text']);
                }
            }
        });
    });

    //Получение списка заказов клиента
    $('#account-order-search').submit(function (e) {
        e.preventDefault();
        $form = $(this);
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'get_orders_by_date',
                date: $form.find('[name="date"]').val(),
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                $('#account-order-list tbody').html('');
                if(message['status'] == 'success') {
                    $('#account-order-list tbody').append(message['orders']);
                    $('#account-order-search .form-error').text('');
                } else {
                    $('#account-order-search .form-error').text(message['text']);
                }
            }
        });
    });

    //Детальная информация по заказу
    $(document).on('click','.show-order-detail',function (e) {
        e.preventDefault();
        $orderId = $(this).attr('data-order-id');
        if(typeof $orderId != "undefined") {
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'account_order_detail',
                    order_id: $orderId,
                },
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    $orderProducts = $('#order-detail .order-detail-products tbody');
                    $orderInfo = $('#order-detail .order-detail-info tbody');
                    $orderProducts.html('');
                    $orderInfo.html('');
                    if(message['status'] == 'success') {
                        $('#order-detail .order-id').text('№ '+$orderId);
                        $.each(message['data']['products'],function (index,value) {
                            $orderProducts.append(
                                '<tr><td>'+value.name+'</td><td>'+value.quantity+'</td><td>'+value.cost+'</td></tr>'
                        );
                        });
                        $orderInfo.html(
                            '<tr><td>ФИО:</td><td>'+message["data"]["orderData"].name+'</td></tr>'+
                            '<tr><td>Адрес:</td><td>'+message["data"]["orderData"].address+'</td></tr>'+
                            '<tr><td>Телефон:</td><td>'+message["data"]["orderData"].phone+'</td></tr>'+
                            '<tr><td>Email:</td><td>'+message["data"]["orderData"].email+'</td></tr>'+
                            '<tr><td>Доставка:</td><td>'+message["data"]["orderData"].delivery+'</td></tr>'+
                            '<tr><td>Сумма:</td><td>'+message["data"]["orderData"].total+'</td></tr>'+
                            '<tr><td>Способ оплаты:</td><td>'+message["data"]["orderData"].payment+'</td></tr>'+
                            '<tr><td>Срок оплаты:</td><td>'+message["data"]["orderData"].payment_data+'</td></tr>'+
                            '<tr><td>Статус заказа:</td><td>'+message["data"]["orderData"].status+'</td></tr>'
                        );
                        $('#order-detail').css('display','block');
                        $('.bg-black').css('display','block');
                    } else {
                        alert(message['text']);
                    }
                }
            });
        }
    })

    //Смена пункта самовывоза по умолчанию
    $('#default-pickup-form').submit(function (e) {
        e.preventDefault();
        $form = $(this);
        $pickup = $form.find('select').val();
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'update_default_pickup',
                pickup: $pickup,
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                if(message['status'] == 'success') {
                    showUniversalPopup('Пункт самовывоза по умолчанию','Пункт самовывоза по умолчанию успешно обновлён');
                } else {
                    showUniversalPopup('Пункт самовывоза по умолчанию','<font color="red">Ошибка!</font> Пункт самовывоза по умолчанию не обновлён');
                }
            }
        });
    });

    //Выключение активность у пункта самовывоза
    $(document).on('click','.changeActivePoint',function () {
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'change_active_pickup',
                pickupId:  $(this).attr('data-pickup-id'),
                active: $(this).is(':checked'),
            },
            error: function(error){
                alert("error");
                console.log(error)
            },
            success: function(msg){
                let message = JSON.parse(msg);
                console.log(message);
                if(message['status'] == 'success') {
                    showUniversalPopup('Пункт самовывоза','Пункт самовывоза обновлён');
                } else {
                    showUniversalPopup('Пункт самовывоза','<font color="red">Ошибка!</font> Пункт самовывоза не обновлён');
                }
            }
        });
    });

    //Смена статуса у заказа клиента
    $(document).on('click','.order-change-status',function (e) {
        e.preventDefault();
        $btn = $(this);
        $error = false;
        $orderId = $btn.parents('.account-orders-item').attr('data-order-id');
        if ($btn.hasClass('order-complete-btn')) $status = 'complete';
        else {
            if ($btn.hasClass('order-close-btn')) $status = 'close'; else $error = true;
        }
        if(!$error && typeof $orderId != 'undefined' && typeof $status != 'undefined') {
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'change_customer_order_status',
                    order_id: $orderId,
                    status: $status,
                },
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    if(message['status'] == 'success') {
                        $('#account-customer-orders-search').trigger("submit");
                        showUniversalPopup('Заказ № '+$orderId,'Статус заказа обновлён');
                    } else {
                        showUniversalPopup('Заказ № '+$orderId,'<font color="red">Ошибка!</font> Статус заказа не обновлён');
                    }
                }
            });
        } else {
            alert('Возникла ошибка');
        }
    });

    //Смена статуса у заказа клиента
    $(document).on('click','.user-order-canceled',function (e) {
        e.preventDefault();
        $btn = $(this);
        $orderId = $btn.parents('.account-user-order').attr('data-order-id');
        if(typeof $orderId != 'undefined') {
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'cancel_order',
                    order_id: $orderId,
                },
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    if(message['status'] == 'success') {
                        $('#account-order-search').trigger("submit");
                        showUniversalPopup('Заказ № '+$orderId,'Статус заказа обновлён');
                    } else {
                        showUniversalPopup('Заказ № '+$orderId,'<font color="red">Ошибка!</font> Статус заказа не обновлён');
                    }
                }
            });
        } else {
            alert('Возникла ошибка');
        }
    });

    //QR код на детализацию заказа в PDF
    $(document).on('click','.open-order-qr',function (e) {
        e.preventDefault();
        $btn = $(this);
        $host = location.protocol + "//" + location.host;
        $orderId = $btn.parents('.account-user-order').attr('data-order-id');
        $link = $host+'/wp-content/themes/sof_theme/report_order_account.php?new_order='+$orderId;
        $('.universalQR .shortcode').attr('src',QRCode.generatePNG($link,{
            custombgcolor: 'white',
            customcolor: 'black',
            modulesize: 7
        }));
        $('.universalQR').show();
        $('.bg-black').show();
    });

    //QR код для перевода заказа в статус "Выполнено"
    $(document).on('click','.complete-order-basket',function (e) {
        e.preventDefault();
        $btn = $(this);
        $host = location.protocol + "//" + location.host;
        $orderId = $btn.attr('data-order-id');
        $link = $host+'/wp-content/themes/sof_theme/php-scripts/complete_order.php?order_id='+$orderId;
        $('.universalQR .shortcode').attr('src',QRCode.generatePNG($link,{
            custombgcolor: 'white',
            customcolor: 'black',
            modulesize: 7
        }));
        $('.universalQR').show();
        $('.bg-black').show();
    });

    $('#profile-form').submit(function (e) {
        e.preventDefault();
        $form = $(this);
        $firstName = $form.find('#first-name').val();
        $lastName = $form.find('#last-name').val();
        if($firstName != '' || $lastName != '') {
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'account_change_profile',
                    firstName: $firstName,
                    lastName: $lastName,
                },
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    if(message['status'] == 'success') {
                        showUniversalPopup('Профиль, Обращение.','Обращение успешно изменено');
                    } else {
                        showUniversalPopup('Заказ № '+$orderId,'<font color="red">Ошибка!</font>Обращение не изменено');
                    }
                }
            });
        } else {
            alert('Возникла ошибка');
        }
    });

    $(document).on('click','#next-first-step.user-magazin-cart',function () {
        $pickup = $('#pickup').val();
        $form = $('.second-step form');
        if($pickup != '' && typeof $pickup != 'undefined') {
            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'get_pickup_manager',
                    pickup: $pickup,
                },
                error: function(error){
                    alert("error");
                    console.log(error)
                },
                success: function(msg){
                    let message = JSON.parse(msg);
                    if(message['status'] == 'success') {
                        $form.each(function () {
                            $(this).find('input[name=surname]').val(message['manager'].lastname);
                            $(this).find('input[name=name]').val(message['manager'].firstname);
                            $(this).find('input[name=phone]').val(message['manager'].phone);
                            $(this).find('input[name=email]').val(message['manager'].email);
                            $(this).find('input[name=adress]').val(message['manager'].address);
                        })
                    } else {
                        alert('Возникла ошибка');
                    }
                }
            });
        } else {
            alert('Возникла ошибка');
        }
    });

    $("#pickup").select2({
        "language": {
            "noResults": function(){
                return "Точек самовывоза не найдено";
            }
        }
    });

    /* Show available pickup point */
    $("#filter-pickup").click(function (e) {
        e.preventDefault();

        let pickupList = [];
        $(".one-product-basket").each(function(){
            pickupList.push($(this).data("pickup").split(";"));
        })

        if (pickupList.length > 0) {
            let commonPickupList = [];
            pickupList[0].forEach(function (value) {
                let isExist = true;
                pickupList.forEach(function (otherList, index) {
                    if (index !== 0) {
                        if (otherList.indexOf(value) === -1) {
                            isExist = false;
                        }
                    }
                })
                if (isExist)
                    commonPickupList.push(value);
            })

            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'get_pickup',
                    pickup: commonPickupList.join(";"),
                },
                success: function(data){
                    $("#pickup option:not(:first-child)").detach();
                    $("#pickup").append(JSON.parse(data));
                    $("#pickup").val("");
                    $("#pickup").trigger("refresh");
                    $("#basket-map").html("");
                    createMap();
                }
            });
        }
    })
})
