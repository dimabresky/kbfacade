<?php
/**
 * Template Name: Elementor Canvas
 * Template Post Type: page
 *
 * Blank canvas for Elementor (header/footer omitted intentionally).
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
<body <?php body_class( 'elementor-template-canvas' ); ?>>
<?php wp_body_open(); ?>
<?php
while ( have_posts() ) {
	the_post();
	the_content();
}
wp_footer();
?>
</body>
</html>
