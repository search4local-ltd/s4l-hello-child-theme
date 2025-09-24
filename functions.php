<?php
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
	       <?php if ( $logo_url ) : ?>
		       <img src="<?php echo esc_url( $logo_url ); ?>" width="184" height="61" alt="<?php echo esc_attr( get_bloginfo('name') ); ?> logo" style="display:block;margin:0 auto 16px;" />
	       <?php else : ?>
		       <img src="/wp-content/themes/s4l-hello-child-theme/login/s4l-logo.svg" width="184" height="61" alt="S4L logo" style="display:block;margin:0 auto 16px;" />
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

add_action( 'add_attachment', 'my_set_image_meta_upon_image_upload' );

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