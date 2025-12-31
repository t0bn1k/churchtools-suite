<?php
/**
 * Admin Area Handler
 *
 * @package ChurchTools_Suite
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ChurchTools_Suite_Admin {
	
	/**
	 * Plugin version
	 */
	private string $version;
	
	/**
	 * Initialize admin area
	 */
	public function __construct( string $version ) {
		$this->version = $version;
	}

	/**
	 * AJAX Handler: Run update now (performs installation) — requires additional confirmation
	 */
	public function ajax_run_update() {
		// Clean any previous output to avoid HTML before JSON
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		// Check nonce
		check_ajax_referer( 'churchtools_suite_admin', 'nonce' );

		// Permission
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Keine Berechtigung.', 'churchtools-suite' ) ] );
			return;
		}

		try {
			if ( ! class_exists( 'ChurchTools_Suite_Auto_Updater' ) ) {
				require_once CHURCHTOOLS_SUITE_PATH . 'includes/class-churchtools-suite-auto-updater.php';
			}

			$result = ChurchTools_Suite_Auto_Updater::run_update_now();

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( [ 'message' => $result->get_error_message() ] );
				return;
			}

			wp_send_json_success( [ 'message' => $result['message'] ?? __( 'Update gestartet.', 'churchtools-suite' ) ] );
		} catch ( Exception $e ) {
			wp_send_json_error( [ 'message' => __( 'Fehler: ', 'churchtools-suite' ) . $e->getMessage() ] );
		}
	}

	/**
	 * AJAX Handler: Manual Update Trigger
	 * Triggers the auto-updater check immediately.
	 */
	public function ajax_manual_update() {
		// Clean any previous output to avoid HTML before JSON
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		// Check nonce
		check_ajax_referer( 'churchtools_suite_admin', 'nonce' );

		// Permission
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Keine Berechtigung.', 'churchtools-suite' ) ] );
			return;
		}

		   try {
			   if ( ! class_exists( 'ChurchTools_Suite_Auto_Updater' ) ) {
				   require_once CHURCHTOOLS_SUITE_PATH . 'includes/class-churchtools-suite-auto-updater.php';
			   }

			   // Logging: Start manuelle Update-Prüfung
			   if ( class_exists( 'ChurchTools_Suite_Logger' ) ) {
				   ChurchTools_Suite_Logger::info('updater', 'Manuelle Update-Prüfung gestartet', [
					   'current_version' => defined('CHURCHTOOLS_SUITE_VERSION') ? CHURCHTOOLS_SUITE_VERSION : null,
					   'user' => get_current_user_id(),
					   'ip' => $_SERVER['REMOTE_ADDR'] ?? null
				   ]);
			   }

			   // Only check availability — do NOT perform the update from the admin button
			   $info = ChurchTools_Suite_Auto_Updater::get_latest_release_info();
			   if ( is_wp_error( $info ) ) {
				   if ( class_exists( 'ChurchTools_Suite_Logger' ) ) {
					   ChurchTools_Suite_Logger::error('updater', 'Fehler bei manueller Update-Prüfung', [ 'error' => $info->get_error_message() ]);
				   }
				   wp_send_json_error( [ 'message' => __( 'Fehler beim Abrufen der Release-Informationen.', 'churchtools-suite' ), 'error' => $info->get_error_message() ] );
				   return;
			   }

			   if ( class_exists( 'ChurchTools_Suite_Logger' ) ) {
				   ChurchTools_Suite_Logger::info('updater', 'Manuelle Update-Prüfung abgeschlossen', [
					   'found_update' => !empty($info['is_update']),
					   'latest_version' => $info['latest_version'] ?? null,
					   'tag_name' => $info['tag_name'] ?? null,
					   'zip_url' => $info['zip_url'] ?? null
				   ]);
			   }

			   wp_send_json_success( [ 'message' => __( 'Update-Prüfung abgeschlossen.', 'churchtools-suite' ), 'data' => $info ] );
		   } catch ( Exception $e ) {
			   if ( class_exists( 'ChurchTools_Suite_Logger' ) ) {
				   ChurchTools_Suite_Logger::error('updater', 'Exception bei manueller Update-Prüfung', [ 'exception' => $e->getMessage() ]);
			   }
			   wp_send_json_error( [ 'message' => __( 'Fehler: ', 'churchtools-suite' ) . $e->getMessage() ] );
		   }
	}
	
	/**
	 * Enqueue admin styles
	 * 
	 * @since 0.6.1.0 Always load (no conditional check)
	 * @since 0.6.1.5 Load public CSS first, then admin CSS (correct order)
	 */
	public function enqueue_styles() {
		// Load public CSS first (for demos in admin area)
		wp_enqueue_style(
			'churchtools-suite-public',
			CHURCHTOOLS_SUITE_URL . 'assets/css/churchtools-suite-public.css',
			[],
			$this->version
		);
		
		// Load admin CSS after (depends on public CSS)
		wp_enqueue_style(
			'churchtools-suite-admin',
			CHURCHTOOLS_SUITE_URL . 'assets/css/churchtools-suite-admin.css',
			[ 'churchtools-suite-public' ],
			$this->version
		);
	}
	
	/**
	 * Enqueue admin scripts
	 * 
	 * @since 0.6.1.0 Always load (no conditional check)
	 */
	public function enqueue_scripts() {
		// Main admin script (jQuery-based)
		wp_enqueue_script(
			'churchtools-suite-admin',
			CHURCHTOOLS_SUITE_URL . 'assets/js/churchtools-suite-admin.js',
			[ 'jquery' ],
			$this->version,
			true
		);
		
		wp_localize_script(
			'churchtools-suite-admin',
			'churchtoolsSuite',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'churchtools_suite_admin' ),
				'version' => $this->version,
				'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
			]
		);
	}

	/**
	 * Add plugin admin menu
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(
			__( 'ChurchTools Suite', 'churchtools-suite' ),
			__( 'ChurchTools', 'churchtools-suite' ),
			'manage_options',
			'churchtools-suite',
			[ $this, 'display_admin_page' ],
			'dashicons-calendar-alt',
			30
		);
		
		// Add Shortcode Manager as submenu
		add_submenu_page(
			'churchtools-suite',
			__( 'Shortcode Manager', 'churchtools-suite' ),
			__( '⚡ Shortcode Manager', 'churchtools-suite' ),
			'manage_options',
			'churchtools-suite-shortcodes',
			[ $this, 'display_shortcode_manager' ]
		);
		
		// Shortcode Demo removed as separate submenu — demo is now integrated into Shortcode Manager

		// Add Data subpage (separate admin page for large lists)
		add_submenu_page(
			'churchtools-suite',
			__( 'Daten', 'churchtools-suite' ),
			__( '📋 Daten', 'churchtools-suite' ),
			'manage_options',
			'churchtools-suite-data',
			[ $this, 'display_data_page' ]
		);

		// Add Documentation subpage
		add_submenu_page(
			'churchtools-suite',
			__( 'Dokumentation', 'churchtools-suite' ),
			__( '📚 Dokumentation', 'churchtools-suite' ),
			'manage_options',
			'churchtools-suite-docs',
			[ $this, 'display_documentation_page' ]
		);

		// Note: Settings, Sync and Debug are handled as tabs in the main admin page
		// (admin/views/admin-page.php) — no separate submenu entries are added here.
	}

	/**
	 * Display main admin page
	 */
	public function display_admin_page() {
		// Get active tab
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
		
		// Include view
		include_once CHURCHTOOLS_SUITE_PATH . 'admin/views/admin-page.php';
	}

	/**
	 * Display shortcode manager page
	 */
	public function display_shortcode_manager() {
		include_once CHURCHTOOLS_SUITE_PATH . 'admin/views/shortcode-manager.php';
	}

	/**
	 * Display shortcode demo page
	 */
	public function display_shortcode_demo() {
		include_once CHURCHTOOLS_SUITE_PATH . 'admin/views/shortcode-demo.php';
	}

	/**
	 * Display Data page (dedicated subpage)
	 */
	public function display_data_page() {
		// Reuse existing data subtab view
		include_once CHURCHTOOLS_SUITE_PATH . 'admin/views/tab-data.php';
	}

	/**
	 * Display Documentation page (dedicated subpage)
	 */
	public function display_documentation_page() {
		include_once CHURCHTOOLS_SUITE_PATH . 'admin/views/tab-documentation.php';
	}

	/**
	 * Check if current page is a plugin page
	 */
	private function is_plugin_page(): bool {
		$screen = get_current_screen();
		return $screen && strpos( $screen->id, 'churchtools-suite' ) !== false;
	}

	/**
	 * Register AJAX handlers
	 */
	public function register_ajax_handlers() {
		add_action( 'wp_ajax_cts_test_connection', [ $this, 'ajax_test_connection' ] );
		add_action( 'wp_ajax_cts_sync_calendars', [ $this, 'ajax_sync_calendars' ] );
		add_action( 'wp_ajax_cts_save_calendar_selection', [ $this, 'ajax_save_calendar_selection' ] );
		add_action( 'wp_ajax_cts_sync_service_groups', [ $this, 'ajax_sync_service_groups' ] );
		add_action( 'wp_ajax_cts_save_service_group_selection', [ $this, 'ajax_save_service_group_selection' ] );
		add_action( 'wp_ajax_cts_sync_services', [ $this, 'ajax_sync_services' ] );
		add_action( 'wp_ajax_cts_save_service_selection', [ $this, 'ajax_save_service_selection' ] );
		add_action( 'wp_ajax_cts_sync_events', [ $this, 'ajax_sync_events' ] );
		add_action( 'wp_ajax_cts_trigger_manual_sync', [ $this, 'ajax_trigger_manual_sync' ] );
		add_action( 'wp_ajax_cts_manual_update', [ $this, 'ajax_manual_update' ] );
		add_action( 'wp_ajax_cts_run_update', [ $this, 'ajax_run_update' ] );
		add_action( 'wp_ajax_cts_trigger_keepalive', [ $this, 'ajax_trigger_keepalive' ] );
		// Simple ping endpoint to verify AJAX/JSON pipeline
		add_action( 'wp_ajax_cts_keepalive_ping', [ $this, 'ajax_keepalive_ping' ] );
		add_action( 'wp_ajax_cts_reload_logs', [ $this, 'ajax_reload_logs' ] );
		add_action( 'wp_ajax_cts_clear_logs', [ $this, 'ajax_clear_logs' ] );
		add_action( 'wp_ajax_cts_clear_block_logs', [ $this, 'ajax_clear_block_logs' ] );
		add_action( 'wp_ajax_cts_save_preset', [ $this, 'ajax_save_preset' ] );
		add_action( 'wp_ajax_cts_update_preset', [ $this, 'ajax_update_preset' ] );
		add_action( 'wp_ajax_cts_delete_preset', [ $this, 'ajax_delete_preset' ] );
		add_action( 'wp_ajax_cts_get_calendars', [ $this, 'ajax_get_calendars' ] );
		// AJAX data lists (server-side filtering/pagination)
		add_action( 'wp_ajax_cts_fetch_events_list', [ $this, 'ajax_fetch_events_list' ] );
		add_action( 'wp_ajax_cts_fetch_imported_services_list', [ $this, 'ajax_fetch_imported_services_list' ] );
		
		// Reset & Cleanup (v0.7.2.4)
		add_action( 'wp_ajax_cts_clear_events', [ $this, 'ajax_clear_events' ] );
		add_action( 'wp_ajax_cts_clear_calendars', [ $this, 'ajax_clear_calendars' ] );
		add_action( 'wp_ajax_cts_clear_services', [ $this, 'ajax_clear_services' ] );
		add_action( 'wp_ajax_cts_clear_sync_history', [ $this, 'ajax_clear_sync_history' ] );
		add_action( 'wp_ajax_cts_full_reset', [ $this, 'ajax_full_reset' ] );
		
		// Public AJAX (for frontend modal)
		add_action( 'wp_ajax_cts_get_modal_template', [ $this, 'ajax_get_modal_template' ] );
		add_action( 'wp_ajax_nopriv_cts_get_modal_template', [ $this, 'ajax_get_modal_template' ] );
		add_action( 'wp_ajax_cts_get_event_details', [ $this, 'ajax_get_event_details' ] );
		add_action( 'wp_ajax_nopriv_cts_get_event_details', [ $this, 'ajax_get_event_details' ] );
	}

	/**
	 * AJAX Handler: Test ChurchTools Connection
	 */
	public function ajax_test_connection() {
		// Check nonce
		check_ajax_referer( 'churchtools_suite_admin', 'nonce' );
	}

	/**
	 * AJAX Handler: Keepalive ping (test endpoint)
	 * Returns simple JSON to validate that admin-ajax.php returns JSON correctly.
	 */
	public function ajax_keepalive_ping() {
		// Use non-fatal nonce check and return JSON
		$ok = check_ajax_referer( 'churchtools_suite_admin', 'nonce', false );
		if ( $ok === false ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
			return;
		}

		// proceed with full keepalive flow (permission check continued below)
		
		// Rate Limiting (v0.7.0.2)
		require_once CHURCHTOOLS_SUITE_PATH . 'includes/class-churchtools-suite-rate-limiter.php';
		
		$user_id = get_current_user_id();
		$identifier = 'user_' . $user_id;
		
		if ( ! ChurchTools_Suite_Rate_Limiter::is_allowed( $identifier, 'ajax' ) ) {
			wp_send_json_error( [
				'message' => __( 'Zu viele Anfragen. Bitte warten Sie einen Moment.', 'churchtools-suite' )
			] );
		}

		// Load CT Client
		require_once CHURCHTOOLS_SUITE_PATH . 'includes/class-churchtools-suite-ct-client.php';
		
		$client = new ChurchTools_Suite_CT_Client();
		$result = $client->test_connection();
		
		if ( $result['success'] ) {
			wp_send_json_success( [
				'message' => $result['message'],
				'user_info' => $result['user_info'] ?? null
			] );
		} else {
			wp_send_json_error( [
				'message' => $result['message']
			] );
		}
	}

	/**
	 * AJAX Handler: Sync Calendars from ChurchTools
	 */
	public function ajax_sync_calendars() {
		// Check nonce
		check_ajax_referer( 'churchtools_suite_admin', 'nonce' );
		
		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [
				'message' => __( 'Keine Berechtigung.', 'churchtools-suite' )
			] );
			return;
		}

		// Rate Limiting (v0.7.0.2)
		require_once CHURCHTOOLS_SUITE_PATH . 'includes/class-churchtools-suite-rate-limiter.php';
		
		$user_id = get_current_user_id();
		$identifier = 'user_' . $user_id;
		
		if ( ! ChurchTools_Suite_Rate_Limiter::is_allowed( $identifier, 'ajax' ) ) {
			wp_send_json_error( [
				'message' => __( 'Zu viele Anfragen. Bitte warten Sie einen Moment.', 'churchtools-suite' )
			] );
		}

