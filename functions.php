<?php
function s4l_set_default_options_on_activation() {
	if (get_option('s4l_auto_alt_tag', null) === null) {
		update_option('s4l_auto_alt_tag', 1);
	}
}
add_action('after_switch_theme', 's4l_set_default_options_on_activation');
function s4l_child_theme_options_menu() {
	add_options_page(
		'S4L Child Theme Options',
		'S4L Theme Options',
		'manage_options',
		's4l-child-theme-options',
		's4l_child_theme_options_page'
	);
}
add_action('admin_menu', 's4l_child_theme_options_menu');

function s4l_child_theme_options_page() {
	?>
	<div class="wrap">
		<h1>S4L Child Theme Options</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('s4l_child_theme_options_group');
			do_settings_sections('s4l-child-theme-options');
			submit_button();
			?>
		</form>
	</div>
	<?php
}

function s4l_child_theme_register_settings() {
	register_setting('s4l_child_theme_options_group', 's4l_login_logo_bg');
	register_setting('s4l_child_theme_options_group', 's4l_auto_alt_tag');
	add_settings_section('s4l_child_theme_main_section', '', null, 's4l-child-theme-options');
	add_settings_field(
		's4l_login_logo_bg',
		'Login Background',
		's4l_child_theme_login_bg_field',
		's4l-child-theme-options',
		's4l_child_theme_main_section'
	);
	add_settings_field(
		's4l_auto_alt_tag',
		'Auto Alt Tag on Image Upload',
		's4l_child_theme_auto_alt_tag_field',
		's4l-child-theme-options',
		's4l_child_theme_main_section'
	);
function s4l_child_theme_auto_alt_tag_field() {
	$value = get_option('s4l_auto_alt_tag', false);
	echo '<input type="checkbox" name="s4l_auto_alt_tag" value="1" ' . checked(1, $value, false) . ' /> Enable automatic alt tagging on image upload';
}
}
add_action('admin_init', 's4l_child_theme_register_settings');

function s4l_child_theme_login_bg_field() {
	$value = get_option('s4l_login_logo_bg', false);
	echo '<input type="checkbox" name="s4l_login_logo_bg" value="1" ' . checked(1, $value, false) . ' /> Enable white background behind login logo';
}
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

/* Replace login area logo with custom title and S4L logo */

function custom_login_message() {
       // Try to get the site logo set in Elementor Site Settings (custom_logo theme mod)
       $logo_id = get_theme_mod( 'custom_logo' );
       $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id , 'full' ) : false;
       ?>
       <div id="loginheader">
       <?php $show_bg = get_option('s4l_login_logo_bg', false); ?>
       <?php if ($show_bg): ?>
	       <div class="s4l-login-logo-bg">
       <?php endif; ?>
       <?php if ( $logo_url ) : ?>
	       <img src="<?php echo esc_url( $logo_url ); ?>" width="184" alt="<?php echo esc_attr( get_bloginfo('name') ); ?> logo" style="display:block;margin:0 auto 16px; height:auto;" />
       <?php else : ?>
	       <img src="/wp-content/themes/s4l-hello-child-theme/login/s4l-logo.svg" width="184" alt="S4L logo" style="display:block;margin:0 auto 16px; height:auto;" />
       <?php endif; ?>
       <?php if ($show_bg): ?>
	       </div>
       <?php endif; ?>
       <h2 id="login-title"><?php echo get_bloginfo( 'name' ); ?></h2>
       </div>
       <div id="s4l-login-logo">
	       <div class="s4l-powered-by">powered by</div>
	       <a href="https://www.search4local.co.uk/">
		       <img src="/wp-content/themes/s4l-hello-child-theme/login/s4l-logo.svg" width="184" height="61" alt="S4L logo" />
	       </a>
       </div>
       <?php
}
add_filter('login_message', 'custom_login_message');

/* Enqueue custom login style */

function loginCSS() {
    wp_enqueue_style('login-styles', '/wp-content/themes/s4l-hello-child-theme/login/login_styles.css');
}
add_action('login_enqueue_scripts', 'loginCSS');

/* Automatically set the image Title, Alt-Text, Caption & Description upon upload */

function s4l_maybe_enable_auto_alt_tag() {
	if (get_option('s4l_auto_alt_tag', false)) {
		add_action( 'add_attachment', 'my_set_image_meta_upon_image_upload' );
	}
}
add_action('init', 's4l_maybe_enable_auto_alt_tag');

function my_set_image_meta_upon_image_upload( $post_ID ) {
	// Check if uploaded file is an image, else do nothing
	if ( wp_attachment_is_image( $post_ID ) ) {
		$my_image_title = get_post( $post_ID )->post_title;
		// Sanitize the title: remove hyphens, underscores & extra Spaces:
		$my_image_title = preg_replace( '%\s*[-_\s]+\s*%', ' ', $my_image_title );
		// Sanitize the title: capitalize first letter of every word (other letters lower case):
		$my_image_title = ucwords( strtolower( $my_image_title ) );
		// Create an array with the image meta (Title, Caption, Description) to be updated
		$my_image_meta = array(
			// Specify the image (ID) to be updated
			'ID' => $post_ID,
			// Set image Title to sanitized title
			'post_title' => $my_image_title,
		);
		// Set the image Alt-Text
		update_post_meta( $post_ID, '_wp_attachment_image_alt', $my_image_title );
		// Set the image meta (e.g. Title, Excerpt, Content)
		wp_update_post( $my_image_meta );
	}
}