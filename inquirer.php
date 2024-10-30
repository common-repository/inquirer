<?php
/**
 * Plugin Name:     Inquirer
 * Plugin URI:      https://www.ecotechie.io/inquirer/
 * Description:     Get information about your site locally and from easily searching external resources.
 * Author:          Sergio Scabuzzo
 * Author URI:      https://www.ecotechie.io/
 * Text Domain:     inquirer
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Inquirer
 * @since 0.1.0
 */

namespace EcoTechie\Inquirer;

defined( 'ABSPATH' ) or die( 'These are not the files you are looking for...' );

if ( ! class_exists( 'Main' ) ) {

	/**
	 * Main class.
	 *
	 * @since 0.1.0
	 *
	 * @package Inquirer
	 */
	class Main {

		/**
		 * Plugin version.
		 *
		 * @since 0.1.0
		 *
		 * @var string
		 */
		const VERSION = '0.1.0';

		/**
		 * Instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin.
		 *
		 * @since 0.1.0
		 */
		private function __construct() {
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			$this->includes();
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 0.1.0
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'inquirer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Register activation hook and create db options.
		 *
		 * @since 0.1.0
		 */
		static function activate() {
			$options = array(
				'url_for_query' => get_site_url(),
				'inquirer_list' => array(
					array(
						'inquirer_name' => 'WebPageTest',
						'inquirer_url'  => 'https://www.webpagetest.org/',
						'inquirer_type' =>  array(
							'Core Web Vitals',
							'Speed',
						),
						'inquirer_testing_url' => 'https://www.webpagetest.org/?url=',
					),
					array(
						'inquirer_name' => 'GTmetrix',
						'inquirer_url'  => 'https://gtmetrix.com/',
						'inquirer_type' =>  array(
							'Core Web Vitals',
							'Speed',
						),
						'inquirer_testing_url' => 'https://gtmetrix.com/?url=',
					),
					array(
						'inquirer_name' => 'PageSpeed Insights',
						'inquirer_url'  => 'https://pagespeed.web.dev/',
						'inquirer_type' =>  array(
							'Core Web Vitals',
							'Speed',
						),
						'inquirer_testing_url' => 'https://pagespeed.web.dev/report?url=',
					),
					array(
						'inquirer_name' => 'WAVE Web Accessibility',
						'inquirer_url'  => 'https://wave.webaim.org/',
						'inquirer_type' =>  array(
							'Accessibility',
						),
						'inquirer_testing_url' => 'https://wave.webaim.org/report#/',
					),
				),
			);
			add_option( 'inquirer_settings', $options );
		}

		/**
		 * Include plugin functions.
		 *
		 * @since 0.1.0
		 */
		protected function includes() {
			include_once dirname( __FILE__ ) . '/class-admin.php';
			include_once dirname( __FILE__ ) . '/class-settings.php';
		}
	}

	/**
	 * Init the plugin.
	 */
	add_action( 'plugins_loaded', array( 'EcoTechie\Inquirer\Main', 'get_instance' ) );
	add_action( 'admin_post_load_inquirers_action', array( 'EcoTechie\Inquirer\Settings', 'update_url_for_query' ) );
	add_action( 'admin_post_load_inquirers_action', array( 'EcoTechie\Inquirer\Settings', 'use_inquirer' ) );
	add_action( 'admin_post_load_inquirers_action', array( 'EcoTechie\Inquirer\Settings', 'remove_inquirer' ) );
	add_action( 'admin_post_load_inquirers_action', array( 'EcoTechie\Inquirer\Settings', 'filter_inquirer_types' ) );
	add_action( 'admin_post_add_inquirer_action', array( 'EcoTechie\Inquirer\Settings', 'save_inquirer' ) );
	register_activation_hook( __FILE__, array('EcoTechie\Inquirer\Main', 'activate' ) );
}
