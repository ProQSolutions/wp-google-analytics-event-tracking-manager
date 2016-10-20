<?php
/*
Plugin Name: Event Tracking Manager for Google Analytics
Description: Makes GA Event tracking easier
Version: 2.0.0
Author: Pro Q Solutions
Author URI: http://proqsolutions.com
*/

class WP_GA_Event_Tracking_Plugin {

  public function __construct() {

    // Hook into the admin menu
    add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );

    add_action( 'init', array( $this, 'include_acf' ), 5 );
    add_action( 'init', array( $this, 'setup_local_vars' ), 10 );

  }

  public function include_acf() {

    if( !class_exists('acf') ) {
      add_filter( 'acf/settings/path', array( $this, 'update_acf_settings_path' ) );
      add_filter( 'acf/settings/dir', array( $this, 'update_acf_settings_dir' ) );
      include_once( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields/acf.php' );
  }
  include_once( plugin_dir_path( __FILE__ ). 'vendor/acf-repeater/acf-repeater.php');

    $this->setup_options();

  }

  public function setup_local_vars() {
    if ( !is_admin() ) {

      // $event_tracking_config = require('config.php');

      $event_tracking_config = array(
        'is_debugging' => 'false',
        'events' => array(),
      );

      $debug = get_field('debug_mode', 'option');
      $debug = ( !empty($debug) && $debug[0] === 'enabled' ) ? 1 : 0;
      $event_tracking_config['is_debugging'] = $debug;

      $events = get_field('events', 'option');
      // error_log( 'Event Count (Above): ' . count($events) );
      foreach($events as $event) {
        $new_event = array(
          'selector' => $event['selector'],
          'bindEvent' => $event['event'],
          'eventCategory' => $event['category'],
          'eventAction' => $event['action'],
          'eventLabel' => $event['label'],
          'bind' => $event['bind'],
          'first' => $event['first'],
        );
        array_push( $event_tracking_config['events'], $new_event );
      }

      wp_register_script('jquery-bind-first', plugins_url( '/assets/js/vendor/jquery.bind-first-0.2.3.min.js', __FILE__ ), array('jquery'), false, true);
      wp_register_script('proq-ga-events', plugins_url( '/assets/js/event-tracking.js', __FILE__ ), array('jquery', 'jquery-bind-first'), false, true);

      wp_localize_script('proq-ga-events', 'proq_ga_events', $event_tracking_config);

      wp_enqueue_script('proq-ga-events');
      wp_enqueue_script('jquery-bind-first');
    }
  }

  public function update_acf_settings_path( $path ) {
    $path = plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields/';
    return $path;
  }

  public function update_acf_settings_dir( $dir ) {
    $dir = plugin_dir_url( __FILE__ ) . 'vendor/advanced-custom-fields/';
    return $dir;
  }

  public function create_plugin_settings_page() {
    add_action('admin_enqueue_scripts', array($this, 'enqueue_acf_head'));

    // Add the menu item and page
    $page_title = 'WP GA Event Tracking Settings';
    $menu_title = 'WP GA Event Tracking';
    $capability = 'manage_options';
    $slug = 'wp_ga_event_tracking_settings';
    $callback = array( $this, 'plugin_settings_page_content' );
    // $icon = 'dashicons-admin-plugins';
    // $position = 100;

    // add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    add_submenu_page( 'options-general.php', $page_title, $menu_title, $capability, $slug, $callback );

    
  }

  public function enqueue_acf_head($hook_suffix) {
    if($hook_suffix == 'settings_page_wp_ga_event_tracking_settings') {
      acf_form_head();
    }
  }

  public function plugin_settings_page_content() {
  
    do_action('acf/input/admin_head');
    do_action('acf/input/admin_enqueue_scripts');

    $options = array(
      'id' => 'acf-form',
      'post_id' => 'options',
      'new_post' => false,
      'field_groups' => array( 'acf_awesome-options' ),
      'return' => admin_url('admin.php?page=wp_ga_event_tracking_settings'),
      'submit_value' => 'Update',
    );

    acf_form( $options );
  }

  public function setup_options() {

      if(function_exists("register_field_group"))
      {
        register_field_group(array (
          'id' => 'acf_awesome-options',
          'title' => 'Awesome Options',
          'fields' => array (
            array (
              'key' => 'field_57f805b421176',
              'label' => 'Debug Mode',
              'name' => 'debug_mode',
              'type' => 'checkbox',
              'required' => 0,
              'choices' => array (
                'enabled' => 'Enable Debug Mode',
              ),
              'default_value' => '',
              'layout' => 'horizontal',
            ),
            array (
              'key' => 'field_57f802407363b',
              'label' => 'Events',
              'name' => 'events',
              'type' => 'repeater',
              'sub_fields' => array (
                array (
                  'key' => 'field_57f802987363c',
                  'label' => 'Selector',
                  'name' => 'selector',
                  'type' => 'text',
                  'required' => 1,
                  'column_width' => '',
                  'default_value' => '',
                  'placeholder' => '.class-name',
                  'prepend' => '',
                  'append' => '',
                  'formatting' => 'none',
                  'maxlength' => '',
                ),
                array (
                  'key' => 'field_57f8032b7363d',
                  'label' => 'Event',
                  'name' => 'event',
                  'type' => 'text',
                  'required' => 1,
                  'column_width' => '',
                  'default_value' => '',
                  'placeholder' => 'click',
                  'prepend' => '',
                  'append' => '',
                  'formatting' => 'none',
                  'maxlength' => '',
                ),
                array (
                  'key' => 'field_57f8035e7363e',
                  'label' => 'Category',
                  'name' => 'category',
                  'type' => 'text',
                  'required' => 1,
                  'column_width' => '',
                  'default_value' => '',
                  'placeholder' => 'Click Event',
                  'prepend' => '',
                  'append' => '',
                  'formatting' => 'none',
                  'maxlength' => '',
                ),
                array (
                  'key' => 'field_57f803767363f',
                  'label' => 'Action',
                  'name' => 'action',
                  'type' => 'text',
                  'required' => 1,
                  'column_width' => '',
                  'default_value' => '',
                  'placeholder' => 'Clicked',
                  'prepend' => '',
                  'append' => '',
                  'formatting' => 'none',
                  'maxlength' => '',
                ),
                array (
                  'key' => 'field_57f8039e73640',
                  'label' => 'Label',
                  'name' => 'label',
                  'type' => 'text',
                  'required' => 1,
                  'column_width' => '',
                  'default_value' => '',
                  'placeholder' => 'Whatever you want',
                  'prepend' => '',
                  'append' => '',
                  'formatting' => 'none',
                  'maxlength' => '',
                ),
                array (
                  'key' => 'field_57f803ac73641',
                  'label' => 'Bind',
                  'name' => 'bind',
                  'type' => 'select',
                  'required' => 1,
                  'choices' => array (
                    'true' => 'Yes',
                    'false' => 'No',
                  ),
                  'default_value' => 'false',
                  'allow_null' => 0,
                  'multiple' => 0,
                ),
                array (
                  'key' => 'field_57f803d373642',
                  'label' => 'First',
                  'name' => 'first',
                  'type' => 'select',
                  'required' => 1,
                  'choices' => array (
                    'true' => 'Yes',
                    'false' => 'No',
                  ),
                  'default_value' => 'true',
                  'allow_null' => 0,
                  'multiple' => 0,
                ),
              ),
              'row_min' => '',
              'row_limit' => '',
              'layout' => 'table',
              'button_label' => 'Add Row',
            ),
          ),
          'location' => array (
            array (
              array (
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'post',
                'order_no' => 0,
                'group_no' => 0,
              ),
            ),
          ),
          'options' => array (
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array (
            ),
          ),
          'menu_order' => 0,
        ));
      }

  }

}
new WP_GA_Event_Tracking_Plugin();
