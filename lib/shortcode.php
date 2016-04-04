<?php
function lionfish_custom_post_form($atts) {
    ob_start();
?>
    <?php if(is_user_logged_in()) : ?>

        <div class="lionfish">
            <p class="status"></p>
            <form id="lionfish-post-form" action="">

                <label for="location">Location:</label>
                <input type="text" name="location" id="location">

                <label for="lat">Lat:</label>
                <input type="text" name="lat" id="lat">

                <label for="long">long:</label>
                <input type="text" name="long" id="long">

                <?php
                wp_dropdown_categories( array(
                    'taxonomy'      => 'lionfish_layers',
                    'hide_empty'    => 0,
                    'orderby'       => 'name',
                    'order'         => 'ASC',
                    'name'          => 'lionfish_layers',
                ) );
                ?>

                <label for="number">Number of Lionfish:</label>
                <input type="number" name="fish_number" id="fish_number">

                <label for="dat">Date:</label><br>
                <input type="date" name="date" id="date">

                <input type="submit" name="submit" value="Post location" class="submit-button">
            </form>
        </div>

        <?php else: ?>

        <p>Please <a href="<?php echo wp_login_url( get_permalink());  ?>">Login</a> to submit lionfish location </p>

    <?php endif; ?>

<?php

    }

add_shortcode('lionfish_form', 'lionfish_custom_post_form');


function gmap_show() {
    return '<div id="map-container">
              <div id="map"></div>
            </div>';
}

add_shortcode('gmap_show', 'gmap_show');

?>