<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function web3gatepicker_delete_plugin() {
	global $wpdb;



	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		 'web3gatepicker_config' ) );
}

web3gatepicker_delete_plugin();
