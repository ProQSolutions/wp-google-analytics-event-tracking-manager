<?php
/*
Plugin Name: WordPress GA Event Tracking
Description: Makes GA Event tracking easier
Version: 1.0
Author: Pro Q Solutions
Author URI: http://proqsolutions.com
*/

if(!is_admin()) {
    //Sample init
	$event_tracking_config = [
	  'is_debugging' => 'true',
	  'events' => [
		  [
		    'selector'=> 'h1 a',
		    'bindEvent'=> 'click',
		    'eventCategory'=> 'Click Event',
		    'eventAction'=> 'Clicked',
		    'eventLabel'=> '{text}',
		  ],
		  [
		    'selector'=> 'h1.widget-title',
		    'bindEvent'=> 'mouseenter',
		    'eventCategory'=> 'Mouse hover',
		    'eventAction'=> 'hovered',
		    'eventLabel'=> '{text}',
		  ],
		  [
		    'selector'=> '.site-info a',
		    'bindEvent'=> 'in_view',
		    'eventCategory'=> 'Visibility',
		    'eventAction'=> 'in_view',
		    'eventLabel'=> '{href}',
		  ]
	  ]
	];

    wp_register_script('proq-ga-events',plugins_url( '/assets/js/event-tracking.js' , __FILE__ ), array('jquery'), false, true);

    wp_localize_script('proq-ga-events', 'proq_ga_events', $event_tracking_config);

    wp_enqueue_script('proq-ga-events');


}