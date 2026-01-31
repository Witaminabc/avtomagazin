var site_url = document.location.origin;

$(document).ready(function(){

	$(".quantity-num").on('change', function(){
		var id = $(this).attr("data-input_quantity_product");
		var low = +$(this).attr("data-low_stock");
		var q = +$(this).val();
		if(q > low){
			$(".pop-up-attention").css("display", "block");
			$('#lowstock').text(low+" шт.");
			q = low;
			$(this).val(q);
		} //endif
		var currentprice = +$(this).attr("data-current_price");
		var regularprice = +$(this).attr("data-regular_price");
		if(!isInteger(q) || q <= 0){
			q = 1;
		}
		if(isInteger(q) && q >= 1){
			var result = (currentprice * q);
			$('#current_price'+id).text(result+" р.");
			result = (regularprice * q);
			$('#regular_price'+id).text(result+" р.");
			$.ajax({
				type: 'GET',
				url: site_url + '/wp-admin/admin-ajax.php',
				data: {
					id: id,
					q: q,
					action: 'updatebasket'
				},
				cache: false,
				dataType: "json",
				error: function(error){
					alert("error");
				},
				success: function(data){
//c	$('#update_cart').text(data.data);
					$('#open_product_basket').text(data.quantity);
					$('#col_product').text(data.unique_quantity);
					if(data.product != ''){
						$('#all_product').html(data.product);
					} //endif
					if(data.price != ''){
						$('#all_price').text(data.price);
					} //endif
					if(data.sum != ''){
						$('#cart_sum').html(data.sum);
					} //endif

				} //endsuccess
			}); //endajax
		}
		else{
			result = currentprice;
			$('#current_price'+id).text(result+" р.");
			result = regularprice;
			$('#regular_price'+id).text(result+" р.");

		}

	}); //end input
	$("[data-cart_plus]").on('click', function(){
		var id = $(this).attr("data-cart_plus");
		var q = 1;
		q += +$('#input_quantity_product'+id).val();
		var low = +$('#input_quantity_product'+id).attr("data-low_stock");
		if(q > low){
			$(".pop-up-attention").css("display", "block");
			$('#lowstock').text(low+" шт.");
			q = low;
			$('#input_quantity_product'+id).val(q);
		} //endif

		var currentprice = +$('#input_quantity_product'+id).attr("data-current_price");
		var regularprice = +$('#input_quantity_product'+id).attr("data-regular_price");
		if(isInteger(q) && q >= 1){
			var result = (currentprice * q);
			$('#current_price'+id).text(result+" р.");
			result = (regularprice * q);
			$('#regular_price'+id).text(result+" р.");
			$.ajax({
				type: 'GET',
				url: site_url + '/wp-admin/admin-ajax.php',
				data: {
					id: id,
					q: q,
					action: 'updatebasket'
				},
				cache: false,
				dataType: "json",
				error: function(error){
					alert("error");
				},
				success: function(data){
//c	$('#update_cart').text(data.data);
					$('#open_product_basket').text(data.quantity);
					$('#col_product').text(data.unique_quantity);
					if(data.product != ''){
						$('#all_product').html(data.product);
					} //endif
					if(data.price != ''){
						$('#all_price').text(data.price);
					} //endif
					if(data.sum != ''){
						$('#cart_sum').html(data.sum);
					} //endif

				} //endsuccess
			}); //endajax
		}
		else{
			result = currentprice;
			$('#current_price'+id).text(result+" р.");
			result = regularprice;
			$('#regular_price'+id).text(result+" р.");
		} //endelse
	}); //endclick plus
	$("[data-cart_minus]").on('click', function(){
		var id = $(this).attr("data-cart_minus");
		var q = +$('#input_quantity_product'+id).val();
		q -= 1;
		var currentprice = +$('#input_quantity_product'+id).attr("data-current_price");
		var regularprice = +$('#input_quantity_product'+id).attr("data-regular_price");
		if(isInteger(q) && q >= 1){
			var result = (currentprice * q);
			$('#current_price'+id).text(result+" р.");
			result = (regularprice * q);
			$('#regular_price'+id).text(result+" р.");
			$.ajax({
				type: 'GET',
				url: site_url + '/wp-admin/admin-ajax.php',
				data: {
					id: id,
					q: q,
					action: 'updatebasket'
				},
				cache: false,
				dataType: "json",
				error: function(error){
					alert("error");
				},
				success: function(data){
//c	$('#update_cart').text(data.data);
					$('#open_product_basket').text(data.quantity);
					$('#col_product').text(data.unique_quantity);
					if(data.product != ''){
						$('#all_product').html(data.product);
					} //endif
					if(data.price != ''){
						$('#all_price').text(data.price);
					} //endif
					if(data.sum != ''){
						$('#cart_sum').html(data.sum);
					} //endif

				} //endsuccess
			}); //endajax
		}
		else{
			result = currentprice;
			$('#current_price'+id).text(result+" р.");
			result = regularprice;
			$('#regular_price'+id).text(result+" р.");
		} //endelse
	}); //endclick minus
	$("[data-delete_product]").on('click', function(){
		var id = $(this).attr("data-delete_product");
		$.ajax({
			type: 'GET',
			url: site_url + '/wp-admin/admin-ajax.php',
			data: {
				id: id,
				action: 'deletebasket'
			},
			cache: false,
			dataType: "json",
			error: function(error){
				alert("error");
			},
			success: function(data){
				if(data.unique_quantity == 0){
					window.location.assign(data.url);
				} //endif
				else{

					$('#open_product_basket').text(data.quantity);
					$('#col_product').text(data.unique_quantity);
					if(data.product != ''){
						$('#all_product').html(data.product);
					} //endif
					if(data.price != ''){
						$('#all_price').text(data.price);
					} //endif
					if(data.sum != ''){
						$('#cart_sum').html(data.sum);
					} //endif
					//check_available_product();
				} //endelse
			} //endsuccess
		}); //endajax


	}); //end click delete

	$('#checkout_courier').on('click', function(){
		var s = '';
		var surname = document.getElementById('surname').value;
		var name = document.getElementById('name').value;
		var phone = document.getElementById('phone').value;
		var email = document.getElementById('email').value;
		var adress = document.getElementById('adress').value;

		if(surname === s || name === s || phone === s || email === s || adress === s){
//c              alert('Заполните пожалуйста все поля');
			alert(surname,name,phone,email,adress);
		}
		else {
			$('.second').removeClass('active');
			$('.third').addClass('active');
			$('.second-step').css('display', 'none')
			$('.third-step').css('display', 'block')
			var delivery = $('input[name=change-delivery]:checked').val();
			var pay = $('input[name=change-pay]:checked').val();
			surname = $('#surname').val();
			name = $('#name').val();
			phone = $('#phone').val();
			email = $('#email').val();
			adress = $('#adress').val();
			var pickup = '';
			var typ = 'courier';
			order_ajax(delivery, pay, surname, name, phone, email, adress, pickup, typ);
		} //endelse

	}); //endclick checkout
	$('#checkout_pickup').on('click', function(){
		var s = '';
		var surname1 = document.getElementById('surname1').value;
		var name1 = document.getElementById('name1').value;
		var phone1 = document.getElementById('phone1').value;
		var email1 = document.getElementById('email1').value;
		if(surname1 === s || name1 === s || phone1 === s || email1 === s){
//c              alert('Заполните пожалуйста все поля');
		}
		else {
			$('.second').removeClass('active');
			$('.third').addClass('active');
			$('.second-step').css('display', 'none')
			$('.third-step').css('display', 'block')

			var delivery = $('input[name=change-delivery]:checked').val();
			var pay = $('input[name=change-pay]:checked').val();
			var pickup = $("#pickup").val();
			surname = $('#surname1').val();
			name = $('#name1').val();
			phone = $('#phone1').val();
			email = $('#email1').val();
			var adress = '';
			var typ = 'pickup';
			order_ajax(delivery, pay, surname, name, phone, email, adress, pickup, typ);
		} //endelse

	}); //endclick checkout

	/*function check_available_product(){
		var point = $('#pickup').val();
		$("div.one-product-basket").each(function(){
			var check = $(this).attr('data-pickup').indexOf(point);
			if(check==-1 && $(this).attr('data-pickup')!=="Все точки самовывоза"){
				$("div.one-product-basket").children("div.not_available_in_this_point").hide();
				$(this).children("div.not_available_in_this_point").show();
				$(this).addClass('remove_item');
			}else{
				$(this).children("div.not_available_in_this_point").hide();
				$(this).removeClass('remove_item');
			}
			if($(this).hasClass("remove_item")){
				$("#next-first-step").addClass('not_available_button');
			}else{
				$("#next-first-step").removeClass('not_available_button');
			}
		})
	}
	$(document).on('click', ' option', function(){
		check_available_product();
	}); */
	/*$("input[type=radio][name=change-delivery]").change(function(){
		if($(this).attr('id')=="mainAdress"){
			$("div.not_available_in_this_point").hide();
			$("#next-first-step").removeClass('not_available_button');
		}else if($(this).attr('id')=="self"){
			check_available_product();
		}
	}) */


}); //endready
function show_basket_qr(){
	$('#result_basket_qr').css("display", "block");
} //endfunction
function show_order_qr(){
	$('.secondQr').css('display','block');
	$('.bg-black').css('display','block');
} //endfunction
function order_ajax(delivery, pay, surname, name, phone, email, adress, pickup, typ){
	$.ajax({
		type: 'GET',
		url: site_url + '/wp-admin/admin-ajax.php',
		data: {
			delivery: delivery,
			pay: pay,
			name: name,
			surname: surname,
			phone: phone,
			email: email,
			adress: adress,
			pickup: pickup,
			typ: typ,
			action: 'makeorder'
		},
		cache: false,
		dataType: "json",
		error: function(error){
			alert("error");
		},
		success: function(data){
			$('#result_checkout').html(data.data);
			$('.openSecondQr').on('click', show_order_qr);
		} //endsuccess
	}); //endajax

} //endfunction
function isInteger(q) {
	return (q ^ 0) === q;
}
//$(document).ready(function(){
//
//    });