<?php
/**
 * Contactsectie met formulier, onderaan elke pagina.
 *
 * @package Jroen
 */

$jroen_notice  = jroen_contact_notice();
$jroen_email   = jroen_mod( 'jroen_topbar_email' );
$jroen_sites   = jroen_mod( 'jroen_contact_sites' );
$jroen_region  = jroen_mod( 'jroen_contact_region' );
$jroen_subject = jroen_contact_subjects();
?>
<section id="contact" class="section contact">
	<div class="container contact__grid">
		<div class="contact__intro">
			<?php if ( jroen_mod( 'jroen_contact_eyebrow' ) ) : ?>
				<span class="label-mono"><?php echo esc_html( jroen_mod( 'jroen_contact_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h2 class="contact__title"><?php echo esc_html( jroen_mod( 'jroen_contact_title' ) ); ?></h2>
			<p><?php echo esc_html( jroen_mod( 'jroen_contact_text' ) ); ?></p>
			<ul class="contact__lines">
				<?php if ( $jroen_email ) : ?>
					<li><span class="contact__icon" aria-hidden="true">@</span><a href="mailto:<?php echo esc_attr( antispambot( $jroen_email ) ); ?>"><?php echo esc_html( antispambot( $jroen_email ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $jroen_sites ) : ?>
					<li><span class="contact__icon" aria-hidden="true">&lt;&gt;</span><span><?php echo esc_html( $jroen_sites ); ?></span></li>
				<?php endif; ?>
				<?php if ( $jroen_region ) : ?>
					<li><span class="contact__icon" aria-hidden="true">#</span><span><?php echo esc_html( $jroen_region ); ?></span></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="contact__card">
			<?php if ( $jroen_notice ) : ?>
				<p class="contact__notice contact__notice--<?php echo esc_attr( $jroen_notice['type'] ); ?>" role="status"><?php echo esc_html( $jroen_notice['text'] ); ?></p>
			<?php endif; ?>
			<form class="contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="jroen_contact">
				<?php wp_nonce_field( 'jroen_contact', 'jroen_contact_nonce' ); ?>
				<div class="contact__hp" aria-hidden="true">
					<label for="jroen_website"><?php esc_html_e( 'Laat dit veld leeg', 'jroen' ); ?></label>
					<input type="text" id="jroen_website" name="jroen_website" tabindex="-1" autocomplete="off">
				</div>
				<div class="contact__row">
					<p>
						<label for="jroen_name"><?php esc_html_e( 'Naam *', 'jroen' ); ?></label>
						<input type="text" id="jroen_name" name="jroen_name" required autocomplete="name" placeholder="<?php esc_attr_e( 'bijv. Jeroen', 'jroen' ); ?>">
					</p>
					<p>
						<label for="jroen_email"><?php esc_html_e( 'E-mailadres *', 'jroen' ); ?></label>
						<input type="email" id="jroen_email" name="jroen_email" required autocomplete="email" placeholder="naam@domein.nl">
					</p>
				</div>
				<?php if ( $jroen_subject ) : ?>
					<p>
						<label for="jroen_subject"><?php esc_html_e( 'Onderwerp', 'jroen' ); ?></label>
						<select id="jroen_subject" name="jroen_subject">
							<?php foreach ( $jroen_subject as $jroen_s ) : ?>
								<option value="<?php echo esc_attr( $jroen_s ); ?>"><?php echo esc_html( $jroen_s ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				<?php endif; ?>
				<p>
					<label for="jroen_message"><?php esc_html_e( 'Bericht *', 'jroen' ); ?></label>
					<textarea id="jroen_message" name="jroen_message" rows="4" required placeholder="<?php esc_attr_e( 'Vertel kort wat je wilt maken of vragen…', 'jroen' ); ?>"></textarea>
				</p>
				<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Verstuur bericht', 'jroen' ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></button>
			</form>
		</div>
	</div>
</section>
