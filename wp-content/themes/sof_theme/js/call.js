var site_url = document.location.origin;

$(document).ready(function(){
	var title = $('#sale_title').attr("data-sale");
	$('#sale_name').val(title);
$('#call_btn').on('click', function(){
            let callform = new FormData($('#call_form')[0]);
$.ajax({
type: 'POST',
url: site_url + '/wp-admin/admin-ajax.php',
                contentType: false,
                processData: false,
data: callform,
dataType: "json",
error: function(error){
alert("error");
},
success: function(data){
		if(data.status == 'success'){
														  $("#call_btn").prop("disabled", true);
														      $('.pop-up-sale').css('display','none');
    $('.pop-up-post-sale').css('display','block');
    $('.bg-black').css('display','block');
	} //endif
//c $('#feedback_result').html(data.data);
} //endsuccess
}); //endajax

}); //endclick

}); //endready