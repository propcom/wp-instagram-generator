<?php

/*
 * Plugin Name: Propeller Instagram Generator
 * Version: 1.0.0
 * Author: Propeller
 * Description: Generate posts to be used with a relationship field based on selected Instagram posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} else {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-data-handling.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-admin-area.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-post-generator.php';

	new AdminArea();
}





