<?php
/**
 * Class Notifications -- Create and output notifications for WordPress
 * Use example:
 *
 * // Instantiate the class
 * $notice = new Asset_Manager\Notification();
 *
 * // Call the class
 * echo $notice->the_notice(
 *     'success', // Notice type (can be any of error, info, success, warning)
 *     __('Notification message text', AM_TEXT), //The notification text
 * );
 *
 * @since   0.5.0
 *
 * @package Asset_Manager
 */

namespace Asset_Manager;

class Admin_Notice {

	/**
	 * @var string $type Message type
	 */
	private $type;

	/**
	 * @var string $body Message body
	 */
	private $body;

	/**
	 * @var array $allowed_html HTML tags and attributes allowed in the message
	 */
	private $allowed_html = [
		'a'      => [
			'href' => [],
			'rel'  => [],
		],
		'br'     => [],
		'div'    => [
			'class' => [],
		],
		'em'     => [],
		'p'      => [],
		'strong' => [],
	];

	/**
	 * @var array $allowed_types allowed notification types
	 */
	private $allowed_types = [
		'error',
		'info',
		'success',
		'warning'
	];

	/**
	 * Notification constructor.
	 *
	 * @param string $type Notification type
	 * @param string $body Notification body
	 *
	 * @since 0.5.0
	 */
	public function __construct( $type = '', $body = '' ) {

		$this->type = $type;
		$this->body = $body;

		/**
		 * Quick sanity check. If there's no message body,
		 * then there's no reason to do anything else.
		 *
		 * @since 0.5.0
		 */
		if ( ! $this->body || ( ! empty( $this->body ) && '' !== $this->body ) ) {
			return;
		}

		/**
		 * filter the $allowed_tags array
		 */
		$this->allowed_html = apply_filters( 'am_admin_notices_allowed_html', $this->allowed_html );

		/**
		 * Hook into the admin_notices function and display the notice.
		 *
		 * @since 0.5.0
		 */
		add_action( 'admin_notices', [ $this, 'the_notice' ] );
	}

	/**
	 * Get the notification classes
	 *
	 * @param string $type notification type
	 *
	 * @since  0.5.0
	 *
	 * @return string
	 */
	public function the_classes( string $type ): string {

		/**
		 * Array of base classes that apply to all notifications.
		 *
		 * @since 0.5.0
		 */
		$classes = [
			'notice',
			'is-dismissible',
		];

		/**
		 * Make sure something is returned for type, either
		 * the supplied type, or a default.
		 *
		 * @since 0.5.0
		 */
		$type = in_array( $type, $this->allowed_types, true ) ? $type : 'info';

		/**
		 * Add our type to the classes array
		 *
		 * @since 0.5.0
		 */
		$classes[] = 'notice-' . $type;

		//Return the notification classes
		return implode( ' ', $classes );

	}

	/**
	 * Return our formatted notification body
	 *
	 * @param string $body notification body
	 *
	 * @since  0.5.0
	 *
	 * @return string
	 */
	public function the_body( string $body ): string {
		return wpautop( wp_kses( $body, $this->allowed_html ) );
	}

	/**
	 * Build the notification
	 *
	 * @param string $type notification type
	 * @param string $body notification body
	 *
	 * @since  0.5.0
	 */
	public function the_notice( string $type, string $body ) {

		$allowed_html = $this->allowed_html;
		$classes      = esc_attr( $this->the_classes( $type ) );
		$body         = $this->the_body( $body );

		printf(
			wp_kses(
				'<div class="%1$s">%2$s</div>',
				$allowed_html
			),
			$classes,
			$body
		);
	}

}