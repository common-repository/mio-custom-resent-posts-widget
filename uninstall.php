<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

function mio_wcrp_delete_plugin() {
	delete_option( 'widget_mio_custom_recent_posts' );
}

mio_wcrp_delete_plugin();
