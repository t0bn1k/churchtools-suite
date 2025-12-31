<?php
/**
 * Debug/Erweitert Subtab: Manuelle Trigger
 *
 * @package ChurchTools_Suite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cts-debug-subtab-content">
	<h2>⚡ Manuelle Trigger</h2>
	<p>Führen Sie manuelle Aktionen wie Sync, Keepalive oder Update-Checks aus.</p>
	<div class="cts-card">
		<h3>🔄 Event-Sync & Session</h3>
		<div style="display: flex; gap: 12px; flex-wrap: wrap;">
			<button type="button" id="cts-trigger-manual-sync" class="cts-button cts-button-primary">
				<span>🔄</span> Event-Sync jetzt ausführen
			</button>
			<button type="button" id="cts-trigger-keepalive" class="cts-button cts-button-secondary">
				<span>💓</span> Session Keepalive
			</button>
		</div>
		<div id="cts-manual-trigger-result" style="margin-top: 16px;"></div>
	</div>
	<div class="cts-card" style="margin-top:24px;">
		<h3>🛠️ Update & Log</h3>
		<div style="display: flex; gap: 12px; flex-wrap: wrap;">
			<button type="button" id="cts-manual-update" class="cts-button">
				<span>🔄</span> Manuelles Update prüfen
			</button>
			<button type="button" id="cts-clear-logs" class="cts-button cts-button-danger">
				<span>🗑️</span> Log löschen
			</button>
		</div>

	</div>
	</div>
