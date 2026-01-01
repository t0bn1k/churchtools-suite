<?php
/**
 * Calendar Views Demo
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
		<span class="cts-card-icon">📅</span>
		<h3><?php esc_html_e( 'Calendar Views', 'churchtools-suite' ); ?></h3>
	</div>
	<div class="cts-card-body">
		<?php cts_demo_embed_public_css(); ?>
		
		<!-- Monthly Modern -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Monthly Modern</h4>
				<code>[cts_calendar view="monthly-modern"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_calendar view="monthly-modern"]' ); ?>
			</div>
		</div>
		
	</div>
</div>
