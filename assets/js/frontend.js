jQuery(document).ready(function($) {
    $('#cashu-token-submit').on('click', function(e) {
        e.preventDefault();

        var token = $('#cashu-token-input').val();

        $.ajax({
            url: wc_checkout_params.ajax_url,
            type: 'POST',
            data: {
                action: 'validate_cashu_token',
                token: token
            },
            success: function(response) {
                if (response.success) {
                    // Token is valid, proceed with payment
                } else {
                    // Display error message
                    alert(response.data.message);
                }
            }
        });
    });
});
