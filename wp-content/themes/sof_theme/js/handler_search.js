var site_url = document.location.origin;

$(document).ready(function(){

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

} //endsuccess
}); //endajax

	}); //end click
}); //endready
function isInteger(q) {
  return (q ^ 0) === q;
}