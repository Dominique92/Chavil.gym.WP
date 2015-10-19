<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen_Gym
 * @since Twenty Fifteen 1.0
 * Author: Dominique Cavailhez
 * Inherited from Twenty_Fifteen
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">

	<meta name="description" content="Section de Chaville de la Fédération Française de Gymnastique Volontaire">
	<meta name="keywords" content="gymnastique,volontaire,cardio,nordique,yoga,pilates,bien-être,qi-gong,marche,Chaville">
	<link rel="shortcut icon" href="wp-content/themes/twentyfifteen-gym/favicon.ico" />

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
		<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
	<!--[if lt IE 9]>
		<link rel="stylesheet" href="wp-content/themes/twentyfifteen-gym/ie78.css" type="text/css" media="screen" />
	<![endif]-->
	<style>.site {max-width: 100%;}</style><!-- Pour IE > 8 -->
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyfifteen' ); ?></a>

	<div id="baniere">
		<img alt="EPGV" src="wp-content/themes/twentyfifteen-gym/logo.png" />
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Aller à l'accueil">
			<b><?php bloginfo( 'name' ); ?></b>
			&nbsp;
			<i><?php echo get_bloginfo( 'description', 'display' ); ?></i>
		</a>
	</div>

	<div id="sidebar" class="sidebar">
		<header id="masthead" class="site-header" role="banner">
			<div class="site-branding">
				<?php
					if ( is_front_page() && is_home() ) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<?php else : ?>
						<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<?php endif;

					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo $description; ?></p>
					<?php endif;
				?>
				<button class="secondary-toggle"><?php _e( 'Menu and widgets', 'twentyfifteen' ); ?></button>
			</div><!-- .site-branding -->
		</header><!-- .site-header -->

		<?php get_sidebar(); ?>
	</div><!-- .sidebar -->

	<div id="content" class="site-content">
