<?php
/**
 * Timetable Views Demo
 *
 * @package ChurchTools_Suite
 * @since   0.5.9.25
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load demo helpers
require_once __DIR__ . '/demo-helpers.php';
?>

<div class="cts-card">
	<div class="cts-card-header">
		<span class="cts-card-icon">🕐</span>
		<h3><?php esc_html_e( 'Timetable Views', 'churchtools-suite' ); ?></h3>
	</div>
	<div class="cts-card-body">
		<?php cts_demo_embed_public_css(); ?>
		
		<!-- Timetable Modern -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Timetable Modern</h4>
				<code>[cts_timetable view="modern" limit="10"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_timetable view="modern" limit="10"]' ); ?>
			</div>
		</div>
		
		<!-- Timetable Clean -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Timetable Clean</h4>
				<code>[cts_timetable view="clean" limit="10"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_timetable view="clean" limit="10"]' ); ?>
			</div>
		</div>
		
		<!-- Timetable Timeline -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Timetable Timeline</h4>
				<code>[cts_timetable view="timeline" limit="10"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_timetable view="timeline" limit="10"]' ); ?>
			</div>
		</div>
		
	</div>
</div>
