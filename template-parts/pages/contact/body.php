<?php
/**
 * Contact page: two-column body section (contact info + form).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = get_query_var( 'anna_contact_page_content', array() );
if ( empty( $contact ) ) {
	$contact = anna_get_contact_page_content();
}

$email   = anna_get_option( 'contact_email', 'info@annabaylis.com.au' );
$phone   = anna_get_option( 'contact_phone', '' );
$address = anna_get_option( 'contact_address', '' );
$hours   = anna_get_option( 'contact_hours', '' );

$form_action = ! empty( $contact['form_action_url'] ) ? $contact['form_action_url'] : '';

// Success/error notice from form submission redirect.
$form_status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<section class="anna-contact-page-body">
	<div class="anna-container anna-container--max">

		<?php if ( 'sent' === $form_status ) : ?>
			<div class="anna-contact-page-notice anna-contact-page-notice--success" role="status">
				<?php esc_html_e( 'Thank you — your message has been sent. I'll be in touch soon.', 'anna-baylis' ); ?>
			</div>
		<?php elseif ( 'error' === $form_status ) : ?>
			<div class="anna-contact-page-notice anna-contact-page-notice--error" role="alert">
				<?php esc_html_e( 'Something went wrong. Please check your details and try again.', 'anna-baylis' ); ?>
			</div>
		<?php endif; ?>

		<div class="anna-contact-page-body__grid">

			<!-- ── Left: Contact Information ──────────────────────────── -->
			<div class="anna-contact-page-info anna-reveal--left">
				<?php if ( ! empty( $contact['info_heading'] ) ) : ?>
					<h2 class="anna-contact-page-info__heading"><?php echo esc_html( $contact['info_heading'] ); ?></h2>
				<?php endif; ?>

				<ul class="anna-contact-page-info__list" role="list">

					<?php if ( $email ) : ?>
						<li class="anna-contact-page-info__item">
							<span class="anna-contact-page-info__icon" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="m22 6-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<div class="anna-contact-page-info__content">
								<span class="anna-contact-page-info__label"><?php esc_html_e( 'Email', 'anna-baylis' ); ?></span>
								<a href="mailto:<?php echo esc_attr( $email ); ?>" class="anna-contact-page-info__value"><?php echo esc_html( $email ); ?></a>
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $phone ) : ?>
						<li class="anna-contact-page-info__item">
							<span class="anna-contact-page-info__icon" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.98-.98a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<div class="anna-contact-page-info__content">
								<span class="anna-contact-page-info__label"><?php esc_html_e( 'Phone', 'anna-baylis' ); ?></span>
								<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>" class="anna-contact-page-info__value"><?php echo esc_html( $phone ); ?></a>
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $address ) : ?>
						<li class="anna-contact-page-info__item">
							<span class="anna-contact-page-info__icon" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<div class="anna-contact-page-info__content">
								<span class="anna-contact-page-info__label"><?php esc_html_e( 'Location', 'anna-baylis' ); ?></span>
								<span class="anna-contact-page-info__value"><?php echo nl2br( esc_html( $address ) ); ?></span>
							</div>
						</li>
					<?php endif; ?>

					<?php if ( $hours ) : ?>
						<li class="anna-contact-page-info__item">
							<span class="anna-contact-page-info__icon" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<div class="anna-contact-page-info__content">
								<span class="anna-contact-page-info__label"><?php esc_html_e( 'Hours', 'anna-baylis' ); ?></span>
								<span class="anna-contact-page-info__value"><?php echo esc_html( $hours ); ?></span>
							</div>
						</li>
					<?php endif; ?>

				</ul>

				<?php if ( ! empty( $contact['cta_card_heading'] ) ) : ?>
					<aside class="anna-contact-page-cta-card anna-reveal--scale">
						<h3 class="anna-contact-page-cta-card__heading"><?php echo esc_html( $contact['cta_card_heading'] ); ?></h3>
						<?php if ( ! empty( $contact['cta_card_body'] ) ) : ?>
							<p class="anna-contact-page-cta-card__body"><?php echo esc_html( $contact['cta_card_body'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $contact['cta_card_button_text'] ) && ! empty( $contact['cta_card_button_url'] ) ) : ?>
							<a href="<?php echo esc_url( $contact['cta_card_button_url'] ); ?>" class="anna-btn anna-btn--primary anna-contact-page-cta-card__btn">
								<?php echo esc_html( $contact['cta_card_button_text'] ); ?>
							</a>
						<?php endif; ?>
					</aside>
				<?php endif; ?>
			</div>

			<!-- ── Right: Contact Form ────────────────────────────────── -->
			<div class="anna-contact-page-form anna-reveal--right">
				<?php if ( ! empty( $contact['form_heading'] ) ) : ?>
					<h2 class="anna-contact-page-form__heading"><?php echo esc_html( $contact['form_heading'] ); ?></h2>
				<?php endif; ?>

				<form
					class="anna-contact-page-form__form"
					method="post"
					action="<?php echo $form_action ? esc_url( $form_action ) : esc_url( admin_url( 'admin-post.php' ) ); ?>"
					novalidate
				>
					<?php if ( ! $form_action ) : ?>
						<input type="hidden" name="action" value="anna_contact_form">
						<?php wp_nonce_field( 'anna_contact_form', 'anna_contact_nonce' ); ?>
					<?php endif; ?>

					<div class="anna-contact-page-form__field">
						<label for="anna-contact-name" class="anna-contact-page-form__label">
							<?php echo esc_html( $contact['form_name_label'] ); ?>
						</label>
						<input
							type="text"
							id="anna-contact-name"
							name="contact_name"
							class="anna-contact-page-form__input"
							placeholder="<?php esc_attr_e( 'Your name', 'anna-baylis' ); ?>"
							required
						>
					</div>

					<div class="anna-contact-page-form__field">
						<label for="anna-contact-email" class="anna-contact-page-form__label">
							<?php echo esc_html( $contact['form_email_label'] ); ?>
						</label>
						<input
							type="email"
							id="anna-contact-email"
							name="contact_email"
							class="anna-contact-page-form__input"
							placeholder="<?php esc_attr_e( 'your@email.com', 'anna-baylis' ); ?>"
							required
						>
					</div>

					<div class="anna-contact-page-form__field">
						<label for="anna-contact-subject" class="anna-contact-page-form__label">
							<?php echo esc_html( $contact['form_subject_label'] ); ?>
						</label>
						<input
							type="text"
							id="anna-contact-subject"
							name="contact_subject"
							class="anna-contact-page-form__input"
							placeholder="<?php esc_attr_e( 'What is your message about?', 'anna-baylis' ); ?>"
						>
					</div>

					<div class="anna-contact-page-form__field">
						<label for="anna-contact-message" class="anna-contact-page-form__label">
							<?php echo esc_html( $contact['form_message_label'] ); ?>
						</label>
						<textarea
							id="anna-contact-message"
							name="contact_message"
							rows="6"
							class="anna-contact-page-form__textarea"
							placeholder="<?php esc_attr_e( "Tell me about what you're looking for...", 'anna-baylis' ); ?>"
							required
						></textarea>
					</div>

					<button type="submit" class="anna-btn anna-btn--primary anna-contact-page-form__submit">
						<?php echo esc_html( $contact['form_button_text'] ); ?>
					</button>

					<?php if ( ! empty( $contact['form_response_note'] ) ) : ?>
						<p class="anna-contact-page-form__note"><?php echo wp_kses_post( $contact['form_response_note'] ); ?></p>
					<?php endif; ?>
				</form>
			</div>

		</div>
	</div>
</section>
