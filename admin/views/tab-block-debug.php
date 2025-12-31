<?php
/**
 * Block Debug Tab
 *
 * @package ChurchTools_Suite
 * @since   0.5.9.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get block debug logs from options
$block_logs = get_option( 'churchtools_suite_block_debug_logs', [] );
$block_status = get_option( 'churchtools_suite_block_status', [] );

// Reverse logs (newest first)
$block_logs = array_reverse( $block_logs );
?>

<div class="cts-tab-content">
	
	<div class="cts-section-header">
		<h2>🔍 Block Debug Informationen</h2>
		<p class="cts-section-description">
			Live-Debugging für Gutenberg Blocks. Zeigt Registration und Rendering-Status.
		</p>
	</div>
	
	<!-- Block Status -->
	<div class="cts-card cts-maxw-1200 cts-mb-20">
		<div class="cts-card-header">
			<span class="cts-card-icon">📊</span>
			<h3>Block Registration Status</h3>
		</div>
		<div class="cts-card-body">
			<?php if ( ! empty( $block_status ) ) : ?>
				<table class="cts-debug-table">
					<thead>
						<tr>
							<th class="cts-w-30">Block</th>
							<th class="cts-w-20">Status</th>
							<th class="cts-w-50">Details</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $block_status as $block_id => $status ) : ?>
							<tr>
								<td><code><?php echo esc_html( $block_id ); ?></code></td>
								<td>
									<?php if ( $status['registered'] ) : ?>
										<span class="cts-status-success cts-font-600">✅ Registriert</span>
									<?php else : ?>
										<span class="cts-status-error cts-font-600">❌ Fehler</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if ( isset( $status['render_callback'] ) ) : ?>
										<small>Callback: <code><?php echo esc_html( $status['render_callback'] ); ?></code></small><br>
									<?php endif; ?>
									<?php if ( isset( $status['last_render'] ) ) : ?>
										<small>Letztes Rendering: <?php echo esc_html( $status['last_render'] ); ?></small>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p class="cts-muted cts-italic">Keine Block-Status-Daten verfügbar. Bitte Seite neu laden.</p>
			<?php endif; ?>
		</div>
	</div>
	
	<!-- Shortcode Parameter Test -->
	<div class="cts-card cts-maxw-1200 cts-mb-20">
		<div class="cts-card-header">
			<span class="cts-card-icon">🧪</span>
			<h3>Shortcode Parameter Test</h3>
		</div>
		<div class="cts-card-body">
			<p class="cts-mb-15 cts-muted">
				Teste die boolean-Parameter direkt:
			</p>

			<div class="cts-block-sample cts-mb-20">
				<h4 class="cts-mb-15">Test mit allen Parametern TRUE:</h4>
				<code class="cts-code cts-display-block cts-mb-10">[cts_list view="classic" limit="3" show_services="true" show_description="true" show_location="true"]</code>
				<div class="cts-block-inner cts-border-primary">
					<?php echo do_shortcode('[cts_list view="classic" limit="3" show_services="true" show_description="true" show_location="true"]'); ?>
				</div>
			</div>

			<div class="cts-block-sample-error cts-mb-20">
				<h4 class="cts-mb-15">Test mit allen Parametern FALSE:</h4>
				<code class="cts-code cts-display-block cts-mb-10">[cts_list view="classic" limit="3" show_services="false" show_description="false" show_location="false"]</code>
				<div class="cts-block-inner cts-border-error">
					<?php echo do_shortcode('[cts_list view="classic" limit="3" show_services="false" show_description="false" show_location="false"]'); ?>
				</div>
			</div>

			<div class="cts-block-sample-warn cts-mb-0">
				<h4 class="cts-mb-15">Test OHNE Parameter (soll Defaults TRUE verwenden):</h4>
				<code class="cts-code cts-display-block cts-mb-10">[cts_list view="classic" limit="3"]</code>
				<div class="cts-block-inner cts-border-warn">
					<?php echo do_shortcode('[cts_list view="classic" limit="3"]'); ?>
				</div>
			</div>

			<div class="cts-block-info cts-mt-20">
				<strong>💡 Erwartetes Verhalten:</strong>
				<ul class="cts-list-reset cts-ml-20 cts-line-height-18">
					<li><strong>TRUE</strong>: Services, Beschreibung und Ort werden angezeigt</li>
					<li><strong>FALSE</strong>: Services, Beschreibung und Ort werden ausgeblendet</li>
					<li><strong>OHNE</strong>: Wie TRUE (Default-Verhalten)</li>
				</ul>
			</div>
		</div>
	</div>
	
	<!-- Live Debug Log -->
	<div class="cts-card cts-maxw-1200">
		<div class="cts-card-header">
			<span class="cts-card-icon">📝</span>
			<h3>Live Debug Log</h3>
		</div>
		<div class="cts-card-body">
			<div style="margin-bottom: 15px;">
				<button type="button" class="cts-button cts-button-secondary" id="cts-reload-block-logs">
					<span>🔄</span> Log neu laden
				</button>
				<button type="button" class="cts-button cts-button-secondary cts-ml-10" id="cts-clear-block-logs">
					<span>🗑️</span> Log löschen
				</button>
			</div>
			<div id="cts-block-log-content" class="cts-log-content">
				<?php if ( ! empty( $block_logs ) ) : ?>
					<?php foreach ( $block_logs as $log ) : ?>
						<?php
						$timestamp = isset( $log['time'] ) ? date( 'H:i:s', $log['time'] ) : '';
						$level = $log['level'] ?? 'info';
						$message = $log['message'] ?? '';
						
						// Color coding
						$color = '#d4d4d4';
						$icon = '•';
						if ( strpos( $message, '🔴' ) !== false ) {
							$color = '#f48771';
							$icon = '🔴';
						} elseif ( strpos( $message, '🟢' ) !== false ) {
							$color = '#89d185';
							$icon = '🟢';
						} elseif ( strpos( $message, '🟡' ) !== false ) {
							$color = '#e5c07b';
							$icon = '🟡';
						} elseif ( strpos( $message, '⚠️' ) !== false ) {
							$color = '#e5c07b';
							$icon = '⚠️';
						}
						?>
						<div class="cts-log-line cts-mb-8" style="color: <?php echo esc_attr( $color ); ?>;">
							<span style="color: #6a9fb5;">[<?php echo esc_html( $timestamp ); ?>]</span>
							<?php echo esc_html( $message ); ?>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="cts-muted cts-italic">
						Keine Logs vorhanden. Füge einen Block in einer Seite ein, um Logs zu sehen.
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<!-- Instructions -->
	<div class="cts-card cts-maxw-1200 cts-mt-20">
		<div class="cts-card-header">
			<span class="cts-card-icon">ℹ️</span>
			<h3>Anleitung</h3>
		</div>
		<div class="cts-card-body">
			<ol class="cts-line-height-18">
				<li>Öffne eine Seite oder Beitrag im <strong>Block-Editor</strong></li>
				<li>Füge einen <strong>"ChurchTools Calendar NEU"</strong> Block ein</li>
				<li>Speichere und <strong>lade die Seite im Frontend</strong></li>
				<li>Komme zurück zu diesem Tab und klicke <strong>"Log neu laden"</strong></li>
				<li>Du siehst dann die Debug-Ausgaben mit 🔴 (Registration) und 🟢 (Rendering)</li>
			</ol>
			
			<h4 class="cts-mt-20 cts-mb-10">Log-Symbole:</h4>
			<ul class="cts-list-reset">
				<li>🔴 <strong>Rot</strong> - Block Registration (beim Laden)</li>
				<li>🟢 <strong>Grün</strong> - Block Rendering (beim Anzeigen)</li>
				<li>🟡 <strong>Gelb</strong> - Warnungen</li>
				<li>⚠️ <strong>Warnung</strong> - Fehler oder Probleme</li>
			</ul>
		</div>
	</div>
	
<!-- Inline JS migrated to assets/js/churchtools-suite-admin.js -> initBlockDebug() -->

