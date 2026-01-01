<?php
/**
 * Enhanced Shortcode Manager Page
 * 
 * Verwaltung für ChurchTools Suite Shortcodes mit Preset-System:
 * - Übersicht aller Standard-Shortcodes
 * - Eigene Presets erstellen und speichern
 * - System-Presets für häufige Use Cases
 * 
 * @package ChurchTools_Suite
 * @since   0.5.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Presets Repository
require_once CHURCHTOOLS_SUITE_PATH . 'includes/repositories/class-churchtools-suite-repository-base.php';
require_once CHURCHTOOLS_SUITE_PATH . 'includes/repositories/class-churchtools-suite-shortcode-presets-repository.php';
$presets_repo = new ChurchTools_Suite_Shortcode_Presets_Repository();

// Get saved presets
$all_presets = $presets_repo->get_all_presets();
$saved_presets = array_filter( $all_presets, fn($p) => ! $p['is_system'] ); // Nur User-Presets
$system_presets = array_filter( $all_presets, fn($p) => $p['is_system'] ); // System-Presets

// Shortcode Definitions (nur getestete Views)
$shortcodes = [
	[
		'tag' => 'cts_list',
		'name' => 'List',
		'icon' => '📋',
		'category' => 'list',
		'description' => 'Zeigt Events als Liste an',
		'views' => ['classic', 'medium'], // Nur getestete Views
		'params' => [
			// === Ansicht & Basis ===
			'view' => ['type' => 'select', 'label' => 'View', 'options' => ['classic', 'medium'], 'section' => '📋 Ansicht & Basis'],
			'calendar' => ['type' => 'checkboxes', 'label' => 'Kalender auswählen', 'section' => '📋 Ansicht & Basis'],
			'limit' => ['type' => 'number', 'label' => 'Anzahl Events', 'default' => '20', 'section' => '⚙️ Basis-Einstellungen'],
			// === Anzeige-Optionen ===
			'show_description' => ['type' => 'toggle', 'label' => 'Beschreibung anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			'show_location' => ['type' => 'toggle', 'label' => 'Ort anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			'show_services' => ['type' => 'toggle', 'label' => 'Services anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			'show_calendar_name' => ['type' => 'toggle', 'label' => 'Kalender-Name anzeigen', 'default' => false, 'section' => '👁️ Anzeige-Optionen'],
			'show_time' => ['type' => 'toggle', 'label' => 'Uhrzeit anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			// === Filter & Sortierung ===
			'order' => ['type' => 'select', 'label' => 'Sortierung', 'options' => ['asc' => 'Aufsteigend', 'desc' => 'Absteigend'], 'default' => 'asc', 'section' => '🔍 Filter & Sortierung'],
			'date_from' => ['type' => 'date', 'label' => 'Datum von (YYYY-MM-DD)', 'section' => '🔍 Filter & Sortierung'],
			'date_to' => ['type' => 'date', 'label' => 'Datum bis (YYYY-MM-DD)', 'section' => '🔍 Filter & Sortierung'],
			// === Legacy ===
			'from' => ['type' => 'date', 'label' => 'Von Datum (Legacy)', 'section' => '📦 Legacy-Parameter'],
			'to' => ['type' => 'date', 'label' => 'Bis Datum (Legacy)', 'section' => '📦 Legacy-Parameter'],
			'class' => ['type' => 'text', 'label' => 'CSS Klasse', 'section' => '🎨 Styling'],
		],
		'example' => '[cts_list view="classic"]',
	],
	[
		'tag' => 'cts_calendar',
		'name' => 'Calendar',
		'icon' => '📅',
		'category' => 'calendar',
		'description' => 'Zeigt Events in Kalender-Ansicht an',
		'views' => ['monthly-modern'], // Nur getestete Views
		'params' => [
			'view' => ['type' => 'select', 'label' => 'View', 'options' => ['monthly-modern'], 'section' => '📋 Ansicht & Basis'],
			'calendar' => ['type' => 'checkboxes', 'label' => 'Kalender auswählen', 'section' => '📋 Ansicht & Basis'],
			'from' => ['type' => 'date', 'label' => 'Von Datum', 'section' => '🔍 Filter & Sortierung'],
			'to' => ['type' => 'date', 'label' => 'Bis Datum', 'section' => '🔍 Filter & Sortierung'],
			'class' => ['type' => 'text', 'label' => 'CSS Klasse', 'section' => '🎨 Styling'],
		],
		'example' => '[cts_calendar view="monthly-modern"]',
	],
	[
		'tag' => 'cts_grid',
		'name' => 'Grid',
		'icon' => '🎯',
		'category' => 'grid',
		'description' => 'Zeigt Events als Raster an',
		'views' => ['simple'], // Nur getestete Views
		'params' => [
			// === Ansicht & Basis ===
			'view' => ['type' => 'select', 'label' => 'View', 'options' => ['simple'], 'section' => '📋 Ansicht & Basis'],
			'calendar' => ['type' => 'checkboxes', 'label' => 'Kalender auswählen', 'section' => '📋 Ansicht & Basis'],
			'limit' => ['type' => 'number', 'label' => 'Anzahl Events', 'default' => '12', 'section' => '⚙️ Basis-Einstellungen'],
			// === Layout ===
			'columns' => ['type' => 'number', 'label' => 'Spalten (1-4)', 'default' => '3', 'section' => '⚙️ Basis-Einstellungen'],
			// === Anzeige-Optionen ===
			'show_description' => ['type' => 'toggle', 'label' => 'Beschreibung anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			'show_location' => ['type' => 'toggle', 'label' => 'Ort anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			'show_services' => ['type' => 'toggle', 'label' => 'Services anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			'show_calendar_name' => ['type' => 'toggle', 'label' => 'Kalender-Name anzeigen', 'default' => false, 'section' => '👁️ Anzeige-Optionen'],
			'show_time' => ['type' => 'toggle', 'label' => 'Uhrzeit anzeigen', 'default' => true, 'section' => '👁️ Anzeige-Optionen'],
			// === Filter & Sortierung ===
			'order' => ['type' => 'select', 'label' => 'Sortierung', 'options' => ['asc' => 'Aufsteigend', 'desc' => 'Absteigend'], 'default' => 'asc', 'section' => '🔍 Filter & Sortierung'],
			'date_from' => ['type' => 'date', 'label' => 'Datum von (YYYY-MM-DD)', 'section' => '🔍 Filter & Sortierung'],
			'date_to' => ['type' => 'date', 'label' => 'Datum bis (YYYY-MM-DD)', 'section' => '🔍 Filter & Sortierung'],
			// === Legacy ===
			'from' => ['type' => 'date', 'label' => 'Von Datum (Legacy)', 'section' => '📦 Legacy-Parameter'],
			'to' => ['type' => 'date', 'label' => 'Bis Datum (Legacy)', 'section' => '📦 Legacy-Parameter'],
			'class' => ['type' => 'text', 'label' => 'CSS Klasse', 'section' => '🎨 Styling'],
		],
		'example' => '[cts_grid view="simple" columns="3"]',
	],
];
?>

<div class="wrap cts-wrap">
	
	<div class="cts-header">
		<h1>
			<span>⚡</span>
			<?php esc_html_e( 'Shortcode Manager', 'churchtools-suite' ); ?>
		</h1>
		<p class="cts-subtitle"><?php esc_html_e( 'Verwalte Standard-Shortcodes und erstelle eigene Presets', 'churchtools-suite' ); ?></p>
	</div>
	
	<!-- Tabs -->
	<div class="cts-tabs" style="margin-bottom: 20px;">
		<a href="#" class="cts-tab active" data-tab="standards">
			<span>📚</span>
			<?php esc_html_e( 'Standard-Shortcodes', 'churchtools-suite' ); ?>
		</a>
		<a href="#" class="cts-tab" data-tab="presets">
			<span>⭐</span>
			<?php esc_html_e( 'Meine Presets', 'churchtools-suite' ); ?> (<?php echo count( array_filter( $saved_presets, fn($p) => ! $p['is_system'] ) ); ?>)
		</a>
		<a href="#" class="cts-tab" data-tab="create" id="cts-create-tab">
			<span id="cts-create-icon">➕</span>
			<span id="cts-create-label"><?php esc_html_e( 'Neues Preset erstellen', 'churchtools-suite' ); ?></span>
		</a>
		<a href="#" class="cts-tab" data-tab="demo">
			<span>🎯</span>
			<?php esc_html_e( 'Demo & Live-Views', 'churchtools-suite' ); ?>
		</a>
	</div>
	
	<!-- Tab: Standard Shortcodes -->
	<div id="tab-standards" class="cts-tab-content active">
		<div class="cts-card" style="max-width: 1200px; margin-bottom: 20px;">
			<div class="cts-card-body">
				<div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
					<input 
						type="text" 
						id="cts-shortcode-search" 
						placeholder="<?php esc_attr_e( 'Shortcode suchen...', 'churchtools-suite' ); ?>"
						class="cts-form-input"
						style="max-width: 300px; margin: 0;"
					>
					
					<select id="cts-category-filter" class="cts-form-select" style="max-width: 200px; margin: 0;">
						<option value=""><?php esc_html_e( 'Alle Kategorien', 'churchtools-suite' ); ?></option>
						<option value="list">📋 List</option>
						<option value="calendar">📅 Calendar</option>
						<option value="grid">🎯 Grid</option>
					</select>
					
					<span id="cts-shortcode-count" style="margin-left: auto; color: #6b7280; font-size: 14px; font-weight: 600;">
						<?php echo count( $shortcodes ); ?> Shortcodes
					</span>
				</div>
			</div>
		</div>
		
		<div id="cts-shortcodes-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; max-width: 1200px;">
			
			<?php foreach ( $shortcodes as $shortcode ) : ?>
				<div class="cts-shortcode-card" data-category="<?php echo esc_attr( $shortcode['category'] ); ?>" data-tag="<?php echo esc_attr( $shortcode['tag'] ); ?>" data-name="<?php echo esc_attr( strtolower( $shortcode['name'] ) ); ?>">
					<div class="cts-card">
						<div class="cts-card-header">
							<span class="cts-card-icon"><?php echo $shortcode['icon']; ?></span>
							<h3><?php echo esc_html( $shortcode['name'] ); ?></h3>
						</div>
						<div class="cts-card-body">
							<p style="color: #6b7280; font-size: 13px; margin: 0 0 12px;">
								<?php echo esc_html( $shortcode['description'] ); ?>
							</p>
							
							<div style="background: #f9fafb; padding: 10px; border-radius: 4px; margin-bottom: 12px;">
								<code style="font-size: 12px; color: #d63638;"><?php echo esc_html( $shortcode['example'] ); ?></code>
							</div>
							
							<details style="margin-bottom: 12px;">
								<summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: #2271b1;">
									<?php esc_html_e( 'Verfügbare Views', 'churchtools-suite' ); ?> (<?php echo count( $shortcode['views'] ); ?>)
								</summary>
								<div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px;">
									<?php foreach ( $shortcode['views'] as $view ) : ?>
										<span style="background: #e5e7eb; padding: 4px 8px; border-radius: 4px; font-size: 11px; color: #374151;">
											<?php echo esc_html( $view ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</details>
							
							<details>
								<summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: #2271b1;">
									<?php esc_html_e( 'Parameter', 'churchtools-suite' ); ?> (<?php echo count( $shortcode['params'] ); ?>)
								</summary>
								<div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px;">
									<?php foreach ( $shortcode['params'] as $param => $config ) : ?>
										<span style="background: #dbeafe; padding: 4px 8px; border-radius: 4px; font-size: 11px; color: #1e40af;">
											<?php echo esc_html( $param ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</details>
						</div>
						<div class="cts-card-footer" style="display: flex; gap: 8px;">
							<button class="cts-button cts-button-secondary cts-copy-shortcode" data-shortcode="<?php echo esc_attr( $shortcode['example'] ); ?>">
								📋 <?php esc_html_e( 'Kopieren', 'churchtools-suite' ); ?>
							</button>
							<button class="cts-button cts-button-secondary cts-create-from-standard" data-shortcode="<?php echo esc_attr( wp_json_encode( $shortcode ) ); ?>">
								⭐ <?php esc_html_e( 'Als Preset', 'churchtools-suite' ); ?>
							</button>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			
		</div>
	</div>
	
	<!-- Tab: Meine Presets -->
	<div id="tab-presets" class="cts-tab-content" style="display: none;">
		<div id="cts-presets-container" style="max-width: 1200px;">
			<?php if ( empty( $saved_presets ) ) : ?>
				<div style="text-align: center; padding: 60px 20px; color: #6b7280;">
					<span style="font-size: 64px; display: block; margin-bottom: 16px;">⭐</span>
					<h3 style="margin: 0 0 8px; font-size: 18px; color: #374151;"><?php esc_html_e( 'Noch keine Presets', 'churchtools-suite' ); ?></h3>
					<p style="margin: 0 0 16px; font-size: 14px;"><?php esc_html_e( 'Erstelle dein erstes Preset basierend auf einem Standard-Shortcode', 'churchtools-suite' ); ?></p>
					<button class="cts-button cts-button-primary" data-action="open-create-tab">
						➕ <?php esc_html_e( 'Preset erstellen', 'churchtools-suite' ); ?>
					</button>
				</div>
			<?php else : ?>
				<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
					<?php foreach ( $saved_presets as $preset ) : 
						$is_system = $preset['is_system'];
						$config = $preset['configuration'];
						
						// Build shortcode string - NUR view Parameter anzeigen (Preset-Slug)
						// Der Rest wird intern durch apply_preset_config() geladen
						$view_value = $config['view'] ?? '';
						$shortcode_string = '[' . $preset['shortcode_tag'] . ' view="' . esc_attr( $view_value ) . '"]';
					?>
						<div class="cts-preset-card" data-preset-id="<?php echo $preset['id']; ?>">
							<div class="cts-card">
								<div class="cts-card-header" style="display: flex; justify-content: space-between; align-items: center;">
									<div style="display: flex; align-items: center; gap: 8px;">
										<span class="cts-card-icon"><?php echo $is_system ? '🔒' : '⭐'; ?></span>
										<h3><?php echo esc_html( $preset['name'] ); ?></h3>
									</div>
									<?php if ( $is_system ) : ?>
										<span style="background: #e5e7eb; padding: 4px 8px; border-radius: 4px; font-size: 11px; color: #374151;">
											System
										</span>
									<?php endif; ?>
								</div>
								<div class="cts-card-body">
									<?php if ( ! empty( $preset['description'] ) ) : ?>
										<p style="color: #6b7280; font-size: 13px; margin: 0 0 12px;">
											<?php echo esc_html( $preset['description'] ); ?>
										</p>
									<?php endif; ?>
									
									<div style="background: #f9fafb; padding: 10px; border-radius: 4px; margin-bottom: 12px;">
										<code style="font-size: 12px; color: #d63638; word-break: break-all;">
											<?php echo esc_html( $shortcode_string ); ?>
										</code>
									</div>
									
									<details>
										<summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: #2271b1;">
											<?php esc_html_e( 'Konfiguration', 'churchtools-suite' ); ?>
										</summary>
										<div style="margin-top: 8px;">
											<?php foreach ( $config as $key => $value ) : ?>
												<div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #f0f0f1;">
													<span style="font-size: 12px; color: #6b7280;"><?php echo esc_html( $key ); ?>:</span>
													<span style="font-size: 12px; color: #1f2937; font-weight: 600;"><?php echo esc_html( $value ); ?></span>
												</div>
											<?php endforeach; ?>
										</div>
									</details>
								</div>
								<div class="cts-card-footer" style="display: flex; gap: 8px; justify-content: space-between;">
									<button class="cts-button cts-button-secondary cts-copy-preset" data-shortcode="<?php echo esc_attr( $shortcode_string ); ?>">
										📋 <?php esc_html_e( 'Kopieren', 'churchtools-suite' ); ?>
									</button>
									<?php if ( ! $is_system ) : ?>									<button class="cts-button cts-button-secondary cts-edit-preset" data-preset="<?php echo esc_attr( wp_json_encode( $preset ) ); ?>">
										✏️ <?php esc_html_e( 'Bearbeiten', 'churchtools-suite' ); ?>
									</button>										<button class="cts-button cts-button-secondary cts-delete-preset" data-preset-id="<?php echo $preset['id']; ?>" style="color: #d63638; border-color: #d63638;">
											🗑️ <?php esc_html_e( 'Löschen', 'churchtools-suite' ); ?>
										</button>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	
	<!-- Tab: Neues Preset erstellen -->
	<div id="tab-create" class="cts-tab-content" style="display: none;">
		<div class="cts-card" style="max-width: 800px;">
			<div class="cts-card-header">
				<h3 id="cts-preset-form-title"><?php esc_html_e( 'Neues Preset erstellen', 'churchtools-suite' ); ?></h3>
			</div>
			<div class="cts-card-body">
				<form id="cts-preset-form">
					<input type="hidden" id="preset-id" name="preset-id" value="">
					<table class="cts-form-table">
						<tr>
							<th><?php esc_html_e( 'Preset-Name', 'churchtools-suite' ); ?> *</th>
							<td>
								<input 
									type="text" 
									id="preset-name" 
									name="preset-name" 
									class="cts-form-input" 
									required
									placeholder="z.B. Startseite Events"
								>
								<span class="cts-form-description"><?php esc_html_e( 'Eindeutiger Name für dein Preset (wird als view="name" im Shortcode verwendet)', 'churchtools-suite' ); ?></span>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Beschreibung', 'churchtools-suite' ); ?></th>
							<td>
								<textarea 
									id="preset-description" 
									name="preset-description" 
									class="cts-form-textarea"
									rows="3"
									placeholder="Optional: Wofür wird dieses Preset verwendet?"
								></textarea>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Shortcode-Typ', 'churchtools-suite' ); ?> *</th>
							<td>
								<select id="preset-shortcode-tag" name="preset-shortcode-tag" class="cts-form-select" required>
									<option value=""><?php esc_html_e( 'Bitte wählen...', 'churchtools-suite' ); ?></option>
									<?php foreach ( $shortcodes as $sc ) : ?>
										<option value="<?php echo esc_attr( $sc['tag'] ); ?>" data-params="<?php echo esc_attr( wp_json_encode( $sc['params'] ) ); ?>">
											<?php echo esc_html( $sc['icon'] . ' ' . $sc['name'] ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</table>
					
					<div id="preset-params-container" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f1;">
						<h4 style="margin: 0 0 16px;"><?php esc_html_e( 'Parameter konfigurieren', 'churchtools-suite' ); ?></h4>
						<table class="cts-form-table" id="preset-params-table">
							<!-- Dynamisch gefüllt via JavaScript -->
						</table>
					</div>
					
					<div id="preset-preview" style="display: none; margin-top: 20px; padding: 16px; background: #f9fafb; border-radius: 4px;">
						<h4 style="margin: 0 0 8px; font-size: 14px;"><?php esc_html_e( 'Vorschau', 'churchtools-suite' ); ?></h4>
						<code id="preset-preview-code" style="font-size: 13px; color: #d63638; word-break: break-all;"></code>
					</div>
				</form>
			</div>
			<div class="cts-card-footer">
				<button type="button" id="cts-save-preset" class="cts-button cts-button-primary" disabled>
					<span id="cts-save-icon">💾</span> <span id="cts-save-label"><?php esc_html_e( 'Preset speichern', 'churchtools-suite' ); ?></span>
				</button>
				<button type="button" id="cts-cancel-edit" class="cts-button cts-button-secondary" style="display: none;">
					❌ <?php esc_html_e( 'Abbrechen', 'churchtools-suite' ); ?>
				</button>
				<span id="cts-save-result" style="margin-left: 12px;"></span>
			</div>
		</div>
	</div>

	<!-- Tab: Demo & Live Views -->
	<div id="tab-demo" class="cts-tab-content" style="display: none;">
		<?php
		// Include Demo-Router (shortcode-demo.php content without header)
		require_once __DIR__ . '/demos/demo-helpers.php';
		
		$selected_type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : '';
		?>
		
		<div class="cts-demo-content">
			<?php
			cts_demo_embed_public_css();
			?>
			
			<?php if ( ! $selected_type ) : ?>
				<!-- Demo Overview Grid -->
				<div class="cts-demo-overview">
					<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
						<?php
						$demo_types = [
							'list' => ['icon' => '📋', 'name' => 'List Views', 'count' => 10, 'status' => 'ready'],
							'calendar' => ['icon' => '📅', 'name' => 'Calendar Views', 'count' => 8, 'status' => 'ready'],
							'grid' => ['icon' => '▦', 'name' => 'Grid Views', 'count' => 12, 'status' => 'ready'],
							'slider' => ['icon' => '🎠', 'name' => 'Slider Views', 'count' => 5, 'status' => 'ready'],
							'countdown' => ['icon' => '⏱️', 'name' => 'Countdown', 'count' => 3, 'status' => 'ready'],
							'cover' => ['icon' => '🎨', 'name' => 'Cover Views', 'count' => 5, 'status' => 'ready'],
							'carousel' => ['icon' => '🎡', 'name' => 'Carousel', 'count' => 4, 'status' => 'ready'],
							'timetable' => ['icon' => '🗓️', 'name' => 'Timetable', 'count' => 3, 'status' => 'ready'],
							'widget' => ['icon' => '🎁', 'name' => 'Widgets', 'count' => 3, 'status' => 'ready'],
							'map' => ['icon' => '🗺️', 'name' => 'Map Views', 'count' => 3, 'status' => 'ready'],
							'search' => ['icon' => '🔍', 'name' => 'Search', 'count' => 2, 'status' => 'ready']
						];
						foreach ( $demo_types as $type_key => $type_data ) :
							$is_ready = $type_data['status'] === 'ready';
							?>
							<a href="?page=churchtools-suite-shortcodes&tab=demo&type=<?php echo esc_attr( $type_key ); ?>" class="cts-card" style="text-decoration: none; transition: all 0.2s; border: 2px solid #e5e7eb; <?php echo ! $is_ready ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
								<div class="cts-card-body" style="text-align: center; padding: 24px;">
									<div style="font-size: 48px; margin-bottom: 12px;"><?php echo esc_html( $type_data['icon'] ); ?></div>
									<h3 style="margin: 0 0 8px; font-size: 16px; color: #1d2327;"><?php echo esc_html( $type_data['name'] ); ?></h3>
									<p style="margin: 0; color: #646970; font-size: 13px;"><?php echo esc_html( $type_data['count'] ); ?> Varianten</p>
									<?php if ( $is_ready ) : ?>
										<span style="display: inline-block; margin-top: 12px; padding: 4px 12px; background: #00a32a; color: #fff; border-radius: 12px; font-size: 11px; font-weight: 600;">✓ Verfügbar</span>
									<?php endif; ?>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php else : ?>
				<!-- Demo Type View -->
				<div style="margin-bottom: 20px;">
					<a href="?page=churchtools-suite-shortcodes&tab=demo" class="cts-button cts-button-secondary">
						<span>←</span> <?php esc_html_e( 'Zurück zur Übersicht', 'churchtools-suite' ); ?>
					</a>
				</div>
				<?php
				$demo_file = __DIR__ . '/demos/demo-' . $selected_type . '.php';
				if ( file_exists( $demo_file ) ) {
					include $demo_file;
				} else {
					echo '<div class="cts-notice cts-notice-error"><p>' . esc_html__( 'Demo-Typ nicht gefunden.', 'churchtools-suite' ) . '</p></div>';
				}
				?>
			<?php endif; ?>
		</div>
	</div>

	
</div>

<!-- Tab animations & button styles migrated to assets/css/admin.css (v0.9.4.7) -->
<!-- Shortcode manager JS migrated to assets/js/admin.js (v0.9.4.7) -->
