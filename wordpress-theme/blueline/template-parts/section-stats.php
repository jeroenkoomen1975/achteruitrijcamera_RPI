<?php
/**
 * Homepage: cijfers.
 *
 * @package Blueline
 */

$blueline_stats = blueline_get_stats();
if ( ! $blueline_stats ) {
	return;
}
?>
<section class="stats">
	<div class="container stats__grid">
		<?php foreach ( $blueline_stats as $blueline_stat ) : ?>
			<div class="stat reveal">
				<span class="stat__number"><span class="js-count" data-target="<?php echo esc_attr( absint( $blueline_stat['number'] ) ); ?>"><?php echo esc_html( absint( $blueline_stat['number'] ) ); ?></span><?php echo esc_html( $blueline_stat['suffix'] ); ?></span>
				<span class="stat__label"><?php echo esc_html( $blueline_stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
