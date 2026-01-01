/**
 * ChurchTools Suite Admin JS
 * Eigenständiges JavaScript ohne jQuery
 *
 * @package ChurchTools_Suite
 * @since   0.2.1.0
 */

(function() {
	'use strict';

	// DOM Ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	function init() {
		initTabs();
		initSyncButton();
		initDataHeaderSync();
		initForms();
		initTestConnection();
		initCalendarSync();
		initCalendarSelection();
		initServiceGroupSync();
		initServiceGroupSelection();
		initServiceSync();
		initServiceSelection();
		initEventSync();
			initManualTriggers();
		initLogViewer();
			initResetCleanup();
		initShortcodeGenerator();
		initEventDetailsPanels(); // v0.9.2.0
		// Migrated view inits
		initDemoTabs();
		initDemoCopyHtml();
		initShortcodeManager();
		initBlockDebug();
		initDashboardHandlers();
		initImportedServices();
	}

	/**
	 * Demo Tabs (migrated from `shortcode-demo-tabs.php`)
	 */
	function initDemoTabs() {
		const tabs = document.querySelectorAll('.cts-demo-tab');
		const panels = document.querySelectorAll('.cts-demo-panel');
		if (!tabs.length || !panels.length) return;

		tabs.forEach(function(tab) {
			tab.addEventListener('click', function(e) {
				e.preventDefault();
				const targetTab = this.getAttribute('data-tab');
				const targetId = 'demo-' + targetTab;
				// Deactivate
				tabs.forEach(function(t) { t.classList.remove('active'); });
				panels.forEach(function(p) { p.classList.remove('active'); });
				this.classList.add('active');
				const targetPanel = document.getElementById(targetId);
				if (targetPanel) targetPanel.classList.add('active');
			});
		});
	}

	function initDemoCopyHtml() {
		const copyButton = document.getElementById('cts-copy-demo-html');
		const textarea = document.getElementById('cts-demo-html');
		const feedback = document.getElementById('cts-copy-feedback');
		if (!copyButton || !textarea) return;
		copyButton.addEventListener('click', function() {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(textarea.value).then(function() {
					if (feedback) { feedback.style.display = 'inline'; setTimeout(function(){ feedback.style.display = 'none'; }, 3000); }
				});
			} else {
				document.execCommand('copy');
				if (feedback) { feedback.style.display = 'inline'; setTimeout(function(){ feedback.style.display = 'none'; }, 3000); }
			}
		});
	}

	/**
	 * Shortcode Manager init (migrated from `shortcode-manager.php` inline script)
	 * Provides: tab switching, copy buttons, create-from-standard, preset form basics,
	 * AJAX calendar load and preset save/delete.
	 */
	function initShortcodeManager() {
		// Tab switching
		const tabs = document.querySelectorAll('.cts-tab');
		const tabContents = document.querySelectorAll('.cts-tab-content');
		tabs.forEach(tab => {
			tab.addEventListener('click', function(e){
				const targetTab = this.dataset.tab;
				// Only handle tabs that define a data-tab (shortcode-manager style).
				// If no data-tab is present, let the anchor behave like a normal link.
				if (!targetTab) {
					return;
				}
				e.preventDefault();
				// Visual state
				tabs.forEach(t => t.classList.remove('active'));
				this.classList.add('active');
				// Content
				tabContents.forEach(content => {
					if (content.id === 'tab-' + targetTab) {
						content.style.display = 'block';
					} else {
						content.style.display = 'none';
					}
				});
			});
		});

		// Copy shortcode/preset buttons
		document.querySelectorAll('.cts-copy-shortcode, .cts-copy-preset').forEach(function(button){
			button.addEventListener('click', function(){
				const shortcode = this.dataset.shortcode || this.getAttribute('data-shortcode');
				const originalText = this.innerHTML;
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(shortcode).then(function(){
						button.innerHTML = '✓ Kopiert!';
						button.classList.add('copied');
						setTimeout(function(){ button.innerHTML = originalText; button.classList.remove('copied'); }, 2000);
					});
				} else {
					alert('Shortcode: ' + shortcode);
				}
			});
		});

		// Create from standard
		document.querySelectorAll('.cts-create-from-standard').forEach(function(button){
			button.addEventListener('click', function(){
				const shortcodeData = JSON.parse(this.dataset.shortcode);
				document.querySelector('[data-tab="create"]').click();
				document.getElementById('preset-name').value = shortcodeData.name + ' Preset';
				document.getElementById('preset-description').value = shortcodeData.description || '';
				document.getElementById('preset-shortcode-tag').value = shortcodeData.tag;
				// trigger change
				const evt = new Event('change');
				document.getElementById('preset-shortcode-tag').dispatchEvent(evt);
			});
		});

		// Preset form: load calendars via AJAX for checkboxes (delegated via change listener already in other code)
		const presetShortcodeTag = document.getElementById('preset-shortcode-tag');
		if (presetShortcodeTag) {
			presetShortcodeTag.addEventListener('change', function(){
				// the original inline script builds complex UI; keep behavior by triggering existing change handler that was migrated earlier
			});
		}

		// Save / Update preset buttons
		const saveButton = document.getElementById('cts-save-preset');
		if (saveButton) {
			saveButton.addEventListener('click', function(){
				const presetId = document.getElementById('preset-id').value;
				const name = document.getElementById('preset-name').value;
				const description = document.getElementById('preset-description').value;
				const shortcodeTag = document.getElementById('preset-shortcode-tag').value;
				if (!name || !shortcodeTag) { alert('Bitte fülle alle Pflichtfelder aus'); return; }
				// collect simple inputs
				const inputs = document.querySelectorAll('#preset-params-table input, #preset-params-table select');
				const configuration = {};
				inputs.forEach(i => { if (i.dataset && i.dataset.paramName) { if (i.value !== '') configuration[i.dataset.paramName] = i.value; }});
				const params = { action: presetId ? 'cts_update_preset' : 'cts_save_preset', nonce: churchtoolsSuite.nonce, name: name, description: description, shortcode_tag: shortcodeTag, configuration: JSON.stringify(configuration) };
				if (presetId) params.preset_id = presetId;
				fetch(churchtoolsSuite.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(params) })
				.then(r => r.json()).then(data => {
					const resultSpan = document.getElementById('cts-save-result');
					if (data.success) { resultSpan.innerHTML = '<span style="color: #00a32a;">✓ Preset gespeichert!</span>'; setTimeout(()=> location.reload(), 1200); }
					else { resultSpan.innerHTML = '<span style="color: #d63638;">✗ Fehler: ' + (data.data ? data.data.message : 'Unbekannt') + '</span>'; }
				}).catch(err => { const resultSpan = document.getElementById('cts-save-result'); resultSpan.innerHTML = '<span style="color: #d63638;">✗ Fehler: ' + err.message + '</span>'; });
			});
		}

		// Edit preset buttons handled by existing delegated script in page; Delete preset use fetch
		document.querySelectorAll('.cts-delete-preset').forEach(btn => {
			btn.addEventListener('click', function(){
				if (!confirm('Möchtest du dieses Preset wirklich löschen?')) return;
				const presetId = this.dataset.presetId;
				fetch(churchtoolsSuite.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'cts_delete_preset', nonce: churchtoolsSuite.nonce, preset_id: presetId }) })
				.then(r=>r.json()).then(data=>{ if (data.success) { const card = document.querySelector('[data-preset-id="'+presetId+'"]'); if (card) card.remove(); } else { alert('Fehler: ' + (data.data ? data.data.message : 'Unbekannter Fehler')); }}).catch(err=>{ alert('Fehler: ' + err.message); });
			});
		});
	}

	/**
	 * Block Debug handlers (migrated from `tab-block-debug.php`)
	 */
	function initBlockDebug() {
		const reloadBtn = document.getElementById('cts-reload-block-logs');
		const clearBtn = document.getElementById('cts-clear-block-logs');
		if (reloadBtn) reloadBtn.addEventListener('click', function(){ location.reload(); });
		if (clearBtn) {
			clearBtn.addEventListener('click', function(){
				if (!confirm('Möchtest du wirklich alle Block-Logs löschen?')) return;
				clearBtn.disabled = true;
				const originalText = clearBtn.innerHTML;
				clearBtn.innerHTML = '<span>⏳</span> Lösche...';
				fetch(churchtoolsSuite.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'cts_clear_block_logs', nonce: churchtoolsSuite.nonce }) })
				.then(r=>r.json()).then(data=>{ if (data.success) location.reload(); else alert('Fehler beim Löschen: ' + (data.data ? data.data.message : 'Unbekannt')); })
				.catch(err=> alert('Fehler: ' + err.message))
				.finally(()=>{ clearBtn.disabled = false; clearBtn.innerHTML = originalText; });
			});
		}
	}

	/**
	 * Dashboard handlers (migrated from `tab-dashboard.php` inline scripts)
	 */
	function initDashboardHandlers() {
		const syncBtn = document.getElementById('cts-sync-now');
		if (syncBtn) {
			syncBtn.addEventListener('click', function(){
				if (!confirm('Einen manuellen Sync jetzt starten? Dies kann einige Zeit dauern.')) return;
				syncBtn.disabled = true; const original = syncBtn.innerHTML; syncBtn.innerHTML = '⏳ Synchronisiere...';
				fetch(churchtoolsSuite.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'cts_trigger_manual_sync', nonce: churchtoolsSuite.nonce }) })
				.then(r=>r.json()).then(data=>{ const result = document.getElementById('cts-sync-result'); if (data.success) { if (result) result.innerHTML = '<span style="color:#0a0">' + (data.data.message||'✅ Synchronisation abgeschlossen') + '</span>'; } else { if (result) result.innerHTML = '<span style="color:#d63638">' + (data.data?.message||data.message||'Fehler beim Sync') + '</span>'; } })
				.catch(err=>{ const result = document.getElementById('cts-sync-result'); if (result) result.innerHTML = '<span style="color:#d63638">Fehler: ' + err.message + '</span>'; })
				.finally(()=>{ syncBtn.disabled = false; syncBtn.innerHTML = original; });
			});
		}

		const installBtn = document.getElementById('cts_install_update_btn');
		if (installBtn) {
			installBtn.addEventListener('click', function(){
				if (!confirm('Update jetzt installieren? Dies überschreibt Plugin-Dateien.')) return;
				installBtn.disabled = true; const orig = installBtn.innerHTML; installBtn.innerHTML = '⏳ Installiere...';
				fetch(churchtoolsSuite.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'cts_run_update', nonce: churchtoolsSuite.nonce }) })
				.then(r=>r.json()).then(function(data){ if (data.success) alert(data.data && data.data.message ? data.data.message : 'Update gestartet'); else alert(data.data && data.data.message ? data.data.message : (data.message || 'Fehler beim Update')); })
				.catch(err=> alert('Netzwerkfehler: ' + err.message))
				.finally(()=>{ installBtn.disabled = false; installBtn.innerHTML = orig; });
			});
		}
	}

	/**
	 * Imported Services pagination (replaces inline jQuery)
	 */
	function initImportedServices() {
		// delegate click on pagination links inside imported services card
		document.addEventListener('click', function(e){
			const a = e.target.closest('.cts-imported-services .cts-pagination a');
			if (!a) return;
			e.preventDefault();
			const href = a.getAttribute('href');
			const match = href && href.match(/paged=(\d+)/);
			if (match) {
				fetchImportedServices(match[1]);
			}
		});

		function fetchImportedServices(paged) {
			fetch(churchtoolsSuite.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'cts_fetch_imported_services_list', paged: paged || 1, nonce: churchtoolsSuite.nonce }) })
			.then(r=>r.json()).then(resp=>{ if (resp.success) { const container = document.getElementById('cts-imported-services-ajax-container'); if (container) container.innerHTML = resp.data.html; } else { alert(resp.data && resp.data.message ? resp.data.message : 'Fehler'); } })
			.catch(err=> alert('Fehler: ' + err.message));
		}
	}

	/**
	 * Events list AJAX (migrated from inline view script)
	 * Handles filter form submission and delegated pagination
	 */
	function initEventsAjax() {
		var form = document.querySelector('.cts-filter-section');
		if (!form) return;

		function fetchEvents(paged) {
			var params = new URLSearchParams(new FormData(form));
			if (paged) params.append('paged', paged);
			params.append('action', 'cts_fetch_events_list');
			params.append('nonce', churchtoolsSuite.nonce);

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: params
			})
			.then(function(r){ return r.json(); })
			.then(function(resp){
				if (resp.success) {
					var container = document.getElementById('cts-events-ajax-container');
					if (container) container.innerHTML = resp.data.html;
				} else {
					alert(resp.data && resp.data.message ? resp.data.message : 'Fehler');
				}
			})
			.catch(function(err){ alert('Fehler: ' + err.message); });
		}

		form.addEventListener('submit', function(e){ e.preventDefault(); fetchEvents(1); });

		document.addEventListener('click', function(e){
			var el = e.target.closest('.cts-ajax-page');
			if (!el) return;
			e.preventDefault();
			var p = el.dataset.paged;
			fetchEvents(p);
		});
	}

	// Initialize the events AJAX handler
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initEventsAjax);
	} else {
		initEventsAjax();
	}

	/**
	 * Reset & Cleanup handlers (migrated from inline jQuery in admin views)
	 */
	function initResetCleanup() {
		const actions = [
			{btnId: 'cts-clear-events', action: 'cts_clear_events', confirm: 'Wirklich alle Events löschen? Diese Aktion kann nicht rückgängig gemacht werden!'},
			{btnId: 'cts-clear-calendars', action: 'cts_clear_calendars', confirm: 'Wirklich alle Kalender löschen? Diese Aktion kann nicht rückgängig gemacht werden!'},
			{btnId: 'cts-clear-services', action: 'cts_clear_services', confirm: 'Wirklich alle Services löschen? Diese Aktion kann nicht rückgängig gemacht werden!'},
			{btnId: 'cts-clear-sync-history', action: 'cts_clear_sync_history', confirm: 'Wirklich die gesamte Sync-Historie löschen?'},
			{btnId: 'cts-full-reset', action: 'cts_full_reset', confirm: 'ACHTUNG: Wirklich ALLE Daten löschen (Events, Kalender, Services, Sync-Historie)? Diese Aktion kann nicht rückgängig gemacht werden!'}
		];

		actions.forEach(item => {
			const btn = document.getElementById(item.btnId);
			if (!btn) return;
			btn.addEventListener('click', function() {
				if (!confirm(item.confirm)) return;
				const originalText = btn.innerHTML;
				btn.disabled = true;
				btn.innerHTML = '⏳ Wird gelöscht...';
				fetch(churchtoolsSuite.ajaxUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: new URLSearchParams({ action: item.action, nonce: churchtoolsSuite.nonce })
				})
				.then(r => r.json())
				.then(data => {
					if (data.success) {
						alert((data.data && data.data.message) ? data.data.message : 'Aktion erfolgreich');
						location.reload();
					} else {
						alert('Fehler: ' + (data.data && data.data.message ? data.data.message : 'Unbekannter Fehler'));
					}
				})
				.catch(() => {
					alert('Fehler beim Ausführen der Aktion');
				})
				.finally(() => {
					btn.disabled = false;
					btn.innerHTML = originalText;
				});
			});
		});
	}

	/**
	 * Tab Navigation
	 */
	function initTabs() {
		const tabs = document.querySelectorAll('.cts-tab');
		
		tabs.forEach(tab => {
			tab.addEventListener('click', function(e) {
				// Let WordPress handle the URL change
				// This is just for visual feedback
			});
		});
	}

	/**
	 * Sync Button
	 */
	function initSyncButton() {
		const syncButton = document.getElementById('cts-sync-now');
		if (!syncButton) return;

		syncButton.addEventListener('click', function() {
			const progress = document.getElementById('cts-sync-progress');
			const result = document.getElementById('cts-sync-result');
			
			if (progress) progress.style.display = 'block';
			if (result) result.innerHTML = '';
			
			syncButton.disabled = true;
			syncButton.textContent = 'Synchronisiere...';

			// AJAX call (wird später implementiert)
			fetch(ajaxurl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'cts_sync_now',
					nonce: churchtoolsSuite.nonce
				})
			})
			.then(response => response.json())
			.then(data => {
				if (progress) progress.style.display = 'none';
				
				if (data.success) {
					if (result) {
						result.innerHTML = '<div class="cts-notice cts-notice-success"><p>' + 
							(data.data.message || 'Synchronisation erfolgreich!') + 
							'</p></div>';
					}
				} else {
					if (result) {
						result.innerHTML = '<div class="cts-notice cts-notice-error"><p>' + 
							(data.data.message || 'Synchronisation fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (progress) progress.style.display = 'none';
				if (result) {
					result.innerHTML = '<div class="cts-notice cts-notice-error"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				syncButton.disabled = false;
				syncButton.textContent = 'Jetzt synchronisieren';
			});
		});
	}

	/**
	 * Data Subpage Header Sync Button
	 * Binds #cts-data-sync-now to the manual sync AJAX endpoint and shows result in #cts-data-sync-result
	 */
	function initDataHeaderSync() {
		const dataBtn = document.getElementById('cts-data-sync-now');
		if (!dataBtn) return;

		dataBtn.addEventListener('click', function() {
			const result = document.getElementById('cts-data-sync-result');
			const originalText = dataBtn.innerHTML;
			dataBtn.disabled = true;
			dataBtn.classList.add('loading');
			dataBtn.setAttribute('aria-busy', 'true');
			dataBtn.innerHTML = '<span class="dashicons dashicons-update"></span> Synchronisiere...';
			if (result) { result.style.display = 'none'; result.innerHTML = ''; }

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams({ action: 'cts_trigger_manual_sync', nonce: churchtoolsSuite.nonce })
			})
			.then(r => r.json())
			.then(data => {
				if (result) result.style.display = 'block';
				if (data.success) {
					if (result) {
						result.innerHTML = '<div class="cts-notice cts-notice-success" role="status" aria-live="polite"><p>' + (data.data.message || 'Manueller Sync erfolgreich') + '</p></div>';
					}
					// If events filter form exists, submit it to refresh the list
					const eventsForm = document.querySelector('.cts-events form.cts-filter-section');
					if (eventsForm) {
						eventsForm.submit();
					}
				} else {
					if (result) {
						result.innerHTML = '<div class="cts-notice cts-notice-error" role="alert"><p>' + (data.data?.message || 'Sync fehlgeschlagen') + '</p></div>';
					}
				}
			})
			.catch(err => {
				if (result) { result.style.display = 'block'; result.innerHTML = '<div class="cts-notice cts-notice-error" role="alert"><p>Fehler: ' + err.message + '</p></div>'; }
			})
			.finally(() => {
				dataBtn.disabled = false;
				dataBtn.classList.remove('loading');
				dataBtn.removeAttribute('aria-busy');
				dataBtn.innerHTML = originalText;
			});
		});
	}

	/**
	 * Form Enhancements
	 */
	function initForms() {
		// Auto-dismiss notices
		const dismissButtons = document.querySelectorAll('.cts-notice-dismiss');
		dismissButtons.forEach(button => {
			button.addEventListener('click', function() {
				this.closest('.cts-notice').style.display = 'none';
			});
		});

		// Form validation
		const forms = document.querySelectorAll('.cts-form');
		forms.forEach(form => {
			form.addEventListener('submit', function(e) {
				const requiredFields = form.querySelectorAll('[required]');
				let valid = true;

				requiredFields.forEach(field => {
					if (!field.value.trim()) {
						valid = false;
						field.style.borderColor = '#dc3232';
					} else {
						field.style.borderColor = '';
					}
				});

				if (!valid) {
					e.preventDefault();
					alert('Bitte füllen Sie alle erforderlichen Felder aus.');
				}
			});
		});
	}

	/**
	 * Test Connection Button
	 */
	function initTestConnection() {
		const testButton = document.getElementById('cts-test-connection');
		if (!testButton) return;

		testButton.addEventListener('click', function() {
			const resultDiv = document.getElementById('cts-connection-result');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			testButton.disabled = true;
			const originalText = testButton.innerHTML;
			testButton.innerHTML = '<span>⏳</span> Teste Verbindung...';

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'cts_test_connection',
					nonce: churchtoolsSuite.nonce
				})
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						let message = data.data.message || 'Verbindung erfolgreich!';
						
						// User-Info anzeigen wenn verfügbar
						if (data.data.user_info) {
							const user = data.data.user_info;
							message += '<br><br><strong>Eingeloggt als:</strong><br>';
							if (user.firstName && user.lastName) {
								message += user.firstName + ' ' + user.lastName;
							}
							if (user.email) {
								message += ' (' + user.email + ')';
							}
						}
						
						resultDiv.innerHTML = '<div class="cts-notice cts-notice-success"><p>' + 
							message + 
							'</p></div>';
					} else {
						resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>' + 
							(data.data.message || 'Verbindung fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				testButton.disabled = false;
				testButton.innerHTML = originalText;
			});
		});
	}

	/**
	 * Calendar Sync Button
	 */
	function initCalendarSync() {
		const syncButton = document.getElementById('cts-sync-calendars-btn');
		if (!syncButton) return;

		syncButton.addEventListener('click', function() {
			const resultDiv = document.getElementById('cts-sync-calendars-result');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			syncButton.disabled = true;
			const originalText = syncButton.innerHTML;
			syncButton.innerHTML = '<span class="dashicons dashicons-update"></span> Synchronisiere...';

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'cts_sync_calendars',
					nonce: churchtoolsSuite.nonce
				})
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + 
							(data.data.message || 'Synchronisation erfolgreich!') + 
							'</p></div>';
						
						// Seite neu laden nach erfolgreicher Sync
						setTimeout(() => {
							location.reload();
						}, 1500);
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Synchronisation fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				syncButton.disabled = false;
				syncButton.innerHTML = originalText;
			});
		});
	}

	/**
	 * Calendar Selection Form
	 */
	function initCalendarSelection() {
		const form = document.getElementById('cts-calendar-selection-form');
		if (!form) return;

		// Select all checkbox
		const selectAllCheckbox = document.getElementById('cts-select-all-calendars');
		const calendarCheckboxes = document.querySelectorAll('.cts-calendar-checkbox');

		if (selectAllCheckbox && calendarCheckboxes.length > 0) {
			selectAllCheckbox.addEventListener('change', function() {
				calendarCheckboxes.forEach(checkbox => {
					checkbox.checked = selectAllCheckbox.checked;
				});
			});

			calendarCheckboxes.forEach(checkbox => {
				checkbox.addEventListener('change', function() {
					const totalCheckboxes = calendarCheckboxes.length;
					const checkedCheckboxes = document.querySelectorAll('.cts-calendar-checkbox:checked').length;
					selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
				});
			});
		}

		// Form submission
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			
			const resultDiv = document.getElementById('cts-calendar-selection-result');
			const submitButton = form.querySelector('button[type="submit"]');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			if (submitButton) {
				submitButton.disabled = true;
				const originalText = submitButton.innerHTML;
				submitButton.innerHTML = '<span class="dashicons dashicons-update"></span> Speichere...';
			}

			// Collect selected calendar IDs
			const selectedIds = [];
			calendarCheckboxes.forEach(checkbox => {
				if (checkbox.checked) {
					selectedIds.push(checkbox.value);
				}
			});

			// Build form data with array support
			const formData = new URLSearchParams();
			formData.append('action', 'cts_save_calendar_selection');
			formData.append('nonce', churchtoolsSuite.nonce);
			selectedIds.forEach(id => {
				formData.append('selected_ids[]', id);
			});

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + 
							(data.data.message || 'Auswahl gespeichert!') + 
							'</p></div>';
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Speichern fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				if (submitButton) {
					submitButton.disabled = false;
					submitButton.innerHTML = originalText;
				}
			});
		});
	}

	/**
	 * Service Group Sync Button
	 */
	function initServiceGroupSync() {
		const syncButton = document.getElementById('cts-sync-service-groups-btn');
		if (!syncButton) return;

		syncButton.addEventListener('click', function() {
			const resultDiv = document.getElementById('cts-sync-service-groups-result');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			syncButton.disabled = true;
			const originalText = syncButton.innerHTML;
			syncButton.innerHTML = '<span class="dashicons dashicons-update"></span> Synchronisiere...';

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'cts_sync_service_groups',
					nonce: churchtoolsSuite.nonce
				})
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + 
							(data.data.message || 'Synchronisation erfolgreich!') + 
							'</p></div>';
						
						// Seite neu laden nach erfolgreicher Sync
						setTimeout(() => {
							location.reload();
						}, 1500);
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Synchronisation fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				syncButton.disabled = false;
				syncButton.innerHTML = originalText;
			});
		});
	}

	/**
	 * Service Group Selection Form
	 */
	function initServiceGroupSelection() {
		const form = document.getElementById('cts-service-group-selection-form');
		if (!form) return;

		// Bulk-Action Buttons
		const selectAllBtn = document.getElementById('cts-select-all-groups-btn');
		const deselectAllBtn = document.getElementById('cts-deselect-all-groups-btn');
		const selectAllCheckbox = document.getElementById('cts-select-all-service-groups');
		const groupCheckboxes = document.querySelectorAll('.cts-service-group-checkbox');

		// Select All Button
		if (selectAllBtn) {
			selectAllBtn.addEventListener('click', function(e) {
				e.preventDefault();
				groupCheckboxes.forEach(checkbox => {
					checkbox.checked = true;
				});
				if (selectAllCheckbox) {
					selectAllCheckbox.checked = true;
				}
			});
		}

		// Deselect All Button
		if (deselectAllBtn) {
			deselectAllBtn.addEventListener('click', function(e) {
				e.preventDefault();
				groupCheckboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				if (selectAllCheckbox) {
					selectAllCheckbox.checked = false;
				}
			});
		}

		// Select all checkbox in table header
		if (selectAllCheckbox && groupCheckboxes.length > 0) {
			selectAllCheckbox.addEventListener('change', function() {
				groupCheckboxes.forEach(checkbox => {
					checkbox.checked = selectAllCheckbox.checked;
				});
			});

			groupCheckboxes.forEach(checkbox => {
				checkbox.addEventListener('change', function() {
					const totalCheckboxes = groupCheckboxes.length;
					const checkedCheckboxes = document.querySelectorAll('.cts-service-group-checkbox:checked').length;
					selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
				});
			});
		}

		// Form submission
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			
			const resultDiv = document.getElementById('cts-service-group-selection-result');
			const submitButton = form.querySelector('button[type="submit"]');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			if (submitButton) {
				submitButton.disabled = true;
				const originalText = submitButton.innerHTML;
				submitButton.innerHTML = '<span class="dashicons dashicons-update"></span> Speichere...';
			}

			// Collect selected group IDs
			const selectedIds = [];
			groupCheckboxes.forEach(checkbox => {
				if (checkbox.checked) {
					selectedIds.push(checkbox.value);
				}
			});

			// Build form data with array support
			const formData = new URLSearchParams();
			formData.append('action', 'cts_save_service_group_selection');
			formData.append('nonce', churchtoolsSuite.nonce);
			selectedIds.forEach(id => {
				formData.append('selected_ids[]', id);
			});

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + 
							(data.data.message || 'Auswahl gespeichert!') + 
							'</p></div>';
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Speichern fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				if (submitButton) {
					submitButton.disabled = false;
					submitButton.innerHTML = originalText;
				}
			});
		});
	}

	/**
	 * Service Sync Button
	 */
	function initServiceSync() {
		const syncButton = document.getElementById('cts-sync-services-btn');
		if (!syncButton) return;

		syncButton.addEventListener('click', function() {
			const resultDiv = document.getElementById('cts-sync-services-result');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			syncButton.disabled = true;
			const originalText = syncButton.innerHTML;
			syncButton.innerHTML = '<span class="dashicons dashicons-update"></span> Synchronisiere...';

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'cts_sync_services',
					nonce: churchtoolsSuite.nonce
				})
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + 
							(data.data.message || 'Synchronisation erfolgreich!') + 
							'</p></div>';
						
						// Seite neu laden nach erfolgreicher Sync
						setTimeout(() => {
							location.reload();
						}, 1500);
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Synchronisation fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				syncButton.disabled = false;
				syncButton.innerHTML = originalText;
			});
		});
	}

	/**
	 * Service Selection Form
	 */
	function initServiceSelection() {
		const form = document.getElementById('cts-service-selection-form');
		if (!form) return;

		// Bulk-Action Buttons
		const selectAllBtn = document.getElementById('cts-select-all-services-btn');
		const deselectAllBtn = document.getElementById('cts-deselect-all-services-btn');
		const selectAllCheckbox = document.getElementById('cts-select-all-services');
		const serviceCheckboxes = document.querySelectorAll('.cts-service-checkbox');

		// Select All Button
		if (selectAllBtn) {
			selectAllBtn.addEventListener('click', function(e) {
				e.preventDefault();
				serviceCheckboxes.forEach(checkbox => {
					checkbox.checked = true;
				});
				if (selectAllCheckbox) {
					selectAllCheckbox.checked = true;
				}
			});
		}

		// Deselect All Button
		if (deselectAllBtn) {
			deselectAllBtn.addEventListener('click', function(e) {
				e.preventDefault();
				serviceCheckboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				if (selectAllCheckbox) {
					selectAllCheckbox.checked = false;
				}
			});
		}

		// Select all checkbox in table header
		if (selectAllCheckbox && serviceCheckboxes.length > 0) {
			selectAllCheckbox.addEventListener('change', function() {
				serviceCheckboxes.forEach(checkbox => {
					checkbox.checked = selectAllCheckbox.checked;
				});
			});

			serviceCheckboxes.forEach(checkbox => {
				checkbox.addEventListener('change', function() {
					const totalCheckboxes = serviceCheckboxes.length;
					const checkedCheckboxes = document.querySelectorAll('.cts-service-checkbox:checked').length;
					selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
				});
			});
		}

		// Form submission
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			
			const resultDiv = document.getElementById('cts-service-selection-result');
			const submitButton = form.querySelector('button[type="submit"]');
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			if (submitButton) {
				submitButton.disabled = true;
				const originalText = submitButton.innerHTML;
				submitButton.innerHTML = '<span class="dashicons dashicons-update"></span> Speichere...';
			}

			// Collect selected service IDs
			const selectedIds = [];
			serviceCheckboxes.forEach(checkbox => {
				if (checkbox.checked) {
					selectedIds.push(checkbox.value);
				}
			});

			// Build form data with array support
			const formData = new URLSearchParams();
			formData.append('action', 'cts_save_service_selection');
			formData.append('nonce', churchtoolsSuite.nonce);
			selectedIds.forEach(id => {
				formData.append('selected_ids[]', id);
			});

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + 
							(data.data.message || 'Auswahl gespeichert!') + 
							'</p></div>';
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Speichern fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				if (submitButton) {
					submitButton.disabled = false;
					submitButton.innerHTML = originalText;
				}
			});
		});
	}

	/**
	 * Event Sync Button
	 */
	function initEventSync() {
		const syncButton = document.getElementById('cts-sync-events-btn');
		const forceFullSyncButton = document.getElementById('cts-force-full-sync-btn');
		if (!syncButton && !forceFullSyncButton) return;

		// Regular incremental sync
		if (syncButton) {
			syncButton.addEventListener('click', function() {
				performEventSync(false);
			});
		}

		// Force full sync (v0.7.1.0)
		if (forceFullSyncButton) {
			forceFullSyncButton.addEventListener('click', function() {
				if (!confirm('Vollständigen Sync erzwingen?\n\nDies wird ALLE Termine erneut synchronisieren, unabhängig vom letzten Änderungsdatum. Der normale inkrementelle Sync ist in den meisten Fällen ausreichend.\n\nFortfahren?')) {
					return;
				}
				performEventSync(true);
			});
		}

		/**
		 * Perform event sync (incremental or full)
		 * @param {boolean} forceFull - Force full sync instead of incremental
		 */
		function performEventSync(forceFull) {
			const resultDiv = document.getElementById('cts-sync-events-result');
			const button = forceFull ? forceFullSyncButton : syncButton;
			
			if (resultDiv) {
				resultDiv.style.display = 'none';
				resultDiv.innerHTML = '';
			}
			
			button.disabled = true;
			const originalText = button.innerHTML;
			const syncType = forceFull ? 'Vollständigen Sync' : 'Synchronisiere';
			button.innerHTML = '<span class="dashicons dashicons-' + (forceFull ? 'backup' : 'calendar') + '"></span> ' + syncType + '...';

			fetch(churchtoolsSuite.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: 'cts_sync_events',
					nonce: churchtoolsSuite.nonce,
					force_full: forceFull ? '1' : '0'
				})
			})
			.then(async response => {
				// v0.7.2.6: Better error handling for non-JSON responses
				const contentType = response.headers.get('content-type');
				
				// Check if response is JSON
				if (!contentType || !contentType.includes('application/json')) {
					const text = await response.text();
					const preview = text.substring(0, 200);
					
					if (response.status === 500) {
						throw new Error('WordPress 500 Internal Server Error. Mögliche Ursachen: PHP Fatal Error, Memory Limit erreicht, Plugin-Konflikt. Prüfen Sie den Debug-Tab unter "Logs" für Details.');
					}
					
					throw new Error(`Server lieferte HTML statt JSON (HTTP ${response.status}). Vorschau: ${preview}`);
				}
				
				return response.json();
			})
			.then(data => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					
					if (data.success) {
						let message = data.data.message || 'Synchronisation erfolgreich!';
						
						// Show sync type badge (v0.7.1.0)
						if (data.data.sync_type) {
							const syncTypeLabel = data.data.sync_type === 'incremental' ? 'INKREMENTELL' : 'VOLL';
							const syncTypeColor = data.data.sync_type === 'incremental' ? '#00a32a' : '#2271b1';
							message = '<span style="display: inline-block; padding: 2px 8px; font-size: 11px; font-weight: 600; color: white; background: ' + syncTypeColor + '; border-radius: 3px; margin-right: 6px;">' + syncTypeLabel + '</span>' + message;
						}
						
						resultDiv.innerHTML = '<div class="notice notice-success inline"><p>' + message + '</p></div>';
						
						// Seite neu laden nach erfolgreicher Sync
						setTimeout(() => {
							location.reload();
						}, 1500);
					} else {
						resultDiv.innerHTML = '<div class="notice notice-error inline"><p>' + 
							(data.data.message || 'Synchronisation fehlgeschlagen!') + 
							'</p></div>';
					}
				}
			})
			.catch(error => {
				if (resultDiv) {
					resultDiv.style.display = 'block';
					resultDiv.innerHTML = '<div class="notice notice-error inline"><p>Fehler: ' + 
						error.message + 
						'</p></div>';
				}
			})
			.finally(() => {
				button.disabled = false;
				button.innerHTML = originalText;
			});
		}
	}

	/**
	 * Manual Trigger: Event Sync
	 */
	function initManualTriggers() {
		const syncButton = document.getElementById('cts-trigger-manual-sync');
		const keepaliveButton = document.getElementById('cts-trigger-keepalive');
		const resultDiv = document.getElementById('cts-manual-trigger-result');
		
		if (syncButton) {
			syncButton.addEventListener('click', function() {
				if (resultDiv) {
					resultDiv.style.display = 'none';
					resultDiv.innerHTML = '';
				}
				
				syncButton.disabled = true;
				const originalText = syncButton.innerHTML;
				syncButton.innerHTML = '<span>⏳</span> Synchronisiere...';

				fetch(churchtoolsSuite.ajaxUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams({
						action: 'cts_trigger_manual_sync',
						nonce: churchtoolsSuite.nonce
					})
				})
				.then(response => response.json())
				.then(data => {
					if (resultDiv) {
						resultDiv.style.display = 'block';
						
						if (data.success) {
							resultDiv.innerHTML = '<div class="cts-notice cts-notice-success"><p>' + 
								(data.data.message || 'Sync erfolgreich!') + 
								'</p></div>';
							
							// Seite neu laden nach erfolgreicher Sync
							setTimeout(() => {
								location.reload();
							}, 2000);
						} else {
							resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>' + 
								(data.data.message || 'Sync fehlgeschlagen!') + 
								'</p></div>';
						}
					}
				})
				.catch(error => {
					if (resultDiv) {
						resultDiv.style.display = 'block';
						resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>Fehler: ' + 
							error.message + 
							'</p></div>';
					}
				})
				.finally(() => {
					syncButton.disabled = false;
					syncButton.innerHTML = originalText;
				});
			});
		}
		
		if (keepaliveButton) {
			keepaliveButton.addEventListener('click', function() {
				if (resultDiv) {
					resultDiv.style.display = 'none';
					resultDiv.innerHTML = '';
				}
				
				keepaliveButton.disabled = true;
				const originalText = keepaliveButton.innerHTML;
				keepaliveButton.innerHTML = '<span>⏳</span> Keepalive...';

				fetch(churchtoolsSuite.ajaxUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams({
						action: 'cts_trigger_keepalive',
						nonce: churchtoolsSuite.nonce
					})
				})
				.then(response => response.json())
				.then(data => {
					if (resultDiv) {
						resultDiv.style.display = 'block';
						
						if (data.success) {
							resultDiv.innerHTML = '<div class="cts-notice cts-notice-success"><p>' + 
								(data.data.message || 'Keepalive erfolgreich!') + 
								'</p></div>';
						} else {
							resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>' + 
								(data.data.message || 'Keepalive fehlgeschlagen!') + 
								'</p></div>';
						}
					}
				})
				.catch(error => {
					if (resultDiv) {
						resultDiv.style.display = 'block';
						resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>Fehler: ' + 
							error.message + 
							'</p></div>';
					}
				})
				.finally(() => {
					keepaliveButton.disabled = false;
					keepaliveButton.innerHTML = originalText;
				});
			});
		}

			// Manual update check button (migrated from inline jQuery)
			const manualUpdateButton = document.getElementById('cts-manual-update');
			if (manualUpdateButton) {
				manualUpdateButton.addEventListener('click', function() {
					const resultDiv = document.getElementById('cts-manual-trigger-result');
					if (resultDiv) { resultDiv.style.display = 'none'; resultDiv.innerHTML = ''; }
					manualUpdateButton.disabled = true;
					const originalText = manualUpdateButton.innerHTML;
					manualUpdateButton.innerHTML = '<span class="dashicons dashicons-update"></span> Prüfe...';

					fetch(churchtoolsSuite.ajaxUrl, {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: new URLSearchParams({ action: 'cts_manual_update', nonce: churchtoolsSuite.nonce })
					})
					.then(r => r.json())
					.then(data => {
						if (resultDiv) { resultDiv.style.display = 'block'; }
						if (data.success) {
							if (resultDiv) resultDiv.innerHTML = '<div class="cts-notice cts-notice-success"><p>' + (data.data.message || 'Update-Prüfung abgeschlossen') + '</p></div>';
						} else {
							if (resultDiv) resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>' + (data.data?.message || 'Update-Prüfung fehlgeschlagen') + '</p></div>';
						}
					})
					.catch(err => {
						if (resultDiv) resultDiv.style.display = 'block';
						if (resultDiv) resultDiv.innerHTML = '<div class="cts-notice cts-notice-error"><p>Fehler: ' + err.message + '</p></div>';
					})
					.finally(() => {
						manualUpdateButton.disabled = false;
						manualUpdateButton.innerHTML = originalText;
					});
				});
			}
	}

	/**
	 * Log Viewer: Reload Logs
	 */
	function initLogViewer() {
		const reloadButton = document.getElementById('cts-reload-logs');
		const clearButton = document.getElementById('cts-clear-logs');
		const logContent = document.getElementById('cts-log-content');
		
		if (reloadButton && logContent) {
			reloadButton.addEventListener('click', function() {
				reloadButton.disabled = true;
				const originalText = reloadButton.innerHTML;
				reloadButton.innerHTML = '<span>⏳</span> Lädt...';
				
				fetch(churchtoolsSuite.ajaxUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams({
						action: 'cts_reload_logs',
						nonce: churchtoolsSuite.nonce
					})
				})
				.then(response => response.json())
				.then(data => {
					if (data.success && data.data.html) {
						logContent.innerHTML = data.data.html;
						// Scroll to bottom
						logContent.scrollTop = logContent.scrollHeight;
					}
				})
				.catch(error => {
					console.error('Fehler beim Laden der Logs:', error);
				})
				.finally(() => {
					reloadButton.disabled = false;
					reloadButton.innerHTML = originalText;
				});
			});
		}
		
		if (clearButton && logContent) {
			clearButton.addEventListener('click', function() {
				if (!confirm('Möchten Sie wirklich alle Logs löschen?')) {
					return;
				}
				
				clearButton.disabled = true;
				const originalText = clearButton.innerHTML;
				clearButton.innerHTML = '<span>⏳</span> Löscht...';
				
				fetch(churchtoolsSuite.ajaxUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams({
						action: 'cts_clear_logs',
						nonce: churchtoolsSuite.nonce
					})
				})
				.then(response => response.json())
				.then(data => {
					if (data.success && data.data.html) {
						logContent.innerHTML = data.data.html;
					}
				})
				.catch(error => {
					console.error('Fehler beim Löschen der Logs:', error);
				})
				.finally(() => {
					clearButton.disabled = false;
					clearButton.innerHTML = originalText;
				});
			});
		}
	}

	/**
	 * Event Details Panels
	 * Delegated handler for .cts-details-toggle buttons
	 */
	function initEventDetailsPanels() {
		// Delegate clicks for detail toggles
		document.addEventListener('click', function(e) {
			const btn = e.target.closest('.cts-details-toggle');
			if (!btn) return;
			e.preventDefault();
			const targetId = btn.dataset.target || btn.getAttribute('data-target');
			if (!targetId) return;
			const panel = document.getElementById(targetId);
			if (!panel) return;
			const hidden = panel.classList.toggle('cts-hidden');
			panel.style.display = hidden ? 'none' : 'block';
		});
	}

	/**
	 * Helper: Show Loading Spinner
	 */
	function showLoading(element) {
		if (!element) return;
		element.classList.add('cts-loading');
	}

	/**
	 * Helper: Hide Loading Spinner
	 */
	function hideLoading(element) {
		if (!element) return;
		element.classList.remove('cts-loading');
	}

	/**
	 * Shortcode Generator
	 */
	function initShortcodeGenerator() {
		const viewTypeButtons = document.querySelectorAll('.cts-view-type-btn');
		const stepVariant = document.getElementById('cts-step-variant');
		const stepParams = document.getElementById('cts-step-params');
		const stepResult = document.getElementById('cts-step-result');
		const variantsContainer = document.getElementById('cts-view-variants');
		const generatedShortcode = document.getElementById('cts-generated-shortcode');
		const copyButton = document.getElementById('cts-copy-shortcode');
		const copyFeedback = document.getElementById('cts-copy-feedback');
		
		let selectedType = '';
		let selectedShortcode = '';
		let selectedVariant = '';
		
		// View type data
		const viewVariants = {
			calendar: ['monthly-modern', 'monthly-clean', 'monthly-classic', 'weekly-fluent', 'weekly-liquid', 'yearly', 'daily', 'daily-liquid'],
			list: ['classic', 'standard', 'modern', 'minimal', 'toggle', 'with-map', 'fluent', 'large-liquid', 'medium-liquid', 'small-liquid'],
			grid: ['simple', 'modern', 'minimal', 'ocean', 'classic', 'colorful', 'novel', 'with-map', 'large-liquid', 'medium-liquid', 'small-liquid', 'tile'],
			slider: ['type-1', 'type-2', 'type-3', 'type-4', 'type-5'],
			countdown: ['type-1', 'type-2', 'type-3'],
			cover: ['classic', 'modern', 'clean', 'fluent', 'liquid'],
			timetable: ['modern', 'clean', 'timeline'],
			carousel: ['type-1', 'type-2', 'type-3', 'type-4'],
			widget: ['upcoming-events', 'calendar-widget', 'countdown-widget']
		};
		
		// Step 1: View Type Selection
		viewTypeButtons.forEach(button => {
			button.addEventListener('click', function() {
				viewTypeButtons.forEach(btn => btn.classList.remove('active'));
				this.classList.add('active');
				
				selectedType = this.dataset.type;
				selectedShortcode = this.dataset.shortcode;
				
				showVariants(selectedType);
			});
		});
		
		// Show variants for selected type
		function showVariants(type) {
			const variants = viewVariants[type] || [];
			
			variantsContainer.innerHTML = '';
			
			variants.forEach(variant => {
				const button = document.createElement('button');
				button.className = 'cts-variant-btn';
				button.dataset.variant = variant;
				button.textContent = variant;
				
				button.addEventListener('click', function() {
					document.querySelectorAll('.cts-variant-btn').forEach(btn => btn.classList.remove('active'));
					this.classList.add('active');
					
					selectedVariant = variant;
					showParameters(type);
					generateShortcode();
				});
				
				variantsContainer.appendChild(button);
			});
			
			stepVariant.style.display = 'block';
			stepParams.style.display = 'none';
			stepResult.style.display = 'none';
		}
		
		// Show parameter fields based on type
		function showParameters(type) {
			// Hide all type-specific fields
			document.querySelectorAll('.cts-param-columns, .cts-param-services, .cts-param-autoplay, .cts-param-interval').forEach(el => {
				el.style.display = 'none';
			});
			
			// Show type-specific fields
			if (type === 'grid') {
				document.querySelector('.cts-param-columns').style.display = 'block';
			}
			
			if (type === 'list') {
				document.querySelector('.cts-param-services').style.display = 'block';
			}
			
			if (type === 'slider' || type === 'carousel') {
				document.querySelector('.cts-param-autoplay').style.display = 'block';
				document.querySelector('.cts-param-interval').style.display = 'block';
			}
			
			stepParams.style.display = 'block';
			stepResult.style.display = 'block';
		}
		
		// Generate shortcode
		function generateShortcode() {
			const params = [];
			
			// View
			if (selectedVariant) {
				params.push(`view="${selectedVariant}"`);
			}
			
			// Calendars
			const calendarSelect = document.getElementById('cts-param-calendar');
			const selectedCalendars = Array.from(calendarSelect.selectedOptions)
				.map(opt => opt.value)
				.filter(val => val !== '');
			
			if (selectedCalendars.length > 0) {
				params.push(`calendar="${selectedCalendars.join(',')}"`);
			}
			
			// Limit
			const limit = document.getElementById('cts-param-limit').value;
			if (limit && limit !== '20') {
				params.push(`limit="${limit}"`);
			}
			
			// From date
			const fromDate = document.getElementById('cts-param-from').value;
			if (fromDate) {
				params.push(`from="${fromDate}"`);
			}
			
			// To date
			const toDate = document.getElementById('cts-param-to').value;
			if (toDate) {
				params.push(`to="${toDate}"`);
			}
			
			// Columns (grid)
			if (selectedType === 'grid') {
				const columns = document.getElementById('cts-param-columns').value;
				if (columns && columns !== '3') {
					params.push(`columns="${columns}"`);
				}
			}
			
			// Show services (list)
			if (selectedType === 'list') {
				const showServices = document.getElementById('cts-param-services').value;
				if (showServices === 'false') {
					params.push(`show_services="false"`);
				}
			}
			
			// Autoplay (slider/carousel)
			if (selectedType === 'slider' || selectedType === 'carousel') {
				const autoplay = document.getElementById('cts-param-autoplay').value;
				if (autoplay === 'true') {
					params.push(`autoplay="true"`);
					
					const interval = document.getElementById('cts-param-interval').value;
					if (interval && interval !== '5000') {
						params.push(`interval="${interval}"`);
					}
				}
			}
			
			// Build shortcode
			const shortcode = `[${selectedShortcode}${params.length > 0 ? ' ' + params.join(' ') : ''}]`;
			
			generatedShortcode.textContent = shortcode;
		}
		
		// Update shortcode on parameter change
		document.querySelectorAll('#cts-step-params input, #cts-step-params select').forEach(input => {
			input.addEventListener('change', generateShortcode);
		});
		
		// Copy shortcode
		if (copyButton) {
			copyButton.addEventListener('click', function() {
				const shortcode = generatedShortcode.textContent;
				
				navigator.clipboard.writeText(shortcode).then(() => {
					copyFeedback.style.display = 'block';
					
					setTimeout(() => {
						copyFeedback.style.display = 'none';
					}, 3000);
				}).catch(err => {
					console.error('Copy failed:', err);
				});
			});
		}
		
		// Template copy buttons
		document.querySelectorAll('.cts-template-copy').forEach(button => {
			button.addEventListener('click', function() {
				const shortcode = this.dataset.shortcode;
				
				navigator.clipboard.writeText(shortcode).then(() => {
					const originalText = this.textContent;
					this.textContent = '✓ Kopiert!';
					
					setTimeout(() => {
						this.textContent = originalText;
					}, 2000);
				}).catch(err => {
					console.error('Copy failed:', err);
				});
			});
		});
	}
	
	/**
	 * Event Details Panel Toggle (v0.9.2.0)
	 * Close panel when clicking outside
	 */
	function initEventDetailsPanels() {
		document.addEventListener('click', function(e) {
			// Check if click is on a details toggle button
			if (e.target.closest('.cts-details-toggle')) {
				// Button click is handled inline via onclick
				return;
			}
			
			// Check if click is inside any details panel
			if (e.target.closest('.cts-event-details-panel')) {
				// Click inside panel, don't close
				return;
			}
			
			// Click outside - close all open panels
			const openPanels = document.querySelectorAll('.cts-event-details-panel:not(.cts-hidden)');
			openPanels.forEach(panel => {
				panel.classList.add('cts-hidden');
				panel.style.display = 'none';
			});
		});
	}

})();

