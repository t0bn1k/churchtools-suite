<?php
/**
 * Tab: Calendars
 *
 * @package ChurchTools_Suite
 * @since   0.3.5.0
 */

if (!defined('ABSPATH')) {
	exit;
}

// Load calendars from database
global $wpdb;
require_once CHURCHTOOLS_SUITE_PATH . 'includes/repositories/class-churchtools-suite-repository-base.php';
require_once CHURCHTOOLS_SUITE_PATH . 'includes/repositories/class-churchtools-suite-calendars-repository.php';

$calendars_repo = new ChurchTools_Suite_Calendars_Repository();
$calendars = $calendars_repo->get_all();
$selected_count = $calendars_repo->count_selected();
$last_sync = get_option('churchtools_suite_calendars_last_sync', null);
?>

<div class="cts-tab-content-inner">

   <!-- Kalender Sync Button -->
   <div class="cts-card cts-card-muted-accent cts-mt-20 cts-mb-0">
	   <div class="cts-card-header">
		   <h2 class="cts-mt-0">📅 <?php esc_html_e('Kalender', 'churchtools-suite'); ?></h2>
		   <button id="cts-sync-calendars-btn" class="button button-secondary cts-btn-sync">
			   <span class="dashicons dashicons-update"></span> <?php esc_html_e('Kalender synchronisieren', 'churchtools-suite'); ?>
		   </button>
	   </div>
	   <div id="cts-sync-calendars-result" class="cts-mt-8 cts-accent"></div>
   </div>

   <!-- Calendar Selection Card -->
   <div class="cts-card cts-mt-20">
	   <div class="cts-card-header">
		   <h2>✅ <?php esc_html_e('Kalenderauswahl', 'churchtools-suite'); ?></h2>
	   </div>
		</div>


		<div class="cts-card-body">
			
			<?php if (empty($calendars)): ?>
				<div class="notice notice-info inline">
					<p>
						<?php esc_html_e('Keine Kalender vorhanden. Bitte synchronisieren Sie zuerst die Kalender von ChurchTools.', 'churchtools-suite'); ?>
					</p>
				</div>
			<?php else: ?>
				
				<p class="description">
					<?php
					printf(
						esc_html__('Wählen Sie die Kalender aus, deren Termine synchronisiert werden sollen. Aktuell ausgewählt: %d von %d', 'churchtools-suite'),
						(int) $selected_count,
						count($calendars)
					);
					?>
				</p>
				
				<form method="post" id="cts-calendar-selection-form">
					<?php wp_nonce_field('cts_calendar_selection', 'cts_calendar_selection_nonce'); ?>
					
					<table class="widefat cts-mt-15">
						<thead>
							<tr>
								<th class="cts-w-40">
									<input type="checkbox" id="cts-select-all-calendars">
								</th>
								<th><?php esc_html_e('Kalender', 'churchtools-suite'); ?></th>
								<th><?php esc_html_e('ChurchTools-ID', 'churchtools-suite'); ?></th>
								<th><?php esc_html_e('Sichtbarkeit', 'churchtools-suite'); ?></th>
								<th><?php esc_html_e('Farbe', 'churchtools-suite'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($calendars as $calendar): ?>
								<tr>
									<td>
										<input 
											type="checkbox" 
											name="selected_calendars[]" 
											value="<?php echo esc_attr($calendar->id); ?>"
											class="cts-calendar-checkbox"
											<?php checked($calendar->is_selected, 1); ?>
										>
									</td>
									<td>
										<strong><?php echo esc_html($calendar->name_translated ?: $calendar->name); ?></strong>
										<?php if ($calendar->name !== $calendar->name_translated && !empty($calendar->name_translated)): ?>
											<br><small class="description"><?php echo esc_html($calendar->name); ?></small>
										<?php endif; ?>
									</td>
									<td>
										<code><?php echo esc_html($calendar->calendar_id); ?></code>
									</td>
									<td>
										<?php if ($calendar->is_public): ?>
											<span class="cts-badge cts-badge-success">
												<?php esc_html_e('Öffentlich', 'churchtools-suite'); ?>
											</span>
										<?php else: ?>
											<span class="cts-badge cts-badge-secondary">
												<?php esc_html_e('Privat', 'churchtools-suite'); ?>
											</span>
										<?php endif; ?>
									</td>
									<td>
										<?php if (!empty($calendar->color)): ?>
											<div class="cts-color-swatch" style="background-color: <?php echo esc_attr($calendar->color); ?>;"></div>
										<?php else: ?>
											—
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
					<div class="cts-mt-15">
						<button type="submit" class="button button-primary">
							<span class="dashicons dashicons-yes"></span>
							<?php esc_html_e('Auswahl speichern', 'churchtools-suite'); ?>
						</button>
					</div>
				</form>
				
				<div id="cts-calendar-selection-result" class="cts-mt-15"></div>
				
			<?php endif; ?>
			
		</div>
	</div>
	

</div>
