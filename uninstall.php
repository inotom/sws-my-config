<?php
/**
 * Serendip My Config Plugin uninstall script
 *
 * @package Serendip
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * オプションを削除
 *
 * @access public
 * @return void
 */
function sws_my_config_delete_options() {
	$settings = require dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';
	$fields = $settings['fields'];
	foreach ( $fields as $field ) {
		delete_option( $field['key'] );
	}
}
sws_my_config_delete_options();
