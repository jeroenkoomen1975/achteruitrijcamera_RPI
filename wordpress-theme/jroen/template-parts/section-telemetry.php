<?php
/**
 * Homepage: telemetrie van een ESP32 of Raspberry Pi.
 * Met een JSON-adres in de Customizer worden echte meetwaarden getoond;
 * zonder adres draait de sectie op voorbeelddata, duidelijk gelabeld als "Demo-data".
 *
 * @package Jroen
 */

$jroen_url     = jroen_mod( 'jroen_telemetry_url' );
$jroen_metrics = array();
foreach ( array( 1, 2, 3 ) as $jroen_i ) {
	$jroen_label = jroen_mod( "jroen_telemetry_m{$jroen_i}_label" );
	if ( '' !== trim( (string) $jroen_label ) ) {
		$jroen_metrics[] = array(
			'label' => $jroen_label,
			'unit'  => jroen_mod( "jroen_telemetry_m{$jroen_i}_unit" ),
			'key'   => jroen_mod( "jroen_telemetry_m{$jroen_i}_key" ),
		);
	}
}
$jroen_relay  = jroen_mod( 'jroen_telemetry_relay_label' );
$jroen_config = array(
	'url'      => $jroen_url,
	'interval' => max( 1, absint( jroen_mod( 'jroen_telemetry_interval' ) ) ) * 1000,
	'relayKey' => jroen_mod( 'jroen_telemetry_relay_key' ),
);
?>
<section id="telemetrie" class="section section--light telemetry" data-telemetry="<?php echo esc_attr( wp_json_encode( $jroen_config ) ); ?>">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<div>
				<span class="telemetry__eyebrow">
					<span class="pulse" aria-hidden="true"></span>
					<?php echo esc_html( jroen_mod( 'jroen_telemetry_eyebrow' ) ); ?>
					<span class="telemetry__mode<?php echo $jroen_url ? ' is-live' : ''; ?>"><?php echo $jroen_url ? esc_html__( 'Live', 'jroen' ) : esc_html__( 'Demo-data', 'jroen' ); ?></span>
				</span>
				<h2 class="section-title"><?php echo esc_html( jroen_mod( 'jroen_telemetry_title' ) ); ?></h2>
			</div>
			<p class="telemetry__meta">
				<?php
				/* translators: %d: aantal seconden */
				echo esc_html( sprintf( __( 'Verversen: %d s', 'jroen' ), $jroen_config['interval'] / 1000 ) );
				?>
				<span aria-hidden="true">•</span>
				<?php esc_html_e( 'Metingen:', 'jroen' ); ?> <strong class="js-telemetry-count">0</strong>
			</p>
		</div>

		<div class="telemetry__grid">
			<?php foreach ( $jroen_metrics as $jroen_index => $jroen_metric ) : ?>
				<div class="telemetry__card" data-key="<?php echo esc_attr( $jroen_metric['key'] ); ?>" data-index="<?php echo (int) $jroen_index; ?>">
					<span class="telemetry__label"><?php echo esc_html( $jroen_metric['label'] ); ?></span>
					<span class="telemetry__value"><strong class="js-telemetry-value">–</strong> <small><?php echo esc_html( $jroen_metric['unit'] ); ?></small></span>
				</div>
			<?php endforeach; ?>
			<?php if ( $jroen_relay ) : ?>
				<div class="telemetry__card telemetry__card--relay">
					<span class="telemetry__label"><?php echo esc_html( $jroen_relay ); ?> <span class="telemetry__led js-relay-led" aria-hidden="true"></span></span>
					<span class="telemetry__relay">
						<strong class="js-relay-state"><?php esc_html_e( 'UIT', 'jroen' ); ?></strong>
						<?php if ( ! $jroen_url ) : ?>
							<button type="button" class="telemetry__switch js-relay-toggle"><?php esc_html_e( 'Schakel', 'jroen' ); ?></button>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $jroen_metrics ) : ?>
			<div class="telemetry__chart">
				<canvas class="js-telemetry-canvas" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: naam van de meetwaarde */ __( 'Grafiek van %s', 'jroen' ), $jroen_metrics[0]['label'] ) ); ?>" role="img"></canvas>
			</div>
		<?php endif; ?>
		<p class="telemetry__error" hidden><?php esc_html_e( 'De live data is op dit moment niet bereikbaar.', 'jroen' ); ?></p>
	</div>
</section>
