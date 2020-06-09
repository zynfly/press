<?php
/**
 * Plugin Name:  Mu-Plugins Autoloader
 * Plugin URI:   https://9z.work/
 * Description:  使普通插件在mu-plugins目录可以自动加载。
 * Version:      1.0.0
 * Author:       淄博玖臻信息技术工作室
 * Author URI:   https://9z.work/
 * License:      MIT License
 */

if ( ! is_blog_installed() ) {
	return;
}

new \Roots\Bedrock\Autoloader();
