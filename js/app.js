$('#alert-download').show();

$('.open-down').on('click',function(e){
    e.preventDefault();
    $('#alert-download').hide();

    $('#download-modal').modal('show');
});

$('.licence-btn').on('click',function(e){
    e.preventDefault();
    
    $('#licence-modal').modal('show');
});


$('.envoyer-lien').on('click',function(e){
    e.preventDefault();

    var data = {
        email: $('#download-email').val()
    };

    $.post('index.php?action=download',data)
    .done(function(success){
        var result = JSON.parse(success);

        if(result.success){
            $('#alert-download').removeClass('alert-danger')
            .addClass('alert-success')
            .html(result.message).show();
            return;
        }else{
            $('#alert-download').removeClass('alert-success')
            .addClass('alert-danger')
            .html(result.message)
            .show();
        }
        return;
    })
    .fail(function(error){

    });
})

$('.btn-customer').on('click',function(e){
    e.preventDefault();

    var data = {
        name:$('.customer-name').val(),
        email:$('.customer-mail').val(),
        adresse:$('.customer-adresse').val(),
        telephone:$('.customer-phone').val(),
        type:$('.customer-type').val(),
        message:$('.customer-message').val()
    };

    $.post('index.php?action=order',data)
    .done(function(success){
        var result = JSON.parse(success);
        console.log(result);
        if(result.success){
            $('#alert-register').removeClass('alert-danger')
            .addClass('alert-success')
            .html(result.message).show();
            return;
        }else{
            var message  = result.message;

            // // for
            // $('#alert-register').removeClass('alert-success')
            // .addClass('alert-danger').show();
        }
        return;
    })
    .fail(function(error){

    });

});