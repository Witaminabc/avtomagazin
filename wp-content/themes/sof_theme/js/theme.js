var site_url = document.location.origin;

$(document).ready(function(){

$("[data-car]").on('click', get_cars);
$('#log_btn').on('click', function(){
var logform = $('#log_form').serialize();

$.ajax({
type: 'POST',
url: site_url + '/wp-admin/admin-ajax.php',
data: logform,
dataType: "json",
error: function(error){
alert("error");
},
success: function(data){
if(data.status == 'success'){
window.location.assign(data.data);
}
else{
	              $('.errorLogPas').css('display','block');
} //endelse
} //endsuccess
}); //endajax
}); //endclick
$('#entity_reg_btn').on('click', function(){
var regform = $('#entity_reg_form').serialize();

$.ajax({
type: 'POST',
url: site_url + '/wp-admin/admin-ajax.php',
data: regform,
dataType: "json",
error: function(error){
alert("error");
},
success: function(data){
	if(data.status == 'success'){
													  $("#entity_reg_btn").prop("disabled", true);
    $('.popUp-registration').css('display','none');
//c    $('.popUpThank').css('display','block');
    $('.popUpLogin').css('display','block');

	} //endif
	else{
//c$('#add_result').html(data.data);
	}
} //endsuccess
}); //endajax

}); //endclick
$('#phiz_reg_btn').on('click', function(){
var regform = $('#phiz_reg_form').serialize();

$.ajax({
type: 'POST',
url: site_url + '/wp-admin/admin-ajax.php',
data: regform,
dataType: "json",
error: function(error){
alert("error");
},
success: function(data){
	if(data.status == 'success'){
													  $("#phiz_reg_btn").prop("disabled", true);
    $('.popUp-registration').css('display','none');
    $('.popUpThank').css('display','block');
    $('.popUpLogin').css('display','none');
  	} //endif
	else{
//c$('#add_result').html(data.data);
	}
} //endsuccess
}); //endajax

}); //endclick

}); //endready
function get_cars(){
	
	

	var more = $(this).attr("data-car");
	var count = $(this).attr("data-count");
	$.ajax({
		type: 'GET',
		url: site_url + '/wp-admin/admin-ajax.php',
		data: {
			more: more,
			count: count,
			action: 'cars'
		},
		cache: false,
		error: function(error){
			//alert("error");
		},
		success: function(data){
			$('#all_cars').html(data);
			$("[data-car]").on('click', get_cars);

		} //endsuccess
	}); //endajax

} //end
