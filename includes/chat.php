<?php

namespace SimpleChat;

use ExtendedCore\Browser;
use function Groundhogg\do_replacements;
use function Groundhogg\get_contactdata;

class Chat {

	/**
	 * @var Browser
	 */
	protected $browser;

	/**
	 * Chat constructor.
	 */
	public function __construct() {
		// insert after opening body tag
		add_action( 'wp_body_open', [ $this, 'setup_browser' ], 1 );
		add_action( 'wp_body_open', [ $this, 'render' ] );

		// Fallback inserts after footer.
		add_action( 'sim_chat_body_close', [ $this, 'setup_browser' ], 1 );
		add_action( 'sim_chat_body_close', [ $this, 'render' ] );

		add_filter( 'simple_chat/chat/show_chat', [ $this, 'hide_chat_if_mobile' ] );
		add_filter( 'simple_chat/chat/show_chat', [ $this, 'hide_chat_if_logged_in' ] );
		add_filter( 'simple_chat/chat/show_chat', [ $this, 'hide_chat_if_disabled_on_page' ] );

		add_filter( 'simple_chat/chat/show_greeting', [ $this, 'greeting_is_disabled' ] );
		add_filter( 'simple_chat/chat/show_greeting', [ $this, 'hide_greeting_if_mobile' ] );
		add_filter( 'simple_chat/chat/show_greeting', [ $this, 'hide_greeting_if_logged_in' ] );

		/**
		 * Groundhogg is installed.
		 */
		if ( is_groundhogg_installed() ) {
			add_filter( 'simple_chat/chat/greeting', [ $this, 'do_groundhogg_replacements' ], 99 );
		}
	}

	/**
	 * Perform replacements on the greeting
	 *
	 * @param $greeting
	 *
	 * @return string
	 */
	public function do_groundhogg_replacements( $greeting ) {
		if ( ! is_groundhogg_installed() ) {
			return $greeting;
		}

		return do_replacements( $greeting );
	}

	/**
	 * Setup the browser object
	 */
	public function setup_browser() {
		$this->browser = new Browser();
	}

	/**
	 * Hide the chat if the user is logged in
	 *
	 * @param $enabled
	 *
	 * @return bool
	 */
	public function hide_chat_if_logged_in( $enabled ) {
		return get_simchat_option( 'hide_when_logged_in' ) && is_user_logged_in() ? false : $enabled;
	}

	/**
	 * Hide the chat if the user is logged in
	 *
	 * @param $enabled
	 *
	 * @return bool
	 */
	public function hide_chat_if_disabled_on_page( $enabled ) {
		if ( is_singular() && get_the_ID() ) {
			$disabled = boolval( get_post_meta( get_the_ID(), 'wp_simple_chat_disabled', true ) );

			return ! $disabled;
		}

		return $enabled;
	}

	/**
	 * Hide the chat if the browser is mobile
	 *
	 * @param $enabled
	 *
	 * @return bool
	 */
	public function hide_chat_if_mobile( $enabled ) {
		return get_simchat_option( 'hide_on_mobile' ) && $this->browser->isMobile() ? false : $enabled;
	}

	/**
	 * Hide the chat if the user is logged in
	 *
	 * @param $enabled
	 *
	 * @return bool
	 */
	public function greeting_is_disabled( $enabled ) {
		return get_simchat_option( 'hide_greeting' ) ? false : $enabled;
	}

	/**
	 * Hide the greeting if the browser is mobile
	 *
	 * @param $enabled
	 *
	 * @return bool
	 */
	public function hide_greeting_if_mobile( $enabled ) {
		return get_simchat_option( 'hide_greeting_on_mobile' ) && $this->browser->isMobile() ? false : $enabled;
	}

	/**
	 * Hide the greeting if the browser is mobile
	 *
	 * @param $enabled
	 *
	 * @return bool
	 */
	public function hide_greeting_if_logged_in( $enabled ) {
		return get_simchat_option( 'hide_greeting_when_logged_in' ) && is_user_logged_in() ? false : $enabled;
	}

	/**
	 * Render the chat bubble after the opening body tag
	 */
	public function render() {
		// Get the business ID and the Enabled option
		$enabled     = get_simchat_option( 'enable_chat' );
		$business_id = get_simchat_option( 'business_id' );

		$enabled = apply_filters( 'simple_chat/chat/show_chat', $enabled );

		// If neither are set then do not show the chat
		if ( ! $enabled || ! $business_id ) {
			return;
		}

		// Get the default greeting
		$greeting         = get_simchat_option( 'default_greeting', __( 'Hi! How can we help?', 'wp-simple-chat' ) );
		$greeting_context = 'default';

		// Overwrite the greeting if the user is logged in
		if ( is_user_logged_in() ) {
			$greeting         = get_simchat_option( 'logged_in_greeting', $greeting );
			$greeting_context = 'logged_in';
		}

		// Override with the Groundhogg personalized greeting
		if ( is_groundhogg_installed() && get_contactdata() ) {
			$greeting         = get_simchat_option( 'groundhogg_greeting', $greeting );
			$greeting_context = 'groundhogg';
		}

		// Check to see if the current post has an override for the greeting
		if ( is_singular() ) {
			$maybe_greeting = get_post_meta( get_the_ID(), 'wp_simple_chat_greeting', true );

			if ( $maybe_greeting ) {
				$greeting         = $maybe_greeting;
				$greeting_context = 'singular';
			}
		}

		// Allow other plugins to modify the greeting
		$greeting = apply_filters( 'simple_chat/chat/greeting', $greeting, $greeting_context );

		// Allow other plugins to modify the theme color
		$theme_color   = apply_filters( 'simple_chat/chat/theme_color', get_simchat_option( 'theme_color' ) );
		$show_greeting = apply_filters( 'simple_chat/chat/show_greeting', true );
		$show_greeting = $show_greeting ? 'show' : 'hide';

		?>
		<!-- Load Facebook SDK for JavaScript -->
		<div id="fb-root"></div>
		<script>
          window.fbAsyncInit = function () {
            FB.init({
              xfbml: true,
              version: 'v4.0'
            })
          };

          (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0]
            if (d.getElementById(id)) return
            js = d.createElement(s)
            js.id = id
            js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js'
            fjs.parentNode.insertBefore(js, fjs)
          }(document, 'script', 'facebook-jssdk'))</script>

		<?php

		$atts = [
			'class'                   => 'fb-customerchat',
			'attribution'             => 'setup_tool',
			'page_id'                 => $business_id,
			'logged_in_greeting'      => $greeting,
			'logged_out_greeting'     => $greeting,
			'greeting_dialog_display' => $show_greeting,
			'theme_color'             => $theme_color,
		];

		// Allow other plugins to add additional information
		$atts = apply_filters( 'simple_chat/chat/chat_atts', $atts );

		echo html()->e( 'div', $atts, '', false );
	}
}
