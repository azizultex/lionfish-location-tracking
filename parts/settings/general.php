<?php 

/*
Title: General Setting Section
Setting: worksfire_setting
*/

/* Logo upload */

piklist('field', array(
  'type' => 'file'
  ,'field' => 'workfire_logo'
  ,'label' => 'Upload Logo'
  ,'options' => array(
		'button' => __('Add Logo','worksfire')
		)
  ,'validate' => array(
	  array(
		'type' => 'limit'
		,'options' => array(
		  'min' => 1
		  ,'max' => 1
		 )
		,'message' => 'Dude, You can upload one logo only'
		)
		)
	)
);


/* copryright text */

piklist('field', array(
 'type' => 'textarea'
 ,'field' => 'copyright'
 ,'label' => 'Copyright Text'
 ,'description' => ''
 ,'value' => '<ul><li>First Item</li><li>Second Item</li></ul>'
 ));