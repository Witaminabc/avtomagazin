var site_url = document.location.origin;

$(document).ready(function(){
	$("[data-add]").on('click', function(){
var id = $(this).attr("data-add");
var q = $('#list_input_product'+id).val();
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
		$("[data-table_add]").on('click', function(){
var id = $(this).attr("data-table_add");
var q = $('#input_product'+id).val();
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
