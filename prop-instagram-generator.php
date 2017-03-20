<?php

/*
 * Plugin Name: Propeller Instagram Generator
 * Version: 1.0.0
 * Author: Callam Williams
 * Description: Pull media from Instagram and have it assigned to posts and imported into media library
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit;

} else {

	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-setup.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-data-handling.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-render.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wp-instagram-generator/class-post-generator.php';

	new Setup();

}
