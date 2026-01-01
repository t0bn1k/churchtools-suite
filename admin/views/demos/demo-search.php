<?php
/**
 * Search Views Demo
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
		<span class="cts-card-icon">🔍</span>
		<h3><?php esc_html_e( 'Search Views', 'churchtools-suite' ); ?></h3>
	</div>
	<div class="cts-card-body">
		<?php cts_demo_embed_public_css(); ?>
		
		<!-- Search Bar -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Search Bar</h4>
				<code>[cts_search view="bar"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_search view="bar"]' ); ?>
			</div>
		</div>
		
		<!-- Advanced Search -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Advanced Search</h4>
				<code>[cts_search view="advanced"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_search view="advanced"]' ); ?>
			</div>
		</div>
		
	</div>
</div>
