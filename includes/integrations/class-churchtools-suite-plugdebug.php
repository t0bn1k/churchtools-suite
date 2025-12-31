<?php
/**
 * PlugDebug integration
 *
 * Exposes ChurchTools Suite logs via a secure REST endpoint so external
 * debugging tools (e.g. PlugDebug) can fetch recent log entries.
 *
 * @package ChurchTools_Suite
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ChurchTools_Suite_PlugDebug {

    /**
     * Register hooks
     */
    public static function init(): void {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    /**
     * Register REST routes
     */
    public static function register_routes(): void {
        register_rest_route(
            'churchtools-suite/v1',
            '/logs',
            [
                'methods'  => 'GET',
                'callback' => [ __CLASS__, 'get_logs' ],
                'permission_callback' => [ __CLASS__, 'permission_check' ],
            ]
        );
    }

    /**
     * Permission check: only admins (manage_options) can read logs
     */
    public static function permission_check(): bool {
        return current_user_can( 'manage_options' );
    }

    /**
     * REST callback: return recent log entries + stats
     * Query params:
     * - lines: int (default 100)
     */
    public static function get_logs( WP_REST_Request $request ): WP_REST_Response {
        $lines = (int) $request->get_param( 'lines' );
        if ( $lines <= 0 ) {
            $lines = 100;
        }

        if ( ! class_exists( 'ChurchTools_Suite_Logger' ) ) {
            require_once CHURCHTOOLS_SUITE_PATH . 'includes/class-churchtools-suite-logger.php';
        }

        $entries = ChurchTools_Suite_Logger::get_log_content( $lines );
        $stats = ChurchTools_Suite_Logger::get_statistics();

        return rest_ensure_response( [
            'success' => true,
            'data'    => [
                'entries' => $entries,
                'stats'   => $stats,
            ],
        ] );
    }
}

// Initialize integration
ChurchTools_Suite_PlugDebug::init();
