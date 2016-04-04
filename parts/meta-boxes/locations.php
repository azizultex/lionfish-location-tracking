<?php
/*
Title: Location Info
Post Type: lionfish_locations
*/

piklist('field', array(
 'type' => 'text'
 ,'field' => 'location'
 ,'label' => __('Location', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'text'
 ,'field' => 'lat'
 ,'label' => __('Lat', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'text'
 ,'field' => 'long'
 ,'label' => __('Long', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'number'
 ,'field' => 'lionfish_number'
 ,'label' => __('Number of Lionfish', 'lionfish')
 ,'columns' => 6
));

piklist('field', array(
 'type' => 'datepicker'
 ,'field' => 'date'
 ,'label' => __('Date', 'lionfish')
));

