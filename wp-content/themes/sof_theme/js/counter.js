var site_url = document.location.origin;

$(document).ready(function(){
	$('#get_filter').on('click', handler_filter);
	$('#mark-of-car').find('.dd-option').on('click', handler_slick);
	//$(".see-more").on('click', '[data-more]', handler_more);
	$(".see-more").on('click', '[data-more]', handler_filter);
	$(".pagination").on('click', '[data-page]', handler_page);
	$(".pagination").on('click', '[data-category_page]', handler_category_page);

	$(".main-catalog-table").on('input', '.pp-number', handler_input_list);
	$("tbody").on('input', '.pp-number-table', handler_input_table);
	$("tbody").on('click', '.pp-plus-btn', handler_plus_btn);
	$("tbody").on('click', '.pp-minus-btn', handler_minus_btn);
	$(".main-catalog-table").on('click', '[data-add]', handler_data_add);
	$("tbody").on('click', '[data-table_add]', handler_data_table_add);

}); //endready
function handler_input_list(){
	var id = $(this).attr("data-product");
	var price = $('#price'+id).attr("data-price");
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
	$('#price'+id).text(result+" р.");

} //endfunction
function handler_input_table(){
	var id = $(this).attr("data-table_product");
	var price = $('#table_price'+id).attr("data-table_price");
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
	$('#result_price'+id).text(result+" р.");
} //endfunction
function handler_plus_btn(){
	var id = $(this).attr("data-plus_btn");
	var price = $('#table_price'+id).attr("data-table_price");
	q = +$('#input_product'+id).val();
	var stock = +$('#input_product'+id).attr("data-quantity");
	if(q > stock){
		$(".pop-up-attention").css("display", "block");
		$('#lowstock').text(stock+" шт.");
		q = stock;
		$('#input_product'+id).val(q);
	} //endif
	if(isInteger(q) && q >= 1){
		var result = (price * q);
	}
	else{
		result = price;
	}
	$('#result_price'+id).text(result+" р.");

} //endfunction
function handler_minus_btn(){
	var id = $(this).attr("data-minus_btn");
	var price = $('#table_price'+id).attr("data-table_price");
	var q = +$('#input_product'+id).val();
	if(isInteger(q) && q >= 1){
		var result = (price * q);
	}
	else{
		result = price;
	}
	$('#result_price'+id).text(result+" р.");
} //endfunction
function handler_data_add(){
	var id = $(this).attr("data-add");
	var q = $('#list_input_product'+id).val();
	ajax_add_cart(id, q);
} //endfunction
function handler_data_table_add(){
	var id = $(this).attr("data-table_add");
	var q = $('#input_product'+id).val();
	ajax_add_cart(id, q);
} //endfunction
function ajax_add_cart(id, q){
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
} //endfunction
function handler_slick(){
	var slick = $('#mark-of-car').find('.dd-selected-value').val();
	$.ajax({
		type: 'GET',
		url: site_url + '/wp-admin/admin-ajax.php',
		data: {
			car: slick,
			action: 'resizecars'
		},
		cache: false,
		dataType: "json",
		error: function(error){
			alert("error");
		},
		success: function(data){
			$('#model').html(data.models);
			$('#year').html(data.years);
			$('#litr').html(data.engines);


		} //endsuccess
	}); //endajax

} //endfunction
/*function handler_filter(){
	var url = $(this).attr("data-url") + "?filter=Y";

	var categories = '';

	var motoroil='';
	$( "[name=motor-oil]:checked").each(function() {
		motoroil+= '-' + $( this ).attr("data-subcategory");
	});
//    $("[name=motor-oil]:checked").attr("data-subcategory");
	if(typeof motoroil != 'undefined'){
		categories += motoroil;
	}

	var liquid='';
	$( "[name=liquid]:checked").each(function() {
		liquid+= '-' + $( this ).attr("data-subcategory");
	});
//    $("[name=liquid]:checked").attr("data-subcategory");
	if(typeof liquid != 'undefined'){
		categories += liquid;
	}

	var transoil='';
	$( "[name=trans-oil]:checked").each(function() {
		transoil+= '-' + $( this ).attr("data-subcategory");
	});
//    $("[name=trans-oil]:checked").attr("data-subcategory");
	if(typeof transoil != 'undefined'){
		categories += transoil;
	}

	var stop='';
	$( "[name=stop]:checked").each(function() {
		stop+= '-' + $( this ).attr("data-subcategory");
	});
//    $("[name=stop]:checked").attr("data-subcategory");
	if(typeof stop != 'undefined'){
		categories += stop;
	}

	var filter='';
	$("[name=filter]:checked").each(function() {
		filter+= '-' + $( this ).attr("data-subcategory");
	});
//    $("[name=filter]:checked").attr("data-subcategory");
	if(typeof filter != 'undefined'){
		categories +=filter;
	}

	if(categories == ''){
		categories = 0;
	} //endif
	url += '&categories='+categories;

	var car = $("#mark-of-car").find(".dd-selected-value").val();
	if(car != ''){
		url += '&car='+car;
	} //endif
	var models = $("#model").find(".dd-selected-value").val();
	if(typeof models == 'undefined'){
		models = $('#my_models').val();
	}
	if(models != ''){
		url += '&models='+models;
	} //endif
	var years = $("#year").find(".dd-selected-value").val();
	if(typeof years == 'undefined'){
		years = $('#my_years').val();
	}
	if(years != ''){
		url += '&years='+years;
	} //endif
	var engines = $("#litr").find(".dd-selected-value").val();
	if(typeof engines == 'undefined'){
		engines = $('#my_engines').val();
	}
	if(engines != ''){
		url += '&engines='+engines;
	} //endif
	var pickup = $("#location").find(".dd-selected-value").val();
	if(pickup != ''){
		url += '&pickup='+pickup;
	} //endif
	var price = $("#price").find(".dd-selected-value").val();
	if(price != ''){
		url += '&price='+price;
	} //endif

	console.log(url);

	$('#add_result').text(url);

	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		error: function(error){
			alert("error");
		},
		beforeSend: function () {
			$('body').addClass('page-load');
		},
		success: function(data){
			$(".main-catalog-table").html($(data).find(".main-catalog-table").html());
			$("tbody").html($(data).find("tbody").html());
			$(".see-more").html($(data).find(".see-more").html());
			$(".pagination").html($(data).find(".pagination").html());
			$('body').removeClass('page-load');

		} //endsuccess
	}); //endajax
} //endfunction*/



function handler_filter(){
	var url = $(this).attr("data-url") + "?filter=Y";
	var data_more = $(this).attr("data-more");



	var categories = '';



	var motoroil_new = [];
	var motoroil='';
	$( "[name=motor-oil]:checked").each(function() {
		motoroil+= '-' + $( this ).attr("data-subcategory");
		motoroil_new.push($( this ).attr("data-subcategory"));
	});
//    $("[name=motor-oil]:checked").attr("data-subcategory");


	if(typeof motoroil != 'undefined'){
		categories += motoroil;
	}


	var liquid_new = [];
	var liquid='';
	$( "[name=liquid]:checked").each(function() {
		liquid+= '-' + $( this ).attr("data-subcategory");
		liquid_new.push($( this ).attr("data-subcategory"));
	});
//    $("[name=liquid]:checked").attr("data-subcategory");
	if(typeof liquid != 'undefined'){
		categories += liquid;
	}

	var transoil_new = [];
	var transoil='';
	$( "[name=trans-oil]:checked").each(function() {
		transoil+= '-' + $( this ).attr("data-subcategory");
		transoil_new.push($( this ).attr("data-subcategory"));
	});
//    $("[name=trans-oil]:checked").attr("data-subcategory");
	if(typeof transoil != 'undefined'){
		categories += transoil;
	}

	var antifreeze_new = [];
	var antifreeze='';
	$( "[name=antifreeze]:checked").each(function() {
		antifreeze+= '-' + $( this ).attr("data-subcategory");
		antifreeze_new.push($( this ).attr("data-subcategory"));
	});
//    $("[name=trans-oil]:checked").attr("data-subcategory");
	if(typeof antifreeze != 'undefined'){
		categories += antifreeze;
	}

	var stop_new = [];
	var stop='';
	$( "[name=stop]:checked").each(function() {
		stop+= '-' + $( this ).attr("data-subcategory");
		stop_new.push($( this ).attr("data-subcategory"));
	});
//    $("[name=stop]:checked").attr("data-subcategory");
	if(typeof stop != 'undefined'){
		categories += stop;
	}

	var filter_new = [];
	var filter='';
	$("[name=filter]:checked").each(function() {
		filter+= '-' + $( this ).attr("data-subcategory");
		filter_new.push($( this ).attr("data-subcategory"));
	});
//    $("[name=filter]:checked").attr("data-subcategory");
	if(typeof filter != 'undefined'){
		categories +=filter;
	}

	if(categories == ''){
		categories = 0;
	} //endif
	url += '&categories='+categories;











	var car = $("#mark-of-car").find(".dd-selected-value").val();
	if(car != ''){
		url += '&car='+car;
	} //endif
	var models = $("#model").find(".dd-selected-value").val();
	if(typeof models == 'undefined'){
		models = $('#my_models').val();
	}
	if(models != ''){
		url += '&models='+models;
	} //endif
	var years = $("#year").find(".dd-selected-value").val();
	if(typeof years == 'undefined'){
		years = $('#my_years').val();
	}
	if(years != ''){
		url += '&years='+years;
	} //endif
	var engines = $("#litr").find(".dd-selected-value").val();
	if(typeof engines == 'undefined'){
		engines = $('#my_engines').val();
	}
	if(engines != ''){
		url += '&engines='+engines;
	} //endif










	var pickup = $("#location").find(".dd-selected-value").val();
	var pickup_new = '';
	if(pickup != ''){
		url += '&pickup='+pickup;
		pickup_new = pickup;
	} //endif



	var price = $("#price").find(".dd-selected-value").val();
	var price_new = '';
	if(price != ''){
		url += '&price='+price;
		price_new = price;
	} //endif

	var per_page_new = $("#per_page_new").val();
	var number_page_new = $("#number_page_new").val();
	var cat_id_new = $("#cat_id_new").val();

	if(typeof data_more != 'undefined'){
		number_page_new = Number(number_page_new) + 1;		
	}else{
		$("#number_page_new").val(1);
		number_page_new = 1;
	}
	
	$.ajax({
		type: 'POST',
		url: site_url + '/wp-admin/admin-ajax.php',
		data: {
			action: 'filterest_new',
			per_page_new: per_page_new,
			number_page_new: number_page_new,
			cat_id_new: cat_id_new,
			motoroil_new: motoroil_new,
			antifreeze_new:antifreeze_new,
			liquid_new:liquid_new,
			transoil_new:transoil_new,
			stop_new:stop_new,
			filter_new:filter_new,
			pickup_new:pickup_new,
			price_new:price_new,
			car:car,
			models:models,
			years:years,
			engines:engines,
		},
		cache: false,
		dataType: "json",
		error: function(error){
			alert("error");
		},
		beforeSend: function () {
			$('body').addClass('page-load');
		},
		success: function(data){
			// $(".main-catalog-table").html($(data).find(".main-catalog-table").html());
			// $("tbody").html($(data).find("tbody").html());
			// $(".see-more").html($(data).find(".see-more").html());
			// $(".pagination").html($(data).find(".pagination").html());
			// $('body').removeClass('page-load');
			if(typeof data_more != 'undefined'){
				$(".main-catalog-table").html($(".main-catalog-table").html() + data.products);
				$("#number_page_new").val(number_page_new);	
			}else{
				$(".main-catalog-table").html(data.products);
			}
			

			$('body').removeClass('page-load');
		} //endsuccess
	}); //endajax
} //endfunction






function handler_more(){
	var url = $(this).attr("data-more");
	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		error: function(error){
			alert("error");
		},
		success: function(data){
			$(".main-catalog-table").append($(data).find(".main-catalog-table").html());
			$("tbody").append($(data).find("tbody").html());
			$(".see-more").html($(data).find(".see-more").html());
			$(".pagination").html($(data).find(".pagination").html());
		} //endsuccess
	}); //endajax

} //endfunction




function handler_page(){
	var url = $(this).attr("data-page");
	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		error: function(error){
			alert("error");
		},
		success: function(data){
			$(".main-catalog-table").html($(data).find(".main-catalog-table").html());
			$("tbody").html($(data).find("tbody").html());
			$(".see-more").html($(data).find(".see-more").html());
			$(".pagination").html($(data).find(".pagination").html());

		} //endsuccess
	}); //endajax

} //endfunction
function handler_category_page(){
	var url = $(this).attr("data-category_page");
	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		error: function(error){
			alert("error");
		},
		success: function(data){
			$(".main-catalog-table").html($(data).find(".main-catalog-table").html());
			$("tbody").html($(data).find("tbody").html());
			$(".see-more").html($(data).find(".see-more").html());
			$(".pagination").html($(data).find(".pagination").html());

		} //endsuccess
	}); //endajax

} //endfunction

function isInteger(q) {
	return (q ^ 0) === q;
}
//$('body').on('change','#mark-of-car',function(){
//    console.log("test");
//    let mark_car = $(this).val();
//    $.ajax({
//            url: "/wp-admin/admin-ajax.php",
//            method: 'get',
//            data: {
//                action: 'choose_mark_car',
//                mark_car: mark_car,
//            },
//            success: function (response) {
//
//                response = JSON.parse(response);
//
//                $('#model').html(response.models);
//                $('#year').html(response.years);
//                $('#litr').html(response.engines);
//                console.log('test');
//
//            }
//        });
//})