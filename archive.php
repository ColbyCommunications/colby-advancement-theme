<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$templates = array( 'archive.twig', 'index.twig' );

$context = Timber::context();

$context['title'] = 'Archive';
if ( is_day() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'D M Y' );
} elseif ( is_month() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'M Y' );
} elseif ( is_year() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'Y' );
} elseif ( is_tag() ) {
	$context['title'] = single_tag_title( '', false );
} elseif ( is_category() ) {
	$context['title'] = single_cat_title( '', false );
	array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
} elseif ( is_post_type_archive() ) {
	$context['title'] = post_type_archive_title( '', false );
	array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}

$timezone = new DateTimeZone('America/New_York');
// $current_date = date('Y-m-d G:i:s');
$current_date = wp_date("Y-m-d G:i:s", null, $timezone );

if ( get_post_type() == 'events' ) {

	$posts = Timber::get_posts(array(
			'post_type' => 'events',
			'post_status' => array('publish'),
			'posts_per_page' => -1,
	));

	$context['posts'] = array_filter ($posts->to_array(), function($post) use ($current_date){
		if (!$post->event_end_date){
			return false;
		}
		return strtotime($post->event_end_date) > strtotime($current_date);
	});
}

Timber::render( $templates, $context );
