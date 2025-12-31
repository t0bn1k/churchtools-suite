<?php
/**
 * Debug/Erweitert Subtab: Logs
 *
 * @package ChurchTools_Suite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cts-debug-subtab-content">
	<h2>📝 Logs</h2>
	<p>Hier können Sie die letzten Log-Einträge einsehen und das Log löschen.</p>
	<div class="cts-card">
		<h3>Service Import Logs</h3>
			<div class="cts-flex cts-flex-wrap cts-gap-12 cts-mb-30">
			<button type="button" id="cts-reload-logs" class="cts-button cts-button-primary">
				<span>🔄</span> Logs neu laden
			</button>
			<button type="button" id="cts-clear-logs" class="cts-button cts-button-danger">
				<span>🗑️</span> Logs löschen
			</button>
		</div>
		<div id="cts-log-content" class="cts-card-muted" style="max-height:400px; overflow-y:auto; font-family: 'Courier New', monospace; font-size:12px; line-height:1.6;">
			<?php
			$log_entries = ChurchTools_Suite_Logger::get_log_content(200); // Letzte 200 Einträge als Array
			if ( empty( $log_entries ) ) {
				echo '<span style="color: #8c8f94;">Keine Logs verfügbar. Führen Sie einen Sync aus, um Logs zu generieren.</span>';
				echo '<span class="cts-muted">Keine Logs verfügbar. Führen Sie einen Sync aus, um Logs zu generieren.</span>';
			} else {
				if ( ! function_exists( 'esc_html' ) ) {
					function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ); }
				}
				foreach ( $log_entries as $entry ) {
					$color = '#d4d4d4';
					switch ( $entry['level'] ?? '' ) {
						case 'error': $color = '#f48771'; break;
						case 'warning': $color = '#dcdcaa'; break;
						case 'info': $color = '#4ec9b0'; break;
						case 'debug': $color = '#9cdcfe'; break;
						case 'critical': $color = '#ff1744'; break;
					}
					$time = esc_html( $entry['timestamp'] ?? '' );
					$level = strtoupper( esc_html( $entry['level'] ?? '' ) );
					$context = esc_html( $entry['context'] ?? '' );
					$msg = esc_html( $entry['message'] ?? '' );
					echo '<div style="color:'.$color.';margin-bottom:2px;">';
					echo '<span class="cts-muted-small">['.$time.']</span> ';
					echo '<span class="cts-log-level">['.$level.']</span> ';
					echo '<span class="cts-status-badge" style="background:#b388ff;color:#fff;">['.$context.']</span> ';
					echo $msg;
					echo '</div>';
				}
			}
			?>
		</div>
	</div>
</div>

<!-- Log-Buttons werden zentral in assets/js/churchtools-suite-admin.js verarbeitet -->
