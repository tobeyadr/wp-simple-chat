<?php

namespace SimpleChat\Admin;

use function ExtendedCore\get_request_var;
use function SimpleChat\html;

class Metabox extends \ExtendedCore\Admin\Metabox {

	protected function get_name() {
		return __( 'WP Simple Chat' );
	}

	protected function get_id() {
		return 'wp_simple_chat';
	}

	protected function render( $post ) {
		$post_id = $post->ID;

		$greeting = get_post_meta( $post_id, 'wp_simple_chat_greeting', true );
		$disabled = get_post_meta( $post_id, 'wp_simple_chat_disabled', true );

		echo html()->e( 'p', [
		],
			[
				html()->e( 'label', [
					'for'   => 'wp_simple_chat_greeting',
					'style' => [ 'display' => 'block', 'margin-bottom' => '4px' ]
				],
					__( 'Custom chat greeting:', 'wp-simple-chat' )
				),
				html()->input( [
					'name'  => 'wp_simple_chat_greeting',
					'id'    => 'wp_simple_chat_greeting',
					'value' => $greeting,
					'style' => [ 'max-width' => '100%' ]
				] )
			]
		);

		$type = get_post_type( $post_id );

		echo html()->e( 'p', [],
			[
				html()->checkbox( [
					'name'    => 'wp_simple_chat_disabled',
					'value'   => 'enabled',
					'checked' => boolval( $disabled ),
					'label'   => sprintf( __( 'Disable on this %s.', 'groundhogg' ), $type )
				] )
			]
		);
	}

	/**
	 * @param $post_id
	 *
	 * @return mixed|void
	 */
	protected function save( $post_id ) {
		$meta_to_save = [
			'wp_simple_chat_greeting',
			'wp_simple_chat_disabled'
		];

		foreach ( $meta_to_save as $meta_key ) {
			$meta = sanitize_text_field( get_request_var( $meta_key ) );

			if ( $meta ) {
				update_post_meta( $post_id, $meta_key, $meta );
				continue;
			}

			delete_post_meta( $post_id, $meta_key );
		}
	}
}
