var site_url = document.location.origin;

$(document).ready(function(){
    $(".pp-show-order").on('click', show_order_qr);
    $('#save_pass').on('click', function(){
        var s = '';
        var oldPas = document.getElementById('oldPas').value;
        var newPas = document.getElementById('newPas').value;
        if(oldPas === newPas || oldPas === s || newPas === s){
            $('.errorPas').css('display','block');
            $('.bg-black').css('display','block');
        }
        else {

            var acform = $('#pass_form').serialize();

            $.ajax({
                type: 'POST',
                url: site_url + '/wp-admin/admin-ajax.php',
                data: acform,
                cache: false,
                dataType: "json",
                error: function(error){
                    alert("error");
                },
                success: function(data){
                    if(data.status == 'success'){
                        $('.successPas').css('display','block');
                        $('.bg-black').css('display','block');
                    } //endif
                    else{
                        $('.errorPas').css('display','block');
                        $('.bg-black').css('display','block');
                    } //endelse
                } //endsuccess
            }); //endajax
        } //endelse
    }); //endclick
    $('#save_email').on('click', function(){
        var acform = $('#email_form').serialize();


        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: acform,
            cache: false,
            dataType: "json",
            error: function(error){
                alert("error");
            },
            success: function(data){
                if(data.status == 'success'){
//c$('#add_result').html(data.data);
                    $('.errorEmail').css('display','block');
                    $('.bg-black').css('display','block');
                } //endif
            } //endsuccess
        }); //endajax
    }); //endclick
    $('#save_phone').on('click', function(){
        var acform = $('#phone_form').serialize();


        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: acform,
            cache: false,
            dataType: "json",
            error: function(error){
                alert("error");
            },
            success: function(data){
                $('#your_phone').text(data.data);

            } //endsuccess
        }); //endajax
    }); //endclick
    $('#save_address').on('click', function(){
        var acform = $('#address_form').serialize();


        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: acform,
            cache: false,
            dataType: "json",
            error: function(error){
                alert("error");
            },
            success: function(data){
                if(data.status == 'success'){
//c		$('#add_result').html(data.data);

                    window.location.assign(data.data);
                }
                else{
                    $('#add_result').html(data.data);
                }
            } //endsuccess
        }); //endajax
    }); //endclick

}); //endready
function show_order_qr(){
    $('#hid_qr').css("display", "block");
} //endfunction
