jQuery(document).ready(function($) {

    // Perform AJAX login on form submit
    $('form#lionfish-post-form').on('submit', function (e) {
        e.preventDefault();
        $('.lionfish p.status').text('Location is submitting...');

        data = {
            'action': 'ajaxlocation', //calls wp_ajax_nopriv_ajaxlogin
            'location': $('form#lionfish-post-form #location').val(),
            'lat': $('form#lionfish-post-form #lat').val(),
            'long': $('form#lionfish-post-form #long').val(),
            'lionfish_layers': $('form#lionfish-post-form #lionfish_layers').val(),
            'fish_number': $('form#lionfish-post-form #fish_number').val(),
            'date': $('form#lionfish-post-form #date').val(),
        };

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_post_obj.ajaxurl,
            data: data,
            success: function (res) {
                 $('.lionfish p.status').text(res);
                 if( res == 'Location submitted successfully!' ) {
                     $('form#lionfish-post-form')[0].reset();
                 }
                },
            fail: function(res) {
                $('.lionfish p.status').text(res);
            }
        });

    });
});