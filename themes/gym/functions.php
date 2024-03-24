<?php
/**
 * Plugin Name: gym
 * Plugin URI: https://github.com/Dominique92/Chavil.gym
 * Description: Plugin WordPress pour la Gym Volontaire de Chaville
 * Author: Dominique Cavailhez
 * Version: 1.0.0
 * License: GPL2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit();
}

// Load syle.css files
add_action("wp_enqueue_scripts", "wp_enqueue_scripts_gym_theme");
function wp_enqueue_scripts_gym_theme() {
	wp_register_style("gym-theme-style", get_stylesheet_uri());
	wp_enqueue_style("gym-theme-style");
}

// Sous menu dans la page
add_shortcode("menu", "menu_gym_theme");
function menu_gym_theme($args) {
	return wp_nav_menu([
		"menu_class" => @$args["class"],
		"echo" => false,
	]);
}

add_action("admin_head", "admin_head_gym_theme");
function admin_head_gym_theme() {
	wp_enqueue_style("admin_css", get_stylesheet_directory_uri() . "/style.css");
}
