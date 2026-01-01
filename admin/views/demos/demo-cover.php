<?php
/**
 * Cover Views Demo
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
		<span class="cts-card-icon">🖼️</span>
		<h3><?php esc_html_e( 'Cover Views', 'churchtools-suite' ); ?></h3>
	</div>
	<div class="cts-card-body">
		<?php cts_demo_embed_public_css(); ?>
		
		<!-- Cover Classic -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Cover Classic</h4>
				<code>[cts_cover view="classic" limit="1"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_cover view="classic" limit="1"]' ); ?>
			</div>
		</div>
		
		<!-- Cover Modern -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Cover Modern</h4>
				<code>[cts_cover view="modern" limit="1"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_cover view="modern" limit="1"]' ); ?>
			</div>
		</div>
		
		<!-- Cover Clean -->
		<div class="cts-demo-item">
			<div class="cts-demo-item-header">
				<h4>Cover Clean</h4>
				<code>[cts_cover view="clean" limit="1"]</code>
			</div>
			<div class="cts-demo-item-preview">
				<?php echo do_shortcode( '[cts_cover view="clean" limit="1"]' ); ?>
			</div>
		</div>
		
	</div>
</div>
