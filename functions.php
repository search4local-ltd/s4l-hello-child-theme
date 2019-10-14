<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );


add_action( 'customize_register', 'cd_customizer_settings' );
function cd_customizer_settings( $wp_customize ) {
	$wp_customize->add_section( 'cd_script' , array(
		'title'      => 'Scripts',
		'priority'   => 30,
	) );
	$wp_customize->add_setting( 'header_script' , array(
		'transport'   => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_script', array(
		'label'        => 'Header Script',
		'section'    => 'cd_script',
		'settings'   => 'header_script',
		'type'        => 'textarea',
		'description'   => 'This code will output immediately before the closing head tag.',
	) ) );
}

add_action( 'wp_head', 'cd_customizer_script');
function cd_customizer_script()
{
         echo get_theme_mod('header_script', '');
        
}