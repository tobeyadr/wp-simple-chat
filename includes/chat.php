<?php

namespace SimpleChat;

class Chat
{

	/**
	 * Chat constructor.
	 */
    public function __construct()
    {
        // insert after opening body tag
        add_action( 'wp_body_open', [ $this, 'render' ] );
    }

	/**
	 * Render the chat bubble after the opening body tag
	 */
    public function render()
    {
        // Get the business ID and the Enabled option
	    $enabled = get_simchat_option( 'enable_chat' );
	    $business_id = get_simchat_option( 'business_id' );

	    // If neither are set then do not show the chat
        if ( ! $enabled || ! $business_id ){
            return;
        }

        // Get the default greeting
        $greeting = get_simchat_option( 'default_greeting', __( 'Hi! How can we help?', 'wp-simple-chat' ) );
        $greeting_context = 'default';

        // Overwrite the greeting if the user is logged in
        if ( is_user_logged_in() ){
            $greeting = get_simchat_option( 'logged_in_greeting', $greeting );
            $greeting_context = 'logged_in';
        }

        // Check to see if the current post has an override for the greeting
        if ( is_singular() ){
            $maybe_greeting = get_post_meta( get_the_ID(), 'wp_simple_chat_greeting', true );

            if ( $maybe_greeting ){
                $greeting = $maybe_greeting;
                $greeting_context = 'singular';
            }
        }

        // Allow other plugins to modify the greeting
        $greeting = apply_filters( 'simple_chat/chat/greeting', $greeting, $greeting_context );

        // Allow other plugins to modify the theme color
	    $theme_color = apply_filters( 'simple_chat/chat/theme_color', get_simchat_option( 'theme_color' ) );

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

        <?php

        $atts = [
            'class'         => 'fb-customerchat',
            'attribution'   => 'setup_tool',
            'page_id'       => $business_id,
            'logged_in_greeting'   => $greeting,
            'logged_out_greeting'  => $greeting,
            'theme_color'   => $theme_color,
        ];

        // Allow other plugins to add additional information
        $atts = apply_filters( 'simple_chat/chat/chat_atts', $atts );

        echo html()->e( 'div', $atts, '', false );
    }
}
