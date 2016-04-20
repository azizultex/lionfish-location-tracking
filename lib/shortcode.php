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
        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
        <div id="map" style="width:<?php echo $width; ?>;height: <?php echo $height; ?>"></div>
        <div id="filter">
            <select id="lionfish_layers">
                <option value="all">Show all</option>
                <option value="spotted">Spotted</option>
                <option value="removed">Removed</option>
            </select>
        </div>
    </div>

<div id="jQuerymodal" class="login_form modal" style="display:none">
    <h3>New LionFish Location</h3>
        <div class="lionfish">
            <p class="status" style="display: none"></p>
            <form id="lionfish-post-form" action="">

                <label for="location_type">Location type:</label>
                <input type="radio" name="location_type" value="spotted"> Spotted
                <input type="radio" name="location_type" value="removed"> Removed <br/>

                <label for="location">Location name:</label>
                <input type="text" name="location" id="location">

                <label for="lat">Latitude:</label>
                <input type="text" name="lat" id="lat">

                <label for="long">Longitude:</label>
                <input type="text" name="long" id="long">

                <label for="long">Time:</label>
                <input type="time" name="time" id="time">

                <label for="dat">Date:</label>
                <input type="date" name="date" id="date">

                <label for="dat">Depth in metres:</label>
                <input type="number" name="depth" id="depth">

                <label for="number">Number of Lionfish:</label>
                <input type="number" name="fish_number" id="fish_number">


                <input type="submit" name="submit" value="Post location" class="submit-button">
            </form>
        </div>
</div>

<?php

    }

add_shortcode('lionfish_form', 'lionfish_custom_post_form');


?>