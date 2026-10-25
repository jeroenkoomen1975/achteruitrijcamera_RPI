<?php
/**
 * Reacties.
 *
 * @package Jroen
 */

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			/* translators: %s: aantal reacties */
			printf( esc_html( _n( '%s reactie', '%s reacties', get_comments_number(), 'jroen' ) ), esc_html( number_format_i18n( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Reageren is gesloten.', 'jroen' ); ?></p>
	<?php endif; ?>

	<?php comment_form( array( 'class_submit' => 'btn btn--primary' ) ); ?>
</div>
