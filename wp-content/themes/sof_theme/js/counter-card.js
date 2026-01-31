var site_url = document.location.origin;

$(document).ready(function(){
	var stock = $('#price').attr("data-quantidi");
	var low = $('#price').attr("data-lowstock");
	if(+stock <= +low){
		$(".pop-up-attention").css("display", "block");
		$('#lowstock').text(low+" шт.");
	} //endif
	$(".pp-number").on('input', function(){
		var price = $('#price').attr("data-price");
		var q = +$(this).val();
		var stock = +$('#price').attr("data-quantidi");

		if(q > stock){
			$(".pop-up-attention").css("display", "block");
			$('#lowstock').text(stock+" шт.");
			q = stock;
			$(this).val(q);
		} //endif

		if(isInteger(q) && q >= 1){
			var result = (price * q);
		}
		else{
			result = price;
		}
		$('#price').text(result+" р.");
	}); //end input
	$(".pp-cross-number").on('input', function(){
		var id = $(this).attr("data-cross_product");
		var price = $('#cross_price'+id).attr("data-cross_price");
		var q = +$(this).val();
		var stock = +$(this).attr("data-quantity");
		if(q > stock){
			$(".pop-up-attention").css("display", "block");
			$('#lowstock').text(stock+" шт.");
			q = stock;
			$(this).val(q);
		} //endif

		if(isInteger(q) && q >= 1){
			var result = (price * q);
		}
		else{
			result = price;
		}
		$('#cross_price'+id).text(result+" р.");

	}); //end input
	$("[data-product_add]").on('click', function(){
		var id = $(this).attr("data-product_add");
		var q = $('#count').val();
		$.ajax({
			type: 'GET',
			url: site_url + '/wp-admin/admin-ajax.php',
			data: {
				id: id,
				q: q,
				action: 'addtocart'
			},
			cache: false,
			dataType: "json",
			error: function(error){
				alert("error");
			},
			success: function(data){
				$('#open_product_basket').text(data.quantity);
				$('#col_product').text(data.unique_quantity);
				if(data.product != ''){
					$('#all_product').html(data.product);
				} //endif
				if(data.price != ''){
					$('#all_price').text(data.price);
				} //endif
				if(data.status == 'success'){
					$('#basket_url').attr("href", data.link);
					$('#my_checkout_btn').css("display", "block");
				}
			} //endsuccess
		}); //endajax
	}); //end click
	//купить в один клик
	$("[data-oneclick]").on('click', function(){
		var id = $(this).attr("data-oneclick");
		var q = $('#count').val();
		$.ajax({
			type: 'GET',
			url: site_url + '/wp-admin/admin-ajax.php',
			data: {
				id: id,
				q: q,
				action: 'buyoneclick'
			},
			cache: false,
			dataType: "json",
			error: function(error){
				alert("error");
			},
			success: function(data){
				if(data.status == 'success'){
					window.location.assign(data.data);
				}

			} //endsuccess
		}); //endajax
	}); //end click

	$("[data-cross_add]").on('click', function(){
		var id = $(this).attr("data-cross_add");
		var q = $('#cross_product'+id).val();
		$.ajax({
			type: 'GET',
			url: site_url + '/wp-admin/admin-ajax.php',
			data: {
				id: id,
				q: q,
				action: 'addtocart'
			},
			cache: false,
			dataType: "json",
			error: function(error){
				alert("error");
			},
			success: function(data){
				$('#open_product_basket').text(data.quantity);
				$('#col_product').text(data.unique_quantity);
				if(data.product != ''){
					$('#all_product').html(data.product);
				} //endif
				if(data.price != ''){
					$('#all_price').text(data.price);
				} //endif
				if(data.status == 'success'){
					$('#basket_url').attr("href", data.link);
					$('#my_checkout_btn').css("display", "block");
				}
			} //endsuccess
		}); //endajax

	}); //end click

}); //endready
function isInteger(q) {
	return (q ^ 0) === q;
}