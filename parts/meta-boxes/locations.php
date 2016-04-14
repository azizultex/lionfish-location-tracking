<?php
/*
Title: Location Info
Post Type: lionfish_locations
*/

piklist('field', array(
 'type' => 'radio'
 ,'field' => 'location_type'
 ,'label' => __('Location type', 'lionfish')
 ,'choices' => array(
   'spotted' => 'Spotted'
   ,'removed' => 'Removed'
 )
));

piklist('field', array(
 'type' => 'text'
 ,'field' => 'location'
 ,'label' => __('Location name', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'text'
 ,'field' => 'lat'
 ,'label' => __('Latitidue', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'text'
 ,'field' => 'long'
 ,'label' => __('Longitude', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'time'
 ,'field' => 'time'
 ,'label' => __('Time', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
    'type' => 'datepicker'
,'field' => 'date'
,'label' => __('Date', 'lionfish')
));

piklist('field', array(
    'type' => 'number'
,'field' => 'depth'
,'label' => __('Depth in metres', 'lionfish')
));

piklist('field', array(
 'type' => 'number'
 ,'field' => 'lionfish_number'
 ,'label' => __('Number of Lionfish', 'lionfish')
 ,'columns' => 6
));
