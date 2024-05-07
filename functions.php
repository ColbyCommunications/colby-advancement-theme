<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package    WordPress
 * @subpackage Timber
 * @since      Timber 0.1
 */

 include __DIR__ . '/acf_fields.php';

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	include_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function ( $template ) {
			return get_template_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
$static_timber_dirs = array(
	'src/twig/layouts',
	'src/twig/pages',
	'src/twig/components',
);

$theme_base = get_stylesheet_directory() . '/';

$component_dirs = array_map(
	function ( $str ) use ( &$theme_base ) {
		return str_replace( $theme_base, '', $str );
	},
	array_filter( glob( $theme_base . 'src/twig/components/*' ), 'is_dir' )
);

Timber::$dirname = array_merge( $static_timber_dirs, $component_dirs );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class AdvancementSite extends Timber\Site {

	/**
	 * Add timber support.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'acf/init', array( $this, 'my_acf_init' ) );
		parent::__construct();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {
		register_post_type(
			'events',
			array(
				'labels'            => array(
					'name'               => __( 'Events' ),
					'singular_name'      => __( 'Event' ),
					'add_new_item'       => __( 'Add Event' ),
					'edit_item'          => __( 'Edit Event' ),
					'new_item'           => __( 'New Event' ),
					'view_item'          => __( 'View Event' ),
					'search_items'       => __( 'Search Event' ),
					'not_found'          => __( 'Event not found.' ),
					'not_found_in_trash' => __( 'No Event found in trash.' ),
				),
				'rewrite'           => array( 'slug' => 'events/event-calendar' ),
				'public'            => true,
				'has_archive'       => true,
				'menu_icon'         => 'dashicons-calendar-alt',
				'show_in_nav_menus' => true,
				'show_in_rest'      => true,
				'supports'          => array( 'title', 'editor', 'revisions', 'excerpt', 'thumbnail' ),
			)
		);

		register_post_type(
			'subevents',
			array(
				'labels'            => array(
					'name'               => __( 'Subevents' ),
					'singular_name'      => __( 'Subevent' ),
					'add_new_item'       => __( 'Add Subevent' ),
					'edit_item'          => __( 'Edit Subevent' ),
					'new_item'           => __( 'New Subevent' ),
					'view_item'          => __( 'View Subevent' ),
					'search_items'       => __( 'Search Subevent' ),
					'not_found'          => __( 'Subevent not found.' ),
					'not_found_in_trash' => __( 'No Subevent found in trash.' ),
				),
				'public'            => true,
				'has_archive'       => false,
				'menu_icon'         => 'dashicons-calendar',
				'show_in_nav_menus' => true,
				'show_in_rest'      => true,
				'supports'          => array( 'title', 'editor', 'revisions', 'excerpt', 'thumbnail' ),
			)
		);
	}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies() {
		register_taxonomy(
			'groupings',
			'subevents',
			array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'public'            => true,
				'show_tagcloud'     => false,
				'capabilities'      => array(
					'manage_terms' => 'manage_options',
					'edit_terms'   => 'manage_options',
					'delete_terms' => 'manage_options',
					'assign_terms' => 'manage_options',
				),
				'labels'            => array(
					'name'          => __( 'Groupings' ),
					'singular_name' => __( 'Grouping' ),
					'add_new_item'  => __( 'Add New Grouping' ),
					'menu_name'     => __( 'Groupings' ),
				),
			)
		);
	}

	public function my_acf_init() {

		// check function exists
		if ( function_exists( 'acf_register_block' ) ) {

			// register a media context section
			acf_register_block(
				array(
					'name'            => 'media-context-section',
					'title'           => __( 'Media Context Section' ),
					'description'     => __( 'Section component dedicated to grouping media context with a decorative background' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'media', 'context', 'section' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);

			// register a collage section
			acf_register_block(
				array(
					'name'            => 'collage-section',
					'title'           => __( 'Collage Section' ),
					'description'     => __( 'Decorative section with curated image group and context' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'collage', 'context', 'media', 'section', 'image' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);

			// register a related section
			acf_register_block(
				array(
					'name'            => 'related-section',
					'title'           => __( 'Related Section' ),
					'description'     => __( 'Component typically found at the bottom of post style pages.' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'media', 'context', 'section', 'related' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);

			// register a icon section
			acf_register_block(
				array(
					'name'            => 'icon-section',
					'title'           => __( 'Icon Section' ),
					'description'     => __( 'Section dedicated to grouping svg assets or transparent pngs.' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'icon', 'context', 'section' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);

			// register a list block grid
			acf_register_block(
				array(
					'name'            => 'list-block-grid',
					'title'           => __( 'List Block Grid' ),
					'description'     => __( 'Dedicated grid for list blocks' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'list', 'block', 'grid' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);

			// register a bg inset media context
			acf_register_block(
				array(
					'name'            => 'bg-inset-media-context',
					'title'           => __( 'Background Inset Media Context' ),
					'description'     => __( 'Container component for the media context supporting background textures.' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'media', 'context', 'background', 'inset' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);

			// register a home section
			acf_register_block(
				array(
					'name'            => 'home-section',
					'title'           => __( 'Home Section' ),
					'description'     => __( 'Advancement site exclusive block for rendering latest events and alumni news from the Colby News site' ),
					'render_callback' => 'my_acf_block_render_callback',
					'category'        => 'layout',
					'icon'            => file_get_contents( get_template_directory() . '/src/images/svg/c.svg' ),
					'keywords'        => array( 'home', 'context', 'section', 'news', 'events' ),
					'mode'            => 'edit',
					'supports'        => array(
						'align' => false,
					),
				)
			);
		}
	}

	public function theme_supports() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'Global Settings',
					'menu_title' => 'Global Settings',
					'menu_slug'  => 'global-settings',
					'redirect'   => false,
				)
			);
		}

	}
}

new AdvancementSite();

/**
 * Enqueue scripts and styles.
 */
function advancement_theme_scripts() {
	// remove parent
	wp_dequeue_style( 'hvh' );
	wp_dequeue_script( 'main' );

	// child
	wp_enqueue_style( 'child_css', get_stylesheet_directory_uri() . '/dist/styles/scripts.css', array(), date( 'H:i:s' ) );
	wp_enqueue_script( 'child_scripts', get_stylesheet_directory_uri() . '/dist/scripts.js', array(), date( 'H:i:s' ), true );
}
add_action( 'wp_enqueue_scripts', 'advancement_theme_scripts', 100 );

add_filter( 'auto_core_update_send_email', '__return_false' );

// remove shortlinks in <head>
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
