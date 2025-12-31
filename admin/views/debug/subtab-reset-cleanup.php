<?php
/**
 * Debug/Erweitert Subtab: Reset & Cleanup
 *
 * @package ChurchTools_Suite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cts-debug-subtab-content">
	<h2>🗑️ Reset & Cleanup</h2>
	<p class="description"><?php esc_html_e('Vorsicht: Diese Aktionen löschen Daten aus der Datenbank. Die Einstellungen (ChurchTools-Verbindung, Auswahlen) bleiben erhalten.', 'churchtools-suite'); ?></p>

	<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">

		<!-- Clear Events -->
		<div style="padding: 15px; background: #f9f9f9; border-radius: 5px;">
			<h4 style="margin: 0 0 10px 0;">📅 <?php esc_html_e('Events löschen', 'churchtools-suite'); ?></h4>
			<p style="font-size: 13px; color: #666; margin-bottom: 10px;">
				<?php esc_html_e('Löscht alle Events aus der Datenbank.', 'churchtools-suite'); ?>
			</p>
			<button type="button" class="button" id="cts-clear-events" style="width: 100%;">
				<?php esc_html_e('Events löschen', 'churchtools-suite'); ?>
			</button>
		</div>

		<!-- Clear Calendars -->
		<div style="padding: 15px; background: #f9f9f9; border-radius: 5px;">
			<h4 style="margin: 0 0 10px 0;">🗓️ <?php esc_html_e('Kalender löschen', 'churchtools-suite'); ?></h4>
			<p style="font-size: 13px; color: #666; margin-bottom: 10px;">
				<?php esc_html_e('Löscht alle Kalender aus der Datenbank.', 'churchtools-suite'); ?>
			</p>
			<button type="button" class="button" id="cts-clear-calendars" style="width: 100%;">
				<?php esc_html_e('Kalender löschen', 'churchtools-suite'); ?>
			</button>
		</div>

		<!-- Clear Services -->
		<div style="padding: 15px; background: #f9f9f9; border-radius: 5px;">
			<h4 style="margin: 0 0 10px 0;">👥 <?php esc_html_e('Services löschen', 'churchtools-suite'); ?></h4>
			<p style="font-size: 13px; color: #666; margin-bottom: 10px;">
				<?php esc_html_e('Löscht alle Services und Service-Gruppen.', 'churchtools-suite'); ?>
			</p>
			<button type="button" class="button" id="cts-clear-services" style="width: 100%;">
				<?php esc_html_e('Services löschen', 'churchtools-suite'); ?>
			</button>
		</div>

		<!-- Clear Sync History -->
		<div style="padding: 15px; background: #f9f9f9; border-radius: 5px;">
			<h4 style="margin: 0 0 10px 0;">📊 <?php esc_html_e('Sync-Historie löschen', 'churchtools-suite'); ?></h4>
			<p style="font-size: 13px; color: #666; margin-bottom: 10px;">
				<?php esc_html_e('Löscht die gesamte Sync-Historie.', 'churchtools-suite'); ?>
			</p>
			<button type="button" class="button" id="cts-clear-sync-history" style="width: 100%;">
				<?php esc_html_e('Historie löschen', 'churchtools-suite'); ?>
			</button>
		</div>

		<!-- Full Reset -->
		<div style="padding: 15px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 5px;">
			<h4 style="margin: 0 0 10px 0;">⚠️ <?php esc_html_e('Kompletter Reset', 'churchtools-suite'); ?></h4>
			<p style="font-size: 13px; color: #856404; margin-bottom: 10px;">
				<?php esc_html_e('Löscht ALLE Daten (Events, Kalender, Services, Sync-Historie). Einstellungen bleiben erhalten.', 'churchtools-suite'); ?>
			</p>
			<button type="button" class="button button-primary" id="cts-full-reset" style="width: 100%; background: #d63638; border-color: #d63638;">
				<?php esc_html_e('Komplett zurücksetzen', 'churchtools-suite'); ?>
			</button>
		</div>

	</div>


<!-- Reset & Cleanup actions handled in assets/js/churchtools-suite-admin.js (initResetCleanup) -->
</div>
