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
	$event_tracking_config = require('config.php');

  wp_register_script('jquery-bind-first', plugins_url( '/assets/js/vendor/jquery.bind-first-0.2.3.min.js', __FILE__ ), array('jquery'), false, true);
  wp_register_script('proq-ga-events', plugins_url( '/assets/js/event-tracking.js', __FILE__ ), array('jquery', 'jquery-bind-first'), false, true);

  wp_localize_script('proq-ga-events', 'proq_ga_events', $event_tracking_config);

  wp_enqueue_script('proq-ga-events');
	wp_enqueue_script('jquery-bind-first');

}
