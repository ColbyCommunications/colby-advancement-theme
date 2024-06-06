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

if ( get_post_type() == 'events' ) {
	global $paged;

	if (!isset($paged) || !$paged){
		$paged = 1;
	}

	$current_date = date('Y-m-d'); // Get the current date in the format YYYY-MM-DD

$current_date = date('Y-m-d'); // Get the current date in the format YYYY-MM-DD

$context['posts'] = new Timber\PostQuery(array(
    'post_type' => 'events',
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'event_date',
            'value' => $current_date,
            'compare' => '>=', // Events with dates greater than or equal to the current date
            'type' => 'DATE',
        ),
        array(
            'key' => 'event_date',
            'value' => $current_date,
            'compare' => '=', // Today's events
            'type' => 'DATE',
        ),
    ),
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'paged' => $paged,
    'posts_per_page' => -1,
));
}


Timber::render( $templates, $context );