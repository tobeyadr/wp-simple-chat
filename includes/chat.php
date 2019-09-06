<?php

namespace SimpleChat;

class Chat
{

    public function __construct()
    {
        add_action( 'wp_body_open', [ $this, 'render' ] );
    }

    public function render()
    {
        $business_id = get_simchat_option( 'business_id' );
        $enabled = get_simchat_option( 'enable_chat' );

        if ( ! $enabled || ! $business_id ){
            return;
        }

        $greeting = get_simchat_option( 'default_greeting', __( 'Hi! How can we help?', 'wp-simple-chat' ) );
        $greeting_context = 'default';

        if ( is_user_logged_in() ){
            $greeting = get_simchat_option( 'logged_in_greeting', $greeting );
            $greeting_context = 'logged_in';
        }

        if ( is_singular() ){
            $maybe_greeting = get_post_meta( get_the_ID(), 'wp_simple_chat_greeting', true );

            if ( $maybe_greeting ){
                $greeting = $maybe_greeting;
                $greeting_context = 'single';
            }
        }

        $greeting = apply_filters( 'simple_chat/chat/greeting', $greeting, $greeting_context );

        ?>
        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml            : true,
                    version          : 'v4.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>

        <!-- Your customer chat code -->
        <div class="fb-customerchat"
             attribution=setup_tool
             page_id="<?php esc_attr_e( $business_id )?>"
             logged_in_greeting="<?php esc_attr_e( $greeting )?>"
             logged_out_greeting="<?php esc_attr_e( $greeting )?>">
        </div>
        <?php
    }
}
