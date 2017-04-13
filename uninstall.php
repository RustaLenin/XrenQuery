<?php 

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

$func_name = 'xq_settings';
$option_name = 'xq_options';

// Для обычного сайта.
if ( !is_multisite() ) {
	unregister_setting($func_name);
	delete_option( $option_name );
	remove_shortcode( 'XQ_shortcode' );
	wp_deregister_script( 'XQ_stylesheet' );
	wp_dequeue_script( 'XQ_stylesheet' );
} 
// Для мультисайтовой сборки.
else {
	global $wpdb;

	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_blog_id = get_current_blog_id();

	foreach ( $blog_ids as $blog_id )   {
		switch_to_blog( $blog_id );
		unregister_setting($func_name);
		delete_site_option( $option_name );
		remove_shortcode( 'XQ_shortcode' );
		wp_deregister_script( 'XQ_stylesheet' );
		wp_dequeue_script( 'XQ_stylesheet' );		
	}

	switch_to_blog( $original_blog_id );
}

?>