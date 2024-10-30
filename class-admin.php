<?php
/**
 * Plugin admin settings page
 *
 * @package Inquirer
 * @since 0.1.0
 */

namespace EcoTechie\Inquirer;

defined( 'ABSPATH' ) or die( 'These are not the files you are looking for...' );

if ( ! class_exists( 'Admin' ) ) {

	/**
	 * Admin admin page class.
	 *
	 * @since 0.1.0
	 *
	 * @package Inquirer
	 */
	class Admin {

		/**
		 * Instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @var object
		 */
		protected static $instance = null;

		private function __construct() {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'settings_init' ) );
			if ( is_admin() ) {
				wp_enqueue_style( 'query_me_this', plugins_url( 'assets/style.css',  __FILE__ ) );
			}
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
		 * Add admin page plugin menu.
		 *
		 * @since 0.1.0
		 */
		public function add_admin_menu() {
			add_options_page(
				'Inquirer',
				'Inquirer',
				'manage_options',
				'inquirer',
				'EcoTechie\Inquirer\Settings::options_page'
			);
		}

		function settings_section_callback() {
		}

		/**
		 * Add settings page settings.
		 *
		 * @since 0.1.0
		 */
		public function settings_init() {

			register_setting( 'inquirer_page', 'inquirer_settings' );

			add_settings_section(
				'inquirer_section',
				'',
				$this->settings_section_callback(),
				'inquirer_page'
			);

			add_settings_field(
				'load_inquirers',
				'',
				'EcoTechie\Inquirer\Settings::load_inquirers',
				'inquirer_page',
				'inquirer_section'
			);

			add_settings_field(
				'add_inquirer',
				'',
				'EcoTechie\Inquirer\Settings::add_inquirer',
				'inquirer_page',
				'inquirer_section'
			);
		}
	}

	/**
	 * Init the plugin.
	 */
	add_action( 'init', array( 'EcoTechie\Inquirer\Admin', 'get_instance' ) );
}
