<?php
/**
 * Homepage: cijfers.
 *
 * @package Jroen
 */

$jroen_stats = jroen_get_stats();
if ( ! $jroen_stats ) {
	return;
}
?>
<section class="stats">
	<div class="container stats__grid">
		<?php foreach ( $jroen_stats as $jroen_stat ) : ?>
			<div class="stat reveal">
				<span class="stat__number"><span class="js-count" data-target="<?php echo esc_attr( absint( $jroen_stat['number'] ) ); ?>"><?php echo esc_html( absint( $jroen_stat['number'] ) ); ?></span><?php echo esc_html( $jroen_stat['suffix'] ); ?></span>
				<span class="stat__label"><?php echo esc_html( $jroen_stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
