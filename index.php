<?php
/*
Plugin name: LionFish Locations
Plugin URI: http://azizultex.com
Author: Azizul Haque
Author URI: http://azizultex.com
Plugin Type: Piklist
Version: 1.0
Description: LionFish Location Tracking plugin for lionfish.co
*/

/*
 * Useful constants
 */
define( 'LIONFISH_PLUGINURL', plugins_url(basename( dirname(__FILE__))) . "/");
define( 'LIONFISH_PLUGINPATH', dirname(__FILE__) . "/");
define( 'LIONFISH_DOMAIN', "lionfish");
define( 'LIONFISH_VERSION', "1.0");

/* text domain */
load_plugin_textdomain(LIONFISH_DOMAIN, LIONFISH_PLUGINPATH . 'lang' );

/* required files */
require_once( LIONFISH_PLUGINPATH . 'lib/class-tgm-plugin-activation.php' );
require_once( LIONFISH_PLUGINPATH . 'lib/required_plugins.php' );
require_once( LIONFISH_PLUGINPATH . 'lib/custom-post-type.php' );
require_once( LIONFISH_PLUGINPATH . 'lib/shortcode.php' );

function delete_posts() {
    $args = array (
        'post_type' => 'lionfish_locations', // post type
        'nopaging' => true
    );
    $q = new WP_Query ($args);
    while ($q->have_posts()) {
        $q->the_post();
        $id = get_the_ID();
        $posted_time = human_time_diff(get_the_time('U'), current_time ('timestamp'));
        $posted = filter_var($posted_time, FILTER_SANITIZE_NUMBER_INT); // remove 'days' from posted_time
        if($posted >= 360 ) {  // days to delete posts after published
            wp_delete_post($id, true);
        }
    }
    wp_reset_postdata ();
}

add_action( 'init', 'delete_posts' );


function lionfish_location_scripts() {
    $q = new WP_Query(
        array('posts_per_page' => -1, 'post_type' => 'lionfish_locations', 'order'=> 'ASC')
    );

    $post_data = array();

    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {

            $q->the_post();

            $location           = get_post_meta( get_the_ID(), 'location', true );
            $lat                = get_post_meta( get_the_ID(), 'lat', true );
            $long               = get_post_meta( get_the_ID(), 'long', true );
            $lionfish_number    = get_post_meta( get_the_ID(), 'lionfish_number', true );
            $date               = get_post_meta( get_the_ID(), 'date', true );
            // tax
            $terms              = get_the_terms( get_the_ID(), 'lionfish_layers');

            if ( $terms && ! is_wp_error( $terms ) ) {
                $terms_ids = array();
                $terms_names = array();
                foreach ( $terms as $term ) {
                    $terms_ids[] = $term->term_id;
                    $terms_names[] = $term->name;
                }
            }


            $single_data = array();
            $single_data['location'] = $location;
            $single_data['lat'] = $lat;
            $single_data['long'] = $long;
            $single_data['lionfish_number'] = $lionfish_number;
            $single_data['date'] = $date;
            $single_data['layers_id'] = $terms_ids;
            $single_data['layers_name'] = $terms_names;

            $post_data[] = $single_data;

        }
    }

    wp_reset_postdata();

    /* ajaxify the post submit */
    wp_enqueue_style('jquerymodal', LIONFISH_PLUGINURL . 'assets/css/jquery.modal.css');
    wp_enqueue_style('lionfish_style', LIONFISH_PLUGINURL . 'assets/css/lionfish_styles.css');
    wp_enqueue_script('jquerymodal', LIONFISH_PLUGINURL . 'assets/js/jquery.modal.js', array('jquery') );
    wp_enqueue_script('gmap_api', 'https://maps.googleapis.com/maps/api/js?sensor=true&libraries=places', array('jquery') );
    wp_enqueue_script('gmap_cluster', LIONFISH_PLUGINURL . 'assets/js/gmap/markerclusterer.js', array('jquery') );
    wp_enqueue_script('gmap_setting', LIONFISH_PLUGINURL . 'assets/js/gmap/setting.js', array('jquery') );
    wp_localize_script( 'gmap_setting', 'lionfish_locations', $post_data );
    wp_enqueue_script('ajax_js', LIONFISH_PLUGINURL . 'assets/js/ajax-post-submit.js', array('jquery') );
    wp_localize_script( 'ajax_js', 'ajax_post_obj', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));
}

add_action( 'wp_enqueue_scripts', 'lionfish_location_scripts' );



// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_ajaxlocation', 'ajax_location' );
add_action( 'wp_ajax_nopriv_ajaxlocation', 'ajax_location' );

function ajax_location(){

    $notice = '';

    $location       = $_POST['location'];
    $lat            = $_POST['lat'];
    $long           = $_POST['long'];
    $fish_number    = $_POST['fish_number'];
    $date           = $_POST['date'];
    $lionfish_layers = $_POST['lionfish_layers'];
    $term = get_term( $lionfish_layers, 'lionfish_layers' );

    $post_info = array(
        'post_type' => 'lionfish_locations',
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    );

    if ( empty($location) ) {
        $notice = 'Please insert a location';
    } else if ( empty($lat) ) {
        $notice = 'Lat value required';
    } else if ( empty($long) ) {
        $notice = 'Long value required';
    } else if ( empty($fish_number) ) {
        $notice = 'Fish number value required';
    } else if ( is_nan($fish_number) ) {
        $notice = 'Numeric fish value required';
    } else if ( empty($date)) {
        $notice = 'Date is also required';
    } else {
        $post_id = wp_insert_post($post_info);
        $notice = 'Location submitted successfully!';
    }

    if ($post_id) {
        /* submit posts meta */
        add_post_meta($post_id, 'location', $location);
        add_post_meta($post_id, 'lat', $lat);
        add_post_meta($post_id, 'long', $long);
        add_post_meta($post_id, 'lionfish_number', $fish_number);
        add_post_meta($post_id, 'date', $date);

        /* submit posts taxonomy */
        wp_set_object_terms( $post_id, $term->name, 'lionfish_layers' );
    }

    echo json_encode($notice);

    exit();
}