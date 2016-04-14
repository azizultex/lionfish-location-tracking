<?php

if ( ! function_exists('lionfish_post_type') ) {

// Register Custom Post Type
    function lionfish_post_type() {

        $labels = array(
            'name'                  => _x( 'Lionfish Locations', 'Post Type General Name', 'lionfish' ),
            'singular_name'         => _x( 'Lionfish Location', 'Post Type Singular Name', 'lionfish' ),
            'menu_name'             => __( 'Lionfish Locations', 'lionfish' ),
            'name_admin_bar'        => __( 'Lionfish Locations', 'lionfish' ),
            'archives'              => __( 'Location Archives', 'lionfish' ),
            'parent_item_colon'     => __( 'Parent location:', 'lionfish' ),
            'all_items'             => __( 'All locations', 'lionfish' ),
            'add_new_item'          => __( 'Add New location', 'lionfish' ),
            'add_new'               => __( 'Add Location', 'lionfish' ),
            'new_item'              => __( 'New Location', 'lionfish' ),
            'edit_item'             => __( 'Edit Location', 'lionfish' ),
            'update_item'           => __( 'Update Location', 'lionfish' ),
            'view_item'             => __( 'View location', 'lionfish' ),
            'search_items'          => __( 'Search location', 'lionfish' ),
            'not_found'             => __( 'No location found', 'lionfish' ),
            'not_found_in_trash'    => __( 'No location found in Trash', 'lionfish' ),
            'featured_image'        => __( 'Location Image', 'lionfish' ),
            'set_featured_image'    => __( 'Set location image', 'lionfish' ),
            'remove_featured_image' => __( 'Remove location image', 'lionfish' ),
            'use_featured_image'    => __( 'Use as location image', 'lionfish' ),
            'insert_into_item'      => __( 'Insert into location', 'lionfish' ),
            'uploaded_to_this_item' => __( 'Uploaded to this location', 'lionfish' ),
            'items_list'            => __( 'locations list', 'lionfish' ),
            'items_list_navigation' => __( 'locations list navigation', 'lionfish' ),
            'filter_items_list'     => __( 'Filter location list', 'lionfish' ),
        );
        $args = array(
            'label'                 => __( 'Lionfish Location', 'lionfish' ),
            'description'           => __( 'Post Type Description', 'lionfish' ),
            'labels'                => $labels,
            'supports'              => array(''),
            'taxonomies'            => array( 'lionfish_layers' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( 'lionfish_locations', $args );

    }
    add_action( 'init', 'lionfish_post_type', 0 );

}


if ( ! function_exists( 'lionfish_layers' ) ) {

// Register Custom Taxonomy
    function lionfish_layers() {

        $labels = array(
            'name'                       => _x( 'Lionfish Layers', 'Taxonomy General Name', 'lionfish' ),
            'singular_name'              => _x( 'Lionfish Layer', 'Taxonomy Singular Name', 'lionfish' ),
            'menu_name'                  => __( 'Lionfish Layers', 'lionfish' ),
            'all_items'                  => __( 'All layers', 'lionfish' ),
            'parent_item'                => __( 'Parent layer', 'lionfish' ),
            'parent_item_colon'          => __( 'Parent layer:', 'lionfish' ),
            'new_item_name'              => __( 'New Layer Name', 'lionfish' ),
            'add_new_item'               => __( 'Add New Layer', 'lionfish' ),
            'edit_item'                  => __( 'Edit Layer', 'lionfish' ),
            'update_item'                => __( 'Update Layer', 'lionfish' ),
            'view_item'                  => __( 'View Layer', 'lionfish' ),
            'separate_items_with_commas' => __( 'Separate Layers with commas', 'lionfish' ),
            'add_or_remove_items'        => __( 'Add or remove layer', 'lionfish' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'lionfish' ),
            'popular_items'              => __( 'Popular Layers', 'lionfish' ),
            'search_items'               => __( 'Search Layers', 'lionfish' ),
            'not_found'                  => __( 'No LayerFound', 'lionfish' ),
            'no_terms'                   => __( 'No layers', 'lionfish' ),
            'items_list'                 => __( 'Layers list', 'lionfish' ),
            'items_list_navigation'      => __( 'Layers list navigation', 'lionfish' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );
        register_taxonomy( 'lionfish_layers', array( 'lionfish_locations' ), $args );

    }
    add_action( 'init', 'lionfish_layers', 0 );

}


/* Photo column in portfolio List View */

add_filter( 'manage_edit-lionfish_locations_columns', 'my_edit_lionfish_locations_columns' ) ;

function my_edit_lionfish_locations_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Title', 'lionfish' ),
        'type' => __( 'Type', 'lionfish' ),
        'location' => __( 'Location', 'lionfish' ),
        'lat' => __( 'Lat', 'lionfish' ),
        'long' => __( 'Long', 'lionfish' ),
        'layers' => __( 'Layers', 'lionfish' ),
        'number' => __( 'Number of lionfish', 'lionfish' ),
        'seen_date' => __( 'Seen Date', 'lionfish' ),
        'date' => __( 'Post Date', 'lionfish' )
    );

    return $columns;
}

add_action('manage_lionfish_locations_posts_custom_column', 'lionfish_locations_columns', 10, 2);

function lionfish_locations_columns($column, $post_id)
{

    global $post;

    switch ($column)
    {
        case 'title':
            $location = get_post_meta( get_the_ID(), 'location', true);
            echo $location;
            break;

        case 'type':
            $location = get_post_meta( get_the_ID(), 'location_type', true);
            echo $location;
            break;

        case 'location':
            $location = get_post_meta( get_the_ID(), 'location', true);
            echo $location;
            break;

        case 'lat':
            $lat = get_post_meta( get_the_ID(), 'lat', true);
            echo $lat;
            break;

        case 'long':
            $long = get_post_meta( get_the_ID(), 'long', true);
            echo $long;
            break;

        case 'layers':

            $terms = get_the_terms( $post_id, 'lionfish_layers' );
            if ( $terms && ! is_wp_error( $terms ) )  {
                $groups = array();
                foreach ( $terms as $term ) {
                    $groups[] = $term->name;
                }
                $groups = join( ", ", $groups );
            } else {
                $groups = __("No Group", 'fta');
            }

            echo $groups;
            break;

        case 'number':
            $lionfish_number = get_post_meta( get_the_ID(), 'lionfish_number', true);
            echo $lionfish_number;
            break;

        case 'seen_date':
            $date = get_post_meta( get_the_ID(), 'date', true);
            echo $date;
            break;
    }

}
