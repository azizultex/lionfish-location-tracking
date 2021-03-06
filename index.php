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

// Is ABSPATH defined?
if ( !defined('ABSPATH') )
    die('-1');

/*
 * Useful constants
 */
define( 'LIONFISH_PLUGINURL', plugins_url(basename( dirname(__FILE__))) . "/");
define( 'LIONFISH_PLUGINPATH', dirname(__FILE__) . "/");
define( 'LIONFISH_DOMAIN', "lionfish");
define( 'LIONFISH_VERSION', "1.0");

/* text domain */
//load_plugin_textdomain(LIONFISH_DOMAIN, LIONFISH_PLUGINPATH . 'lang' );

/* required files */
require_once( LIONFISH_PLUGINPATH . 'lib/class-tgm-plugin-activation.php' );
require_once( LIONFISH_PLUGINPATH . 'lib/required_plugins.php' );
require_once( LIONFISH_PLUGINPATH . 'lib/custom-post-type.php' );
require_once( LIONFISH_PLUGINPATH . 'lib/shortcode.php' );


function private_posts() {
    $args = array (
        'post_type' => 'lionfish_locations', // post type
        'post_status' => 'publish',
        'nopaging' => true
    );
    $q = new WP_Query ($args);
    while ($q->have_posts()) {
        $q->the_post();
        $id = get_the_ID();
        $location_type = get_post_meta( get_the_ID(), 'location_type', true );
        $days = round((date('U') - get_the_time('U')) / (60*60*24)); // https://mor10.com/add-a-twitter-like-timestamp-to-your-wordpress-posts/

        if($location_type == 'spotted' ) {
            if($days >= 30 ) {  // days to delete posts after published
                $post_private = array( 'ID' => $id, 'post_status' => 'private' );
                wp_update_post($post_private);

            }
        } else {
            if($days >= 365 ) {  // days to delete posts after published
                $post_private = array( 'ID' => $id, 'post_status' => 'private' );
                wp_update_post($post_private);
            }
        }

    }
    wp_reset_postdata ();
}

add_action( 'init', 'private_posts' );
//add_filter('show_admin_bar', '__return_false');

// extend admin search
add_filter('posts_join', 'segnalazioni_search_join' );
function segnalazioni_search_join ($join){
    global $pagenow, $wpdb;
    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='lionfish_locations' && $_GET['s'] != '') {    
        $join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}

add_filter( 'posts_where', 'segnalazioni_search_where' );
function segnalazioni_search_where( $where ){
    global $pagenow, $wpdb;
    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='lionfish_locations' && $_GET['s'] != '') {
        $where = preg_replace(
       "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
       "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }
    return $where;
}



// load scripts
function lionfish_location_scripts() {
    $q = new WP_Query(
        array('posts_per_page' => -1, 'post_type' => 'lionfish_locations', 'post_status' => 'publish', 'order'=> 'ASC')
    );

    $post_data = array();

    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {

            $q->the_post();

            $location_type      = get_post_meta( get_the_ID(), 'location_type', true );
            $location           = get_post_meta( get_the_ID(), 'location', true );
            $lat                = get_post_meta( get_the_ID(), 'lat', true );
            $long               = get_post_meta( get_the_ID(), 'long', true );
            $time               = get_post_meta( get_the_ID(), 'time', true );
            $date               = get_post_meta( get_the_ID(), 'date', true );
            $depth              = get_post_meta( get_the_ID(), 'depth', true );
            $lionfish_number    = get_post_meta( get_the_ID(), 'lionfish_number', true );

            $single_data = array();
            $single_data['location_type'] = $location_type;
            $single_data['location'] = $location;
            $single_data['lat'] = $lat;
            $single_data['long'] = $long;
            $single_data['time'] = $time;
            $single_data['date'] = $date;
            $single_data['depth'] = $depth;
            $single_data['lionfish_number'] = $lionfish_number;

            $post_data[] = $single_data;

        }
    }

    wp_reset_postdata();

    /* ajaxify the post submit */
    wp_enqueue_style('jquerymodal', LIONFISH_PLUGINURL . 'assets/css/jquery.modal.css');
    wp_enqueue_style('lionfish_style', LIONFISH_PLUGINURL . 'assets/css/lionfish_styles.css');
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');
    wp_enqueue_script('jquerymodal', LIONFISH_PLUGINURL . 'assets/js/jquery.modal.js', array('jquery') );
    wp_enqueue_script('gmap_api', 'https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true', array('jquery') );
    wp_enqueue_script('gmap_cluster', LIONFISH_PLUGINURL . 'assets/js/gmap/markerclusterer.js', array('jquery') );  
   // wp_enqueue_script('sharer', '//cdn.jsdelivr.net/sharer.js/latest/sharer.min.js', array('jquery') );
    wp_enqueue_script('gmap_setting', LIONFISH_PLUGINURL . 'assets/js/gmap/setting.js', array('jquery') );
    wp_localize_script( 'gmap_setting', 'lionfish_locations', $post_data );
    wp_enqueue_script('ajax_js', LIONFISH_PLUGINURL . 'assets/js/ajax-post-submit.js', array('jquery') );
    wp_localize_script( 'ajax_js', 'ajax_post_obj', array(
        'LIONFISH_PLUGINURL' => LIONFISH_PLUGINURL,
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));
}

add_action( 'wp_enqueue_scripts', 'lionfish_location_scripts' );



// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_ajaxlocation', 'ajax_location' );
add_action( 'wp_ajax_nopriv_ajaxlocation', 'ajax_location' );


function ajax_location(){

    $notice = '';

    $location_type  = sanitize_text_field($_POST['location_type']);
    $location       = sanitize_text_field($_POST['location']);
    $lat            = $_POST['lat'];
    $long           = $_POST['long'];
    $time           = sanitize_text_field($_POST['time']);
    $date           = sanitize_text_field($_POST['date']);
    $depth          = intval($_POST['depth']);
    $fish_number    = intval($_POST['fish_number']);



    $post_info = array(
        'post_type' => 'lionfish_locations',
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    );
    if ( empty($location_type)) {
        $notice = 'Please select a location type';
    } else if ( empty($lat) ) {
        $notice = 'Lat value required';
    } else if ( empty($long) ) {
        $notice = 'Long value required';
    }  else if ( empty($time)) {
        $notice = 'Time is required';
    } else if ( empty($date)) {
        $notice = 'Date is also required';
    } else if ( empty($depth)) {
        $notice = 'Depth is required in metres';
    } else if ( empty($fish_number) ) {
        $notice = 'Fish number value required';
    } else if ( is_nan($fish_number) ) {
        $notice = 'Numeric fish value required';
    } else {
        $post_id = wp_insert_post($post_info);
        $notice = 'Location submitted successfully!';
    }

    if ($post_id) {
        /* submit posts meta */
        add_post_meta($post_id, 'location_type', $location_type);
        add_post_meta($post_id, 'location', $location);
        add_post_meta($post_id, 'lat', $lat);
        add_post_meta($post_id, 'long', $long);
        add_post_meta($post_id, 'time', $time);
        add_post_meta($post_id, 'date', $date);
        add_post_meta($post_id, 'depth', $depth);
        add_post_meta($post_id, 'lionfish_number', $fish_number);
    }

    echo json_encode($notice);

    exit();
}