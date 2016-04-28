jQuery(document).ready(function($) {

    // Perform AJAX login on form submit
    $('form#lionfish-post-form').on('submit', function (e) {
        e.preventDefault();
        $('.lionfish p.status').css('display', 'block').text('Location is submitting...');

        data = {
            'action': 'ajaxlocation', //calls wp_ajax_nopriv_ajaxlogin
            'location_type': $('form#lionfish-post-form input[name=location_type]:checked').val(),
            'location': $('form#lionfish-post-form #location').val(),
            'lat': $('form#lionfish-post-form #lat').val(),
            'long': $('form#lionfish-post-form #long').val(),
            'time': $('form#lionfish-post-form #time').val(),
            'date': $('form#lionfish-post-form #date').val(),
            'depth': $('form#lionfish-post-form #depth').val(),
            'fish_number': $('form#lionfish-post-form #fish_number').val()
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
                     $('.lionfish p.status').css('color', 'green');
                     initialize();
                 } else {
                     $('.lionfish p.status').css('color', 'red');
                 }
                },
            fail: function(res) {
                $('.lionfish p.status').css('color', 'red').text(res);
            }
        });

    });
    
    $('a[data-modal]').click(function(event) {
      $(this).modal();
      return false;
    });

});