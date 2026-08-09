<?php
/**
 * Theme header.
 *
 * @package KBFacade
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="kbf-site site">
	<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'kbfacade' ); ?></a>
	<main id="content" class="kbf-site-main site-main">
