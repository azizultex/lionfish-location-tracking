<?php

function load_lionfish_data() {
    $q = new WP_Query(
        array('posts_per_page' => -1, 'post_type' => 'lionfish_locations', 'order'=> 'ASC')
    );

    $outp = "var lionfish_data = {";
// The Loop
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {

            $q->the_post();

            $location           = get_post_meta( get_the_ID(), 'location', true );
            $lat                = get_post_meta( get_the_ID(), 'lat', true );
            $long               = get_post_meta( get_the_ID(), 'long', true );
            $lionfish_number    = get_post_meta( get_the_ID(), 'lionfish_number', true );
            $date               = get_post_meta( get_the_ID(), 'date', true );

            $layers = wp_get_post_terms( get_the_ID(), 'lionfish_layers' );
            $outp .= '["location":"'    . $location          . '",';
            $outp .= '"lat":"'          . $lat               . '",';
            $outp .= '"long":"'         . $long              . '",';
            $outp .= '"fish_number":"'  . $lionfish_number   . '",';
            $outp .= '"date":"'         . $date              . '",';
            $outp .= '"layers":"'       . $layers            . '"],';
        }
    }
    $outp .="}";


    /* Restore original Post Data */
    wp_reset_postdata();

    echo json_decode($outp);


}

add_action('init', 'load_lionfish_data');

?>

