<?php
/**
 * Plugin Name: Enix Updater
 * Plugin URI:  https://example.com/
 * Description: A basic plugin for Enix Updater.
 * Version:     1.0.2
 * Author:      Enix
 * Author URI:  https://example.com/
 * License:     GPL-2.0+
 * Text Domain: enix-updater
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$enixUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/ahamedenamul/enix-updater/',
	__FILE__,
	'enix-updater'
);

// Set the branch that contains the stable release.
$enixUpdateChecker->setBranch( 'main' );

// Optional: If you change your repository to private, uncomment the line below and add your GitHub access token.
$enixUpdateChecker->setAuthentication('ghp_f3lM2l7jT4yV77F382pC5V8z0l44gP4l30H1');
