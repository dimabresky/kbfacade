<?php
/**
 * Main template.
 *
 * @package KBFacade
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="kbf-container">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	}
	?>
</div>
<?php
get_footer();
