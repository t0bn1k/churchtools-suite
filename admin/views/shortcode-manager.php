					<details>
						<summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: #2271b1;">
							<?php esc_html_e( 'Konfiguration', 'churchtools-suite' ); ?>
						</summary>
						<div class="cts-mt-8">
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
					<?php if ( ! $is_system ) : ?>						<button class="cts-button cts-button-secondary cts-edit-preset" data-preset="<?php echo esc_attr( wp_json_encode( $preset ) ); ?>">
							✏️ <?php esc_html_e( 'Bearbeiten', 'churchtools-suite' ); ?>
						</button>							<button class="cts-button cts-button-secondary cts-delete-preset" data-preset-id="<?php echo $preset['id']; ?>">
								🗑️ <?php esc_html_e( 'Löschen', 'churchtools-suite' ); ?>
							</button>
					<?php endif; ?>
				</div>
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
	<div class="cts-tabs cts-mb-30">
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
		<div class="cts-card cts-maxw-1200 cts-mb-20">
			<div class="cts-card-body">
				<div class="cts-flex cts-gap-12 cts-flex-wrap cts-align-center">
					<input 
						type="text" 
						id="cts-shortcode-search" 
						placeholder="<?php esc_attr_e( 'Shortcode suchen...', 'churchtools-suite' ); ?>"
						class="cts-form-input"
                        
					>
					
					<select id="cts-category-filter" class="cts-form-select">
						<option value=""><?php esc_html_e( 'Alle Kategorien', 'churchtools-suite' ); ?></option>
						<option value="list">📋 List</option>
						<option value="calendar">📅 Calendar</option>
						<option value="grid">🎯 Grid</option>
					</select>
					
					<span id="cts-shortcode-count" class="cts-ml-auto cts-muted-small cts-font-600">
						<?php echo count( $shortcodes ); ?> Shortcodes
					</span>
				</div>
			</div>
		</div>
		
		<div id="cts-shortcodes-grid" class="cts-grid-auto cts-card-maxwidth">
			
			<?php foreach ( $shortcodes as $shortcode ) : ?>
				<div class="cts-shortcode-card" data-category="<?php echo esc_attr( $shortcode['category'] ); ?>" data-tag="<?php echo esc_attr( $shortcode['tag'] ); ?>" data-name="<?php echo esc_attr( strtolower( $shortcode['name'] ) ); ?>">
					<div class="cts-card">
						<div class="cts-card-header">
							<span class="cts-card-icon"><?php echo $shortcode['icon']; ?></span>
							<h3><?php echo esc_html( $shortcode['name'] ); ?></h3>
						</div>
						<div class="cts-card-body">
							<p class="cts-muted-small cts-mb-12">
								<?php echo esc_html( $shortcode['description'] ); ?>
							</p>
                            
							<div class="cts-card-muted cts-mb-12 cts-p-10">
								<code class="cts-code"><?php echo esc_html( $shortcode['example'] ); ?></code>
							</div>
							
							<details class="cts-mb-12">
								<summary class="cts-muted-small cts-summary-link">
									<?php esc_html_e( 'Verfügbare Views', 'churchtools-suite' ); ?> (<?php echo count( $shortcode['views'] ); ?>)
								</summary>
								<div class="cts-flex cts-flex-wrap cts-gap-12 cts-mt-8">
									<?php foreach ( $shortcode['views'] as $view ) : ?>
										<span class="cts-badge">
											<?php echo esc_html( $view ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</details>
							
							<details>
								<summary class="cts-muted-small cts-summary-link">
									<?php esc_html_e( 'Parameter', 'churchtools-suite' ); ?> (<?php echo count( $shortcode['params'] ); ?>)
								</summary>
								<div class="cts-flex cts-flex-wrap cts-gap-12 cts-mt-8">
									<?php foreach ( $shortcode['params'] as $param => $config ) : ?>
										<span class="cts-badge">
											<?php echo esc_html( $param ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</details>
						</div>
						<div class="cts-card-footer cts-flex cts-gap-12">
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
	<div id="tab-presets" class="cts-tab-content cts-hidden">
		<div id="cts-presets-container" class="cts-card-maxwidth">
			<?php if ( empty( $saved_presets ) ) : ?>
				<div class="cts-text-center cts-card-muted cts-p-12">
					<span class="cts-empty-icon">⭐</span>
					<h3 class="cts-mt-0 cts-mb-8" style="font-size:18px; color:#374151;"><?php esc_html_e( 'Noch keine Presets', 'churchtools-suite' ); ?></h3>
					<p class="cts-muted-small cts-mb-12"><?php esc_html_e( 'Erstelle dein erstes Preset basierend auf einem Standard-Shortcode', 'churchtools-suite' ); ?></p>
					<button class="cts-button cts-button-primary" data-action="open-create-tab">
						➕ <?php esc_html_e( 'Preset erstellen', 'churchtools-suite' ); ?>
					</button>
				</div>
			<?php else : ?>
				<div class="cts-grid-auto">
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
								<div class="cts-card-header cts-flex" style="justify-content:space-between;align-items:center;">
									<div class="cts-flex" style="gap:8px;align-items:center;">
										<span class="cts-card-icon"><?php echo $is_system ? '🔒' : '⭐'; ?></span>
										<h3><?php echo esc_html( $preset['name'] ); ?></h3>
									</div>
									<?php if ( $is_system ) : ?>
										<span class="cts-badge">
											System
										</span>
									<?php endif; ?>
								</div>
								<div class="cts-card-body">
									<?php if ( ! empty( $preset['description'] ) ) : ?>
										<p class="cts-muted-small cts-mb-12">
											<?php echo esc_html( $preset['description'] ); ?>
										</p>
									<?php endif; ?>
									
									<div class="cts-card-muted cts-mb-12 cts-p-10">
										<code class="cts-code">
											<?php echo esc_html( $shortcode_string ); ?>
										</code>
									</div>
									
									<details>
										<summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: #2271b1;">
											<?php esc_html_e( 'Konfiguration', 'churchtools-suite' ); ?>
										</summary>
										<div class="cts-mt-8">
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
	<div id="tab-create" class="cts-tab-content cts-hidden">
		<div class="cts-card cts-maxw-900">
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
					
					<div id="preset-params-container" class="cts-hidden cts-mt-20" style="padding-top: 20px; border-top: 1px solid #f0f0f1;">
						<h4 class="cts-mb-12"><?php esc_html_e( 'Parameter konfigurieren', 'churchtools-suite' ); ?></h4>
						<table class="cts-form-table" id="preset-params-table">
							<!-- Dynamisch gefüllt via JavaScript -->
						</table>
					</div>
					
					<div id="preset-preview" class="cts-hidden cts-mt-20" style="padding: 16px; background: #f9fafb; border-radius: 4px;">
						<h4 class="cts-mb-8" style="font-size: 14px;"><?php esc_html_e( 'Vorschau', 'churchtools-suite' ); ?></h4>
						<code id="preset-preview-code" class="cts-code"></code>
					</div>
				</form>
			</div>
			<div class="cts-card-footer">
				<button type="button" id="cts-save-preset" class="cts-button cts-button-primary" disabled>
					<span id="cts-save-icon">💾</span> <span id="cts-save-label"><?php esc_html_e( 'Preset speichern', 'churchtools-suite' ); ?></span>
				</button>
				<button type="button" id="cts-cancel-edit" class="cts-button cts-button-secondary cts-hidden">
					❌ <?php esc_html_e( 'Abbrechen', 'churchtools-suite' ); ?>
				</button>
				<span id="cts-save-result" class="cts-ml-8"></span>
			</div>
		</div>
	</div>

	<!-- Tab: Demo & Live Views -->
	<div id="tab-demo" class="cts-tab-content cts-hidden">
		<?php
		// Reuse demo data from shortcode-demo-tabs.php to show full demo in manager
		$demo_types = [
			'list' => ['icon' => '📋','name' => 'List Views','count' => 4,'status' => 'ready','description' => '✓ Classic, Medium, Classic-Services | ⏳ Fluent'],
			'calendar' => ['icon' => '📅','name' => 'Calendar Views','count' => 5,'status' => 'ready','description' => '✓ Monthly Modern | ⏳ Monthly Clean, Weekly, Yearly, Daily'],
			'grid' => ['icon' => '▦','name' => 'Grid Views','count' => 3,'status' => 'ready','description' => '✓ Simple | ⏳ Modern, Colorful'],
			'slider' => ['icon' => '🎠','name' => 'Slider Views','count' => 5,'status' => 'planned','description' => 'Autoplay, verschiedene Stile'],
			'countdown' => ['icon' => '⏱️','name' => 'Countdown Views','count' => 3,'status' => 'planned','description' => 'Countdown bis zum nächsten Event'],
			'cover' => ['icon' => '🎨','name' => 'Cover Views','count' => 5,'status' => 'planned','description' => 'Hero-Banner, große Teaserbilder'],
			'timetable' => ['icon' => '🗓️','name' => 'Timetable Views','count' => 3,'status' => 'planned','description' => 'Zeitplan, Timeline-Ansichten'],
			'carousel' => ['icon' => '🎡','name' => 'Carousel Views','count' => 4,'status' => 'planned','description' => 'Karussell mit Navigation'],
			'widget' => ['icon' => '🎁','name' => 'Widget Views','count' => 3,'status' => 'planned','description' => 'Sidebar-Widgets, kleine Ansichten'],
			'search' => ['icon' => '🔍','name' => 'Search Views','count' => 2,'status' => 'planned','description' => 'Suchleiste, erweiterte Suche'],
			'map' => ['icon' => '🗺️','name' => 'Map Views','count' => 3,'status' => 'planned','description' => 'Kartenansichten mit Orten']
		];
		?>
		
		<!-- Quick Stats -->
		<div class="cts-grid cts-grid-3 cts-mb-20">
			<div class="cts-card"><div class="cts-card-body cts-text-center"><div class="cts-stat-number">3/11</div><p class="cts-card-detail"><?php esc_html_e( 'Shortcode-Typen verfügbar', 'churchtools-suite' ); ?></p></div></div>
			<div class="cts-card"><div class="cts-card-body cts-text-center"><div class="cts-stat-number">5<span style="color: #3b82f6;">/12</span></div><p class="cts-card-detail"><?php esc_html_e( 'View-Varianten fertig/geplant', 'churchtools-suite' ); ?></p></div></div>
			<div class="cts-card"><div class="cts-card-body cts-text-center"><div class="cts-stat-number">v0.9.2</div><p class="cts-card-detail"><?php esc_html_e( 'Manager Demo Version', 'churchtools-suite' ); ?></p></div></div>
		</div>

		<!-- Status Legend -->
		<div class="cts-card" style="margin-bottom:20px;"><div class="cts-card-body"><div style="display:flex; gap:24px; flex-wrap:wrap; justify-content:center;"><div style="display:flex; align-items:center; gap:8px;"><span class="cts-status-badge cts-status-ready">✓ Verfügbar</span><span style="color:#646970; font-size:13px;">5 Views (List, Calendar, Grid)</span></div><div style="display:flex; align-items:center; gap:8px;"><span class="cts-status-badge cts-status-planned">⏳ In Entwicklung</span><span style="color:#646970; font-size:13px;">7 Views + 8 neue Typen</span></div></div></div></div>

		<!-- Type Selection Grid -->
		<div class="cts-demo-type-grid">
			<?php foreach ( $demo_types as $type_key => $type_data ) :
				$is_ready = ( $type_data['status'] === 'ready' );
				$card_class = $is_ready ? 'cts-demo-type-card' : 'cts-demo-type-card cts-demo-type-card-disabled';
			?>
				<a href="<?php echo $is_ready ? '?page=churchtools-suite-shortcodes&tab=demo&type=' . esc_attr( $type_key ) : '#'; ?>" class="<?php echo esc_attr( $card_class ); ?>" <?php echo ! $is_ready ? 'data-disabled="1" aria-disabled="true"' : ''; ?>>
					<div class="cts-demo-type-icon"><?php echo esc_html( $type_data['icon'] ); ?></div>
					<?php if ( ! $is_ready ) : ?><span class="cts-status-badge cts-status-planned">⏳ Geplant</span><?php else : ?><span class="cts-status-badge cts-status-ready">✓ Verfügbar</span><?php endif; ?>
					<h3><?php echo esc_html( $type_data['name'] ); ?></h3>
					<p class="cts-demo-type-count"><?php echo esc_html( $type_data['count'] ); ?> Varianten</p>
					<p class="cts-demo-type-desc"><?php echo esc_html( $type_data['description'] ); ?></p>
					<?php if ( $is_ready ) : ?><span class="cts-demo-type-arrow">→</span><?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>

		<!-- Demo HTML Export -->
		<div class="cts-card cts-maxw-900 cts-mt-20">
			<div class="cts-card-header"><span class="cts-card-icon">💾</span><h3><?php esc_html_e( 'Demo-HTML für WordPress', 'churchtools-suite' ); ?></h3></div>
			<div class="cts-card-body">
				<p><?php esc_html_e( 'Kopiere den gesamten HTML-Code und füge ihn in eine neue WordPress-Seite ein (Code-Editor-Modus).', 'churchtools-suite' ); ?></p>
				<textarea id="cts-demo-html" readonly class="cts-preview-textarea"><?php echo esc_textarea( file_exists( CHURCHTOOLS_SUITE_PATH . 'shortcode-demo.html' ) ? file_get_contents( CHURCHTOOLS_SUITE_PATH . 'shortcode-demo.html' ) : '' ); ?></textarea>
				<button type="button" id="cts-copy-demo-html" class="cts-button cts-button-primary cts-mt-8">📋 <?php esc_html_e( 'HTML kopieren', 'churchtools-suite' ); ?></button>
				<span id="cts-copy-feedback" class="cts-hidden cts-ml-8 cts-success">✓ <?php esc_html_e( 'In Zwischenablage kopiert!', 'churchtools-suite' ); ?></span>
			</div>
		</div>

		<!-- Quick Reference, Docs Links & Checklist -->
		<div class="cts-card" style="max-width:900px; margin-top:20px;">
			<div class="cts-card-header"><span class="cts-card-icon">🔖</span><h3><?php esc_html_e( 'Quick Reference', 'churchtools-suite' ); ?></h3></div>
			<div class="cts-card-body">
				<div style="background:#f9fafb; padding:16px; border-radius:6px; border:1px solid #e5e7eb;">
					<h4 style="margin-top:0;"><?php esc_html_e( 'Häufigste Shortcodes:', 'churchtools-suite' ); ?></h4>
					<ul style="margin:0; padding-left:20px;">
						<li class="cts-mb-8"><code class="cts-code">[cts_list view="classic" limit="10" show_services="true"]</code></li>
						<li class="cts-mb-8"><code class="cts-code">[cts_calendar view="monthly-modern" limit="20"]</code></li>
						<li class="cts-mb-8"><code class="cts-code">[cts_grid view="simple" columns="3" limit="9"]</code></li>
						<li class="cts-mb-8"><code class="cts-code">[cts_countdown view="type-1"]</code></li>
					</ul>
					<h4 style="margin:20px 0 8px;"><?php esc_html_e( 'Gemeinsame Parameter:', 'churchtools-suite' ); ?></h4>
					<ul style="margin:0; padding-left:20px;"><li><code>calendar="1,2,3"</code> - <?php esc_html_e( 'Kalender-IDs filtern', 'churchtools-suite' ); ?></li><li><code>limit="10"</code> - <?php esc_html_e( 'Max. Anzahl Events', 'churchtools-suite' ); ?></li><li><code>from="today"</code> - <?php esc_html_e( 'Start-Datum', 'churchtools-suite' ); ?></li><li><code>to="+30 days"</code> - <?php esc_html_e( 'End-Datum', 'churchtools-suite' ); ?></li></ul>
				</div>
			</div>
		</div>

		<div class="cts-card" style="max-width:900px; margin-top:20px;">
			<div class="cts-card-header"><span class="cts-card-icon">📖</span><h3><?php esc_html_e( 'Dokumentation', 'churchtools-suite' ); ?></h3></div>
			<div class="cts-card-body"><div style="display:grid; gap:12px;"><a href="<?php echo esc_url( CHURCHTOOLS_SUITE_URL . 'SHORTCODE-DEMO.md' ); ?>" class="cts-button" target="_blank"><?php esc_html_e( 'SHORTCODE-DEMO.md', 'churchtools-suite' ); ?></a><a href="<?php echo esc_url( CHURCHTOOLS_SUITE_URL . 'SHORTCODE-REFERENCE.md' ); ?>" class="cts-button" target="_blank"><?php esc_html_e( 'SHORTCODE-REFERENCE.md', 'churchtools-suite' ); ?></a><a href="<?php echo esc_url( CHURCHTOOLS_SUITE_URL . 'TEST-DOCUMENTATION.md' ); ?>" class="cts-button" target="_blank"><?php esc_html_e( 'TEST-DOCUMENTATION.md', 'churchtools-suite' ); ?></a><a href="<?php echo esc_url( CHURCHTOOLS_SUITE_URL . 'SHORTCODE-GUIDE.md' ); ?>" class="cts-button" target="_blank"><?php esc_html_e( 'SHORTCODE-GUIDE.md', 'churchtools-suite' ); ?></a></div></div>
		</div>

		<div class="cts-card" style="max-width:900px; margin-top:20px;">
			<div class="cts-card-header"><span class="cts-card-icon">✅</span><h3><?php esc_html_e( 'Test-Checkliste', 'churchtools-suite' ); ?></h3></div>
			<div class="cts-card-body"><div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:20px;"><div><h4 style="margin-top:0; color:#667eea;"><?php esc_html_e( 'Funktionalität', 'churchtools-suite' ); ?></h4><ul style="list-style:none; padding:0;"><li>☐ <?php esc_html_e( 'Alle Shortcodes rendern', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Events werden angezeigt', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Kalenderfilter wirken', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Services erscheinen', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Datumsformatierung', 'churchtools-suite' ); ?></li></ul></div><div><h4 style="margin-top:0; color:#667eea;"><?php esc_html_e( 'Layout', 'churchtools-suite' ); ?></h4><ul style="list-style:none; padding:0;"><li>☐ <?php esc_html_e( 'Responsive Design', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Keine Layout-Breaks', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Farben konsistent', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Schriften lesbar', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Abstände harmonisch', 'churchtools-suite' ); ?></li></ul></div><div><h4 style="margin-top:0; color:#667eea;"><?php esc_html_e( 'Performance', 'churchtools-suite' ); ?></h4><ul style="list-style:none; padding:0;"><li>☐ <?php esc_html_e( 'Ladezeiten < 2s', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Keine JS-Fehler', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Keine PHP-Errors', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'CSS wird geladen', 'churchtools-suite' ); ?></li><li>☐ <?php esc_html_e( 'Kein Blocking', 'churchtools-suite' ); ?></li></ul></div></div></div>
		</div>

	</div>
	
</div>
