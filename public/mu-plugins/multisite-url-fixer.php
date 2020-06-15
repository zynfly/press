<?php
/**
 * Plugin Name:  Multisite Url Fixer
 * Plugin URI:   https://9z.work/
 * Description:  修复多站点模式下，URL错误问题
 * Version:      1.0.0
 * Author:       淄博玖臻信息技术工作室
 * Author URI:   https://9z.work/
 * License:      MIT License
 */




if ( ! defined( 'MULTISITE' ) ) {
	return;
}


add_filter( 'option_home', function ( $url ) {
	if ( substr( $url, - 5 ) === '/core' ) {
		$url = substr( $url, 0, - 5 );
	}

	return $url;
} );


add_filter( 'option_siteurl', function ( $url ) {
	if ( substr( $url, - 5 ) !== '/core' && ( is_main_site() || is_subdomain_install() ) ) {
		$url .= '/core';
	}

	return $url;
} );

add_filter( 'network_site_url', function ( $url, $path, $scheme ) {
	$path = ltrim( $path, '/' );
	$url  = substr( $url, 0, strlen( $url ) - strlen( $path ) );

	if ( substr( $url, - 5 ) !== 'core/' ) {
		$url .= 'core/';
	}

	return $url . $path;

}, 10, 3 );