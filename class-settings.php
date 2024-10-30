<?php

#TODO:
#Maybe use thickbox modal for info/warnings: https://codex.wordpress.org/ThickBox
#Use the help tab for info, like Core does.

namespace EcoTechie\Inquirer;

defined( 'ABSPATH' ) or die( 'These are not the files you are looking for...' );

if ( ! class_exists( 'Settings' ) ) {

	/**
	 * Settings: plugin settings page class.
	 *
	 * @since 0.1.0
	 *
	 * @package Inquirer
	 */
	class Settings {

		/**
		 * Instance of this class.
		 *
		 * @since 0.1.0
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Nonce for the forms.
		 *
		 * @since 0.1.0
		 *
		 * @var string
		 */
		protected static $inquirer_add_meta_nonce = '';

		/**
		 * Array of plugin options (inquirer_settings).
		 *
		 * @since 0.1.0
		 *
		 * @var array
		 */
		protected static $options = '';

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
			self::$options = get_option( 'inquirer_settings' );
			return self::$instance;
		}

		/**
		 * Load Inquirers from database.
		 *
		 * @since 0.1.0
		 */
		static function load_inquirers() {
			self::$inquirer_add_meta_nonce = wp_create_nonce( 'inquirer_add_meta_form_nonce' );
			$inquirer_list_number          = 0;

			if ( ! isset( self::$options['url_for_query'] ) ) {
				self::$options['url_for_query'] = get_site_url();
				update_option( 'inquirer_settings', self::$options );
			}
			?>
				<form action='admin-post.php' method='post'>
					<input type='hidden' name='action' value='load_inquirers_action'>
					<input type='hidden' name='nonce' value='<?php echo esc_textarea( self::$inquirer_add_meta_nonce );?>' />
					<h3>URL to use for query:</h3>
						<input type='url' id='url_for_query' name='url_for_query' value='<?php echo esc_url( self::$options['url_for_query'] );?>' placeholder='<?php echo esc_url( self::$options['url_for_query'] );?>' aria-label='Enter a URL to Test' pattern='https://.*' title='Begin URL with https://' minlength='12' maxlength='80' tabindex='0' autocorrect='off' autocapitalize='none' spellcheck='false' autofocus='true'>
						<section>
							<p class='inquirer_info'>Currently set to <strong><?php echo esc_url( self::$options['url_for_query'] );?></strong> ℹ️</p>
							<p class='inquirer_info-content'>There is no need to update the URL (thereby saving it), instead you can fill in a URL and test away...</p>
						</section>
						<button  name='update_url_for_query' value='<?php echo esc_url( self::$options['url_for_query'] );?>' class='button button-primary'>Update URL for Query</button>
					<h3>Filter by type:</h3>
					<div class='inquirer_query-types'>
			<?php
			if ( isset( self::$options['inquirer_list'] ) && ! empty( self::$options['inquirer_list'] ) ) {
				foreach ( self::$options['inquirer_list'] as $inquirer ) {
					foreach ( $inquirer['inquirer_type'] as $inquirer_type ) {
						$inquirer_type_list[] = $inquirer_type;
					}
				}
				asort( $inquirer_type_list );
				$inquirer_type_list         = array_unique( $inquirer_type_list );
				$inquirer_query_type_status = '';
				foreach ( $inquirer_type_list as $type ) {
					if ( isset( self::$options['filtered_inquirer_types'] ) && ( in_array( $type, self::$options['filtered_inquirer_types'] ) ) ) {
						$inquirer_query_type_status = 'inquirer_query-type-active';
					} else {
						$inquirer_query_type_status = 'inquirer_query-type-inactive';
					}
					?>
						<button name='inquirer_type' value='<?php echo esc_textarea( $type );?>' class='button <?php echo esc_textarea( $inquirer_query_type_status );?>'><?php echo esc_textarea( $type );?></button>
					<?php
				}
				if ( ! empty( self::$options['filtered_inquirer_types'] ) ) {
					?>
						<button name='inquirer_type' value='inquirer_reset_types' class='button delete button-secondary'>Reset Filter</button>
					<?php
				}
			}
			?>
				</div>
				<div class='inquirer_queries'>
			<?php
			$plugin_dir_path = plugin_dir_path( __FILE__ );
			$plugin_dir_url  = plugin_dir_url( __FILE__ );
			foreach ( self::$options['inquirer_list'] as $inquirer ) {

				$inquirer_icon_file_path = $plugin_dir_path . 'assets/images/';
				$inquirer_icon_file_url  = $plugin_dir_url . 'assets/images/';

				if ( isset( self::$options['filtered_inquirer_types'] ) && ! array_intersect( $inquirer['inquirer_type'], self::$options['filtered_inquirer_types'] ) && ! empty( self::$options['filtered_inquirer_types'] ) ) {
					$inquirer_list_number++;
					continue;
				}
				?>
					<section class='inquirer_query'>
						<h4>
							<a href='<?php echo esc_url( $inquirer['inquirer_url'] );?>' target='_blank' rel='noopener nofollow' tabindex='-1'>
								<?php
									if ( ! isset( $inquirer['inquirer_icon'] ) ) {
										preg_match( '#(^(?:https?://)?(?:[^/]*))#i', $inquirer['inquirer_url'], $inquirer_base_url );
										$inquirer_response = wp_remote_get( $inquirer_base_url[0] );
										if ( ! is_wp_error( $inquirer_response ) && $inquirer_response['response']['code'] < 400 ) {
											$inquirer_body = $inquirer_response['body'];
											$pattern       = '/rel=[\'"](?:shortcut )?icon[\'"] href=[\'"]([^?\'"]+)[?\'"]/';
											if ( preg_match( $pattern, $inquirer_body, $inquirer_icon ) !== 0 ) {
												$inquirer_parsed_url = parse_url( $inquirer_icon[1] );
												if ( empty( $inquirer_parsed_url['scheme'] ) ) {
													if ( stripos( $inquirer_icon[1], '//' ) === 0 ) {
														$inquirer_icon_url = "https:" . $inquirer_icon[1];
													}
													if ( empty( $inquirer_parsed_url['host'] ) ) {
														$inquirer_icon_url = $inquirer['inquirer_url'] . $inquirer_icon[1];
													}
												}
											} else {
												$inquirer_favicon_response = wp_remote_get( $inquirer_base_url[0] . '/favicon.ico' );
												if ( ! is_wp_error( $inquirer_favicon_response ) && $inquirer_favicon_response['response']['code'] < 400 ) {
													$inquirer_icon_url = $inquirer_base_url[0] . '/favicon.ico';
												}
											}
											$inquirer_icon_file = strtolower(str_replace(' ','_', $inquirer['inquirer_name'] ) ) . '.' . pathinfo( $inquirer_icon_url, PATHINFO_EXTENSION );
											file_put_contents( $inquirer_icon_file_path . $inquirer_icon_file, file_get_contents( $inquirer_icon_url ) );
											self::$options['inquirer_list'][$inquirer_list_number]['inquirer_icon'] = $inquirer_icon_file_url . $inquirer_icon_file;
											update_option( 'inquirer_settings', self::$options );
										}
									} else {
										?>
										<div class='inquirer_query-image'>
											<img src='<?php echo esc_url( self::$options['inquirer_list'][$inquirer_list_number]['inquirer_icon'] );?>'>
										</div>
										<?php
									}
									echo '<div>' . esc_textarea( $inquirer['inquirer_name'] ) . '</div>';
								?>
							</a>
						</h4>
						<div class='inquirer_query-types'>
							<?php
								if ( isset( $inquirer['inquirer_type'] ) ) {
									foreach ( $inquirer['inquirer_type'] as $inquirer_type ) {
										if ( isset( self::$options['filtered_inquirer_types'] ) && in_array( $inquirer_type, self::$options['filtered_inquirer_types'] ) ) {
											$inquirer_query_type_status = 'inquirer_query-type-active';
										} else {
											$inquirer_query_type_status = 'inquirer_query-type-inactive';
										}
										?>
											<button name='inquirer_type' value='<?php echo esc_textarea( $inquirer_type );?>' class='button <?php echo esc_textarea( $inquirer_query_type_status );?>'><?php echo esc_textarea( $inquirer_type );?></button>
										<?php
									}
								}
							?>
						</div>
						<div>
							<button name='use_inquirer' value='<?php echo esc_textarea( $inquirer_list_number );?>' formtarget='_blank' class='button button-primary'>Test URL</button>
							<button name='remove_inquirer' value='<?php echo esc_textarea( $inquirer_list_number );?>' class='button delete button-secondary'>Delete Inquirer!</button>
						</div>
					</section>
				<?php
				$inquirer_list_number++;
			}
			?>
				</div>
			</form>
			<?php
		}

		/**
		 * Update URL to Inquire
		 *
		 * @since 0.1.0
		 */
		static function update_url_for_query() {
			if ( isset( $_POST['update_url_for_query'] ) && isset( $_POST['url_for_query'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'inquirer_add_meta_form_nonce' ) ) {
				self::$options['url_for_query'] = esc_url( $_POST['url_for_query'] );
				update_option( 'inquirer_settings', self::$options );
				wp_redirect( admin_url( 'options-general.php?page=inquirer' ) );
				exit;
			}
		}

		/**
		 * Filter Inquirer Types
		 *
		 * @since 0.1.0
		 */
		static function filter_inquirer_types() {
			if ( isset( $_POST['inquirer_type'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'inquirer_add_meta_form_nonce' ) ) {
				$inquirer_type = esc_textarea( $_POST['inquirer_type'] ) ;
				if ( $inquirer_type === 'inquirer_reset_types' ) {
					self::$options['filtered_inquirer_types'] = array();
				} else if ( ! isset( self::$options['filtered_inquirer_types'] ) || ! in_array( $inquirer_type, self::$options['filtered_inquirer_types']) ) {
					self::$options['filtered_inquirer_types'][] = $inquirer_type;
				} else {
					unset( self::$options['filtered_inquirer_types'][array_search( $inquirer_type, self::$options['filtered_inquirer_types'] )] );
				}
			}
			update_option( 'inquirer_settings', self::$options );
			wp_redirect( admin_url( 'options-general.php?page=inquirer' ) );
			exit;
		}

		/**
		 * Use Inquirer
		 *
		 * @since 0.1.0
		 */
		static function use_inquirer() {
			if ( isset( $_POST['use_inquirer'] ) && is_numeric( $_POST['use_inquirer'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'inquirer_add_meta_form_nonce' ) ) {
				$inquirer_url  = self::$options['inquirer_list'][$_POST['use_inquirer']]['inquirer_testing_url'] . esc_url( $_POST['url_for_query'] );
				header("Location: $inquirer_url" );
				exit;
			}
		}

		/**
		 * Remove Inquirer
		 *
		 * @since 0.1.0
		 */
		static function remove_inquirer() {
			if ( isset( $_POST['remove_inquirer'] ) && is_numeric( $_POST['remove_inquirer'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'inquirer_add_meta_form_nonce' ) ) {
				wp_delete_file( get_home_path() . parse_url( esc_url( self::$options['inquirer_list'][$_POST['remove_inquirer']]['inquirer_icon'] ) )['path'] );
				array_splice( self::$options['inquirer_list'], $_POST['remove_inquirer'], 1 );
				update_option( 'inquirer_settings', self::$options );
				wp_redirect( admin_url( 'options-general.php?page=inquirer' ) );
				exit;
			}
		}

		/**
		 * Add Inquirer
		 *
		 * @since 0.1.0
		 */
		static function add_inquirer() {
			?>
				<h3>Add Inquirer:</h3>
				<form action='admin-post.php' method='post' class='inquirer_add-inquirer-form'>
					<input type='hidden' name='action' value='add_inquirer_action'>
					<input type='hidden' name='nonce_ya_biznas' value='<?php echo esc_textarea( self::$inquirer_add_meta_nonce );?>'/>
					<label for='inquirer_name'>Name:</label>
					<input type='text' id='inquirer_name' name='new_inquirer[inquirer_name]' pattern='(\w+(?: \w+)*)' title='Only alphanumeric characters and single spaces must be used.' required>
					<label for='inquirer_url'>URL:</label>
					<input type='url' id='inquirer_url' name='new_inquirer[inquirer_url]' placeholder='https://ecotechie.io/' aria-label='Enter a new sitechecker URL' pattern='https://(?:.){4,80}' title='Begin URL with https://' minlength='12' maxlength='80' required>
					<label for='inquirer_type' class='inquirer_select-query-types'>Select Type(s):</label>
					<fieldset class='inquirer_checker-type'>
						<label>
							<input type='checkbox' name='new_inquirer[inquirer_type][]' value='Accessibility'>
							Accessibility
						</label>
						<label>
							<input type='checkbox' name='new_inquirer[inquirer_type][]' value='Core Web Vitals'>
							Core Web Vitals
						</label>
						<label>
							<input type='checkbox' name='new_inquirer[inquirer_type][]' value='Security'>
							Security
						</label>
						<label>
							<input type='checkbox' name='new_inquirer[inquirer_type][]' value='SEO'>
							SEO
						</label>
						<label>
							<input type='checkbox' name='new_inquirer[inquirer_type][]' value='Social'>
							Social
						</label>
						<label>
							<input type='checkbox' name='new_inquirer[inquirer_type][]' value='Speed'>
							Speed
						</label>
						<label>
							<input type='text' name='new_inquirer[inquirer_type][]' placeholder='Other Inquirer Type...'>
						</label>
					</fieldset>
					<label for='inquirer_testing_url'>Testing URL:</label>
					<input type='url' id='inquirer_testing_url' name='new_inquirer[inquirer_testing_url]' placeholder='https://ecotechie.io/?=url' aria-label='Enter a new sitechecker testing URL' pattern='https://(?:.){5,100}' title='Begin URL with https://' minlength='12' maxlength='100' required>
					<button  name='add_inquirer' class='button button-primary'>Add Inquirer</button>
				</form>
			<?php
		}

		/**
		 * Save Inquirer
		 *
		 * @since 0.1.0
		 */
		static function save_inquirer() {
			if ( isset( $_POST['new_inquirer']['inquirer_name'] ) && $_POST['new_inquirer']['inquirer_name'] != '' && isset( $_POST['nonce_ya_biznas'] ) && wp_verify_nonce( $_POST['nonce_ya_biznas'], 'inquirer_add_meta_form_nonce' ) ) {
				$new_inquirer                          = array();
				$new_inquirer['inquirer_name']         = sanitize_text_field( $_POST['new_inquirer']['inquirer_name'] );
				$new_inquirer['inquirer_url']          = esc_url_raw( $_POST['new_inquirer']['inquirer_url'] );
				$new_inquirer['inquirer_type']         = array_unique( $_POST['new_inquirer']['inquirer_type'] );
				$new_inquirer['inquirer_type']         = array_map( 'sanitize_text_field', $new_inquirer['inquirer_type'] );
				$new_inquirer['inquirer_type']         = array_filter( $new_inquirer['inquirer_type'] );
				$new_inquirer['inquirer_testing_url']  = esc_url_raw( $_POST['new_inquirer']['inquirer_testing_url'] );
				$new_inquirer['inquirer_icon']         = esc_url_raw( $_POST['new_inquirer']['inquirer_icon'] );
				self::$options['inquirer_list'][]      = $new_inquirer;
				update_option( 'inquirer_settings', self::$options );
				wp_redirect( admin_url( 'options-general.php?page=inquirer' ), '301', 'Inquirer Plugin' );
				exit;
			} else {
				wp_redirect( admin_url( 'options-general.php?page=inquirer' ) );
				exit;
			}
		}

		/**
		 * Build plugin options page.
		 *
		 * @since 0.1.
		 */
		public static function options_page() {
			settings_fields( 'inquirer_page' );
			do_settings_sections( 'inquirer_page' );
		}
	}

	/**
	* Init the plugin.
	*/
	add_action( 'init', array( 'EcoTechie\Inquirer\Settings', 'get_instance' ) );
}
