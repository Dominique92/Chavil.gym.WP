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

// Load syles.css files
add_action("wp_enqueue_scripts", "wp_enqueue_scripts_theme_gym");
function wp_enqueue_scripts_theme_gym() {
	wp_register_style("gym-theme-style", get_stylesheet_uri());
	wp_enqueue_style("gym-theme-style");
}

// Use global urls in block templates (as defined in wp-includes/general-template.php)
add_shortcode("get_info", "get_info_theme_gym");
function get_info_theme_gym($args) {
	if ($args[0] == "current_user_id") {
		return get_current_user_id(); //TODO use is_user_logged_in()
   	} else {
		return get_bloginfo($args[0]);
	}
}

// Sous menu dans la page
add_shortcode("menu", "menu_theme_gym");
function menu_theme_gym($args) {
	return wp_nav_menu([
		"menu_class" => @$args["class"],
		"echo" => false,
	]);
}

// Redirection d'une page produit
add_filter('template_include', 'template_include_theme_gym');
function template_include_theme_gym($template) {
	global $post;

	if ($post) {
		$query = get_queried_object();
		$cat = get_the_terms($post->ID, 'product_cat');

		if (isset($query->post_type) &&
			$query->post_type == 'product' &&
			$cat)
			header('Location: '.get_site_url().'/'.$cat[0]->slug);
	}

	return $template;
}

add_action("admin_head", "admin_head_theme_gym");
function admin_head_theme_gym() {
	wp_enqueue_style("admin_css", get_stylesheet_directory_uri() . "/style.css");
}
