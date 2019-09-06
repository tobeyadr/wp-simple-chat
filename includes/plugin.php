<?php
namespace SimpleChat;

use SimpleChat\Admin\Metabox;
use SimpleChat\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {exit;}

/**
 * Groundhogg plugin.
 *
 * The main plugin handler class is responsible for initializing Groundhogg. The
 * class registers and all the components required to run the plugin.
 *
 * @since 2.0
 */
class Plugin {

    /**
     * @var HTML;
     */
    public $html;

    /**
     * Instance.
     *
     * Holds the plugin instance.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @var Plugin
     */
    public static $instance = null;

    /**
     * Clone.
     *
     * Disable class cloning and throw an error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object. Therefore, we don't want the object to be cloned.
     *
     * @access public
     * @since 1.0.0
     */
    public function __clone() {
        // Cloning instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'groundhogg' ), '2.0.0' );
    }

    /**
     * Wakeup.
     *
     * Disable unserializing of the class.
     *
     * @access public
     * @since 1.0.0
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'groundhogg' ), '2.0.0' );
    }

    /**
     * Instance.
     *
     * Ensures only one instance of the plugin class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @return Plugin An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Init.
     *
     * Initialize Groundhogg Plugin. Register Groundhogg support for all the
     * supported post types and initialize Groundhogg components.
     *
     * @since 1.0.0
     * @access public
     */
    public function init() {

        $this->includes();
        $this->init_components();

        /**
         * Groundhogg init.
         *
         * Fires on Groundhogg init, after Groundhogg has finished loading but
         * before any headers are sent.
         *
         * @since 1.0.0
         */
        do_action( 'simple_chat/init' );
    }

    /**
     * Init components.
     *
     * Initialize Groundhogg components. Register actions, run setting manager,
     * initialize all the components that run groundhogg, and if in admin page
     * initialize admin components.
     *
     * @since 1.0.0
     * @access private
     */
    private function init_components() {
        $this->html = new HTML();

        if ( is_admin() ){
            new Settings();
            new Metabox();
        } else {
            new Chat();
        }
    }

    /**
     * Register autoloader.
     *
     * Groundhogg autoloader loads all the classes needed to run the plugin.
     *
     * @since 1.6.0
     * @access private
     */
    private function register_autoloader() {
        require dirname(__FILE__) . '/autoloader.php';
        Autoloader::run();
    }

    /**
     * Include the extended core framework
     */
    private function include_core()
    {
        include dirname(__FILE__ ) . '/core/extended-core.php';
    }

    /**
     * Plugin constructor.
     *
     * Initializing Groundhogg plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function __construct() {

        $this->register_autoloader();
        $this->include_core();

        if ( did_action( 'plugins_loaded' ) ){
            $this->init();
        } else {
            add_action( 'plugins_loaded', [ $this, 'init' ], 0 );
        }
    }

    /**
     * Include other files
     */
    private function includes()
    {
        include dirname( __FILE__ ) . '/functions.php';
    }
}

Plugin::instance();