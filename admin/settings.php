<?php

namespace SimpleChat\Admin;

use ExtendedCore\Admin\Admin_Page;
use SimpleChat\Mailhawk_Connect;
use SimpleChat\Plugin;
use function ExtendedCore\get_array_var;
use function ExtendedCore\get_request_var;
use function SimpleChat\get_simchat_option;
use function SimpleChat\html;
use function SimpleChat\is_groundhogg_installed;
use function SimpleChat\simchat_option_name;

class Settings extends Admin_Page {

	### Unused Functions ###

	protected function add_ajax_actions() {
	}

	protected function add_additional_actions() {
	}

	public function get_item_type() {
	}

	public function scripts() {
	}

	public function load_page() {
	}

	### Used Functions ###

	public function get_title_actions() {
		return [];
	}

	protected function get_parent_slug() {
		return 'options-general.php';
	}

	public function get_slug() {
		return 'wp-simple-chat';
	}

	public function get_name() {
		return "WP Simple Chat";
	}

	public function get_cap() {
		return 'manage_options';
	}

	/**
	 * The settings array
	 *
	 * @return array[]
	 */
	protected function get_settings() {
		$settings = [
			'general'           => [
				'name'     => __( 'General Settings', 'wp-simple-chat' ),
				'settings' => [
					[
						'type'   => 'checkbox',
						'option' => 'enable_chat',
						'label'  => __( 'Enable FB chat', 'wp-simple-chat' ),
						'field'  => [
							'label' => __( 'Enable' )
						],
						'desc'   => __( 'Show the Facebook chat on your site. <a href="https://wpsimple.chat/allow-chat-on-your-website/">Chat not appearing?</a>', 'wp-simple-chat' ),
					],
					[
						'type'   => 'input',
						'option' => 'business_id',
						'label'  => __( 'Facebook Business Id', 'wp-simple-chat' ),
						'desc'   => __( 'Your facebook business ID. <a href="https://wpsimple.chat/find-your-facebook-page-id/">Found in your Facebook page.</a>', 'wp-simple-chat' ),
					],
					[
						'type'   => 'input',
						'option' => 'default_greeting',
						'label'  => __( 'Default Greeting', 'wp-simple-chat' ),
						'desc'   => __( 'The default greeting message your visitors will see.', 'wp-simple-chat' ),
					],
					[
						'type'   => 'color_picker',
						'option' => 'theme_color',
						'label'  => __( 'Chat Color', 'wp-simple-chat' ),
						'desc'   => __( 'The color of the Chat Bubble.', 'wp-simple-chat' ),
					],
				],
			],
			'advanced_greeting' => [
				'name'     => __( 'Advanced Greeting Message', 'wp-simple-chat' ),
				'settings' => [
					[
						'type'   => 'input',
						'option' => 'logged_in_greeting',
						'label'  => __( 'Logged In Greeting (WordPress)', 'wp-simple-chat' ),
						'desc'   => __( 'The greeting message your visitors will see if they are logged into WordPress.', 'wp-simple-chat' ),
					],
				]
			],
			'display'           => [
				'name'     => __( 'Chat Display Settings', 'wp-simple-chat' ),
				'settings' => [
					[
						'type'   => 'checkbox',
						'option' => 'hide_on_mobile',
						'label'  => __( 'Hide chat on mobile', 'wp-simple-chat' ),
						'field'  => [
							'label' => __( 'Enable' )
						],
						'desc'   => __( 'Whether to hide the chat on mobile.', 'wp-simple-chat' ),
					],
					[
						'type'   => 'checkbox',
						'option' => 'hide_when_logged_in',
						'label'  => __( 'Hide chat when logged in', 'wp-simple-chat' ),
						'field'  => [
							'label' => __( 'Enable' )
						],
						'desc'   => __( 'Hide the chat bubble when logged in.', 'wp-simple-chat' ),
					],
				],
			],
			'greeting'          => [
				'name'     => __( 'Greeting Display Settings', 'wp-simple-chat' ),
				'settings' => [
					[
						'type'   => 'checkbox',
						'option' => 'hide_greeting',
						'label'  => __( 'Do not show greeting.', 'wp-simple-chat' ),
						'field'  => [
							'label' => __( 'Enable' )
						],
						'desc'   => __( 'Prevent the message greeting from showing automatically.', 'wp-simple-chat' ),
					],
					[
						'type'   => 'checkbox',
						'option' => 'hide_greeting_on_mobile',
						'label'  => __( 'Hide greeting on mobile', 'wp-simple-chat' ),
						'field'  => [
							'label' => __( 'Enable' )
						],
						'desc'   => __( 'Do not show the chat greeting on mobile.', 'wp-simple-chat' ),
					],
					[
						'type'   => 'checkbox',
						'option' => 'hide_greeting_when_logged_in',
						'label'  => __( 'Hide greeting when logged in', 'wp-simple-chat' ),
						'field'  => [
							'label' => __( 'Enable' )
						],
						'desc'   => __( 'Hide the chat bubble when logged in.', 'wp-simple-chat' ),
					],
				]
			]
		];

		if ( is_groundhogg_installed() ) {
			$settings['advanced_greeting']['settings'][] = [
				'type'   => 'input',
				'option' => 'groundhogg_greeting',
				'label'  => __( 'Personalized Greeting (Groundhogg)', 'wp-simple-chat' ),
				'desc'   => __( 'Personalize your greeting with Groundhogg replacement codes.', 'wp-simple-chat' ),

			];
		}

		return apply_filters( "simple_chat/settings", $settings );
	}

	public function view() {

		?>
        <h2 style="font-weight: 300">A <a target="_blank" href="https://www.groundhogg.io/"><img width="125" style="vertical-align: middle;margin: 0 10px" src="<?php echo SIMPLE_CHAT_ASSETS_URL . 'groundhogg-logo.png' ?>"/></a> Product!</h2>
		<form method="post"><?php

		wp_nonce_field( 'save' );
		echo html()->input( [ 'type' => 'hidden', 'name' => 'action', 'value' => 'save' ] );

		foreach ( $this->get_settings() as $section ) {

			$section_name = get_array_var( $section, 'name' );
			$settings     = get_array_var( $section, 'settings' );

			if ( ! empty( $settings ) ) {
				echo html()->e( 'h3', [], $section_name );
				html()->start_form_table();

				foreach ( $settings as $setting ) {

					$setting = wp_parse_args( $setting, [
						'type'   => 'input',
						'name'   => '',
						'field'  => [],
						'label'  => '',
						'option' => '',
						'desc'   => '',
					] );

					$setting['field']['name'] = simchat_option_name( $setting['option'] );

					$value = get_simchat_option( $setting['option'] );

					switch ( $setting['type'] ) {
						case 'select2':
						case 'dropdown':
							$setting['field']['selected'] = $value;
							break;
						case 'checkbox':
							$setting['field']['checked'] = $value;
							break;
						default:
							$setting['field']['value'] = $value;
							break;
					}

					html()->start_row();
					html()->th( $setting['label'] );
					html()->td( [
						call_user_func( [ Plugin::$instance->html, $setting['type'] ], $setting['field'] ),
						html()->description( $setting['desc'] ),
					] );
					html()->end_row();

				}

				html()->end_form_table();
			}
		}

		submit_button();

		?></form>
        <div class="mailhawk-connect">
            <h3><?php _e( 'Sending email from this site?', 'wp-simple-chat' ); ?></h3>
            <?php Mailhawk_Connect::instance()->connect_ui(); ?>
        </div>
        <?php



	}

	protected function get_allowed_options() {
		$settings = $this->get_settings();

		$allowed_options = [];

		foreach ( $settings as $section ) {
			$allowed_options = array_merge( $allowed_options, wp_list_pluck( $section['settings'], 'option' ) );
		}

		return $allowed_options;
	}

	public function process_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'failed', 'Failed to update.' );
		}

		$options = get_request_var( 'wp_simple_chat_options' );

		$options          = array_filter( $options );
		$filtered_options = [];
		$allowed_options  = $this->get_allowed_options();

		foreach ( $options as $option_name => $val ) {
			if ( in_array( $option_name, $allowed_options ) ) {
				$filtered_options[ $option_name ] = $val;
			}
		}

		$filtered_options = map_deep( $filtered_options, 'sanitize_text_field' );

		$yes = update_option( 'wp_simple_chat_options', $filtered_options );

		if ( $yes ) {
			$this->add_notice( 'saved', __( 'Settings updated!' ) );
		}

		return false;
	}

	protected function get_filter_prefix() {
		return "simple_chat";
	}
}
