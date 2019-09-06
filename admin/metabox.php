<?php

namespace SimpleChat\Admin;

use function ExtendedCore\get_request_var;
use function SimpleChat\html;

class Metabox extends \ExtendedCore\Admin\Metabox
{

    protected function get_name()
    {
        return __('WP Simple Chat');
    }

    protected function get_id()
    {
        return 'wp_simple_chat';
    }

    protected function render($post)
    {
        $post_id = $post->ID;

        $greeting = get_post_meta( $post_id, 'wp_simple_chat_greeting', true );

        echo html()->e('div', [
            'class' => 'components-base-control'
        ], html()->e('div', [
            'class' => 'components-base-control__field'
        ],
            [
                html()->e('label', [
                    'class' => 'components-base-control__label'
                ],
                    __('Custom chat greeting:', 'wp-simple-chat')
                ),
                html()->input([
                    'name' => 'wp_simple_chat_greeting',
                    'value' => $greeting,
                    'class' => 'components-text-control__input'
                ])
            ]
        )
        );
    }

    /**
     * @param $post_id
     * @return mixed|void
     */
    protected function save($post_id)
    {
        if ( $greeting = get_request_var( 'wp_simple_chat_greeting' ) ){
            $greeting = sanitize_text_field( $greeting );
            update_post_meta( $post_id, 'wp_simple_chat_greeting', $greeting );
            return;
        }

        delete_post_meta( $post_id, 'wp_simple_chat_greeting' );
    }
}
