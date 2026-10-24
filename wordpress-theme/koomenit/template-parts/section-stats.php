<?php
/**
 * Homepage: cijfers.
 *
 * @package KoomenIT
 */

$koomenit_stats = koomenit_get_stats();
if ( ! $koomenit_stats ) {
	return;
}
?>
<section class="stats">
	<div class="container stats__grid">
		<?php foreach ( $koomenit_stats as $koomenit_stat ) : ?>
			<div class="stat reveal">
				<span class="stat__number"><span class="js-count" data-target="<?php echo esc_attr( absint( $koomenit_stat['number'] ) ); ?>"><?php echo esc_html( absint( $koomenit_stat['number'] ) ); ?></span><?php echo esc_html( $koomenit_stat['suffix'] ); ?></span>
				<span class="stat__label"><?php echo esc_html( $koomenit_stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
