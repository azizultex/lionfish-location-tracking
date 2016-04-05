<?php
function lionfish_custom_post_form($atts) {

    extract(shortcode_atts(array(
        'width' => '100%',
        'height' => '400px'
    ), $atts));

    ob_start();
?>

    <a href="#jQuerymodal" rel="modal:open" class="btn lionfish-btn">Add new location</a>

    <div id="map-container">
        <div id="map" style="width:<?php echo $width; ?>;height: <?php echo $height; ?>"></div>
        <div id="filter">
            <?php
            wp_dropdown_categories( array(
                'taxonomy'      => 'lionfish_layers',
                'hide_empty'    => 0,
                'orderby'       => 'name',
                'order'         => 'ASC',
                'name'          => 'lionfish_layers',
            ) );
            ?>
        </div>
    </div>

<div id="jQuerymodal" class="login_form modal" style="display:none">
    <h3>New LionFish Location</h3>
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

                <label for="lat">Layer:</label>
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

                <label for="dat">Date:</label>
                <input type="date" name="date" id="date"> <br/>

                <input type="submit" name="submit" value="Post location" class="submit-button">
            </form>
        </div>

    <?php else: ?>
        <p>Please <a href="<?php echo wp_login_url( get_permalink());  ?>">Login</a> to submit lionfish location </p>
    <?php endif; ?>
</div>

<?php

    }

add_shortcode('lionfish_form', 'lionfish_custom_post_form');


?>