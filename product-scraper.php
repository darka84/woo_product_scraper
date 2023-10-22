<?php
/**
 * Plugin Name: Product Scraper
 * Version: 1.0.0
 * Plugin URI: http://tekme.lt
 * Description: Copy products from any eshop to your woocommerce..
 * Author: Darius Mikėnas
 * Author URI: http://tekme.lt 
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: product-scraper
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Darius Mikėnas
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-product-scraper.php';
require_once 'includes/class-product-scraper-settings.php';

// Load plugin libraries.
require_once 'includes/lib/class-product-scraper-admin-api.php';
require_once 'includes/lib/class-product-scraper-post-type.php';
require_once 'includes/lib/class-product-scraper-taxonomy.php';
require_once 'includes/lib/class-product-scraper-html-parser.php';
require_once 'vendor/autoload.php';

/**
 * Returns the main instance of Product_Scraper to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Product_Scraper
 */
function product_scraper() {
	$instance = Product_Scraper::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Product_Scraper_Settings::instance( $instance );
	}

	return $instance;
}

product_scraper();
