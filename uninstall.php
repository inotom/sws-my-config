<?php
/**
 * Serendip My Config Plugin uninstall script
 *
 * @package Serendip
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}


require_once dirname( __FILE__ ) . '/includes/sws-my-config-constants.php';

delete_option( SWS_MY_CONFIG_OPTION_KEY );
