<?php 

/*
Title: Social Media Setting
Setting: worksfire_setting
Tab: Social
Tab Order: 40
*/

/*
 The social links added here will be shown everywhere in the site.
*/
piklist('field', array(
    'type' => 'group'
    ,'field' => 'social_media'
    ,'columns' => 12
    ,'add_more' => true
    ,'fields' => array(
	
          array(
			'type' => 'select'
			,'field' => 'icon_class'
			,'value' => 'social_facebook'
		    ,'columns' => 4
			,'choices' => array(
			  'rss' => 'RSS'
			  ,'facebook' => 'Facebook'
			  ,'twitter' => 'Twitter'
			  ,'vimeo' => 'Vidmeo'
			  ,'googleplus' => 'Google Plus'
			  ,'pintrest' => 'Pinterest'
			  ,'linkedin' => 'LinkedIn'
			  ,'skype' => 'Skype'
			  ,'dropbox' => 'Dropbox'
			  ,'picasa' => 'Picasa'
			  ,'spotify' => 'Spotify'
			  ,'jolicloud' => 'Jolicloud'
			  ,'wordpress' => 'Wordpress'
			  ,'github' => 'Github'
			  ,'xing' => 'Xing'
			)
			)
          ,array(
            'type' => 'url'
            ,'field' => 'link'
            ,'columns' => 8
            ,'attributes' => array(
              'placeholder' => 'https://www.facebook.com/worksfireinc'
            )
          ),
    )
  ));	