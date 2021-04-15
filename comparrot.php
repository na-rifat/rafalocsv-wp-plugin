<?php

/**
 * Plugin Name:       Rafalocsv
 * Plugin URI:        https://rafalocsv.nl
 * Description:       This plugin is used by the Rafalocsv theme.
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            LGX
 * Author URI:        https://rafalocsv.nl
 * License:           Commercial
 * Text Domain:       rafalocsv
 */
namespace Rafalocsv;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Rafalocsv\Processor\Processor;

require_once "vendor/autoload.php";

/**
 * Handles the plugin
 */
class Rafalocsv {
    use \Rafalocsv\Traits\Helper;
    const version     = '1.0';
    const plugin_name = 'Rafalocsv';
    const domain      = 'rafalocsv';
    /**
     * Usefull variables and class objects
     */

    /**
     * Builds the class
     */
    function __construct() {
        $this->define_constants();
        add_action( 'plugins_loaded', [$this, 'init'] );
        add_action( 'post_updated', [$this, 'modified_date'], 10, 2 );
        register_activation_hook( __FILE__, [$this, 'move_merge_template'] );
        add_action( 'save_post', [$this, 'prevent_br'] );

        add_action( 'before_delete_post', [$this, 'wps_remove_attachment_with_post'], 10 );
    }

    public function prevent_br() {
        remove_filter( 'the_content', 'wpautop' );
    }

    public function wps_remove_attachment_with_post( $post_id ) {

        if ( has_post_thumbnail( $post_id ) ) {
            $attachment_id = get_post_thumbnail_id( $post_id );
            wp_delete_attachment( $attachment_id, true );
        }

    }

    /**
     * Copying sample merge template in wp-contents
     *
     * @return void
     */
    public function move_merge_template() {

        // Creates the directory if not exists
        if ( ! file_exists( ABSPATH . '/wp-content/html-templates/' ) ) {
            mkdir( ABSPATH . '/wp-content/html-templates/' );
        }

        // Copying the file
        if ( ! file_exists( ABSPATH . '/wp-content/html-templates/merge_template_1.html' ) ) {
            $file          = fopen( ABSPATH . "/wp-content/html-templates/merge_template_1.html", 'w' );
            $template_file = fopen( __DIR__ . '/includes/Processor/views/merge_template_1.html', 'r' );

            $template = fread( $template_file, filesize( __DIR__ . '/includes/Processor/views/merge_template_1.html' ) );
            fwrite( $file, $template );

            fclose( $template_file );
            fclose( $file );
        }
    }

    /**
     * Creates the plugin class
     *
     * @return void
     */
    public static function create() {
        $is_created = false;

        if ( ! $is_created ) {
            $is_created = new self();
        }
        return $is_created;
    }

    /**
     * Initializes the class
     *
     * @return void
     */
    public function init() {
        // Instance creation
        if ( is_admin() ) {
            $admin = new Admin\Admin();
        }

        $csv       = new \Rafalocsv\Schema\CSV();
        $asset     = new Assets();
        $processor = new Processor();
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $ajax            = new Ajax();
            $ajax->processor = $processor;
            $ajax->init();
        }

        // Instance assign
        // Initialization part
        if ( is_admin() ) {
            $admin->init();
        }
        $processor->init();

    }

    /**
     * Defines the constatns
     *
     * @return void
     */
    public function define_constants() {
        define( 'COMPARROT_NAME', self::plugin_name );
        define( 'COMPARROT_VERSION', self::version );

        define( 'COMPARROT_PATH', __DIR__ );
        define( 'COMPARROT_FILE', __FILE__ );
        define( 'COMPARROT_PLUGIN_PATH', plugins_url( '', COMPARROT_FILE ) );
        define( 'COMPARROT_ASSETS', COMPARROT_PLUGIN_PATH . '/assets' );
        define( 'COMPARROT_JS', COMPARROT_ASSETS . '/js' );
        define( 'COMPARROT_CSS', COMPARROT_ASSETS . '/css' );
        define( 'COMPARROT_IMAGES', COMPARROT_ASSETS . '/img' );
        define( 'COMPARROT_FUNCTIONS', __DIR__ . '/includes/functions.php' );
    }

    /**
     * updates the last modified date
     *
     * @return void
     */
    public function modified_date( $post_id, $post ) {
        $date_format   = \Rafalocsv\Schema\Schema::get_settings( 'header' )['date-format']['value'];
        $modified_date = wp_date( $date_format, strtotime( $post->post_modified ) );

        $content  = Processor::update_modified_date( $modified_date, $post->post_content, 'UpdateDate' );
        $old_meta = get_post_meta( $post_id, 'rafalocsv-meta', true );

        $old_meta = preg_replace(
            '/<meta property="article:modified_time" content="(.*)" >/',
            sprintf( '<meta property="article:modified_time" content="%s" >', $modified_date ),
            $old_meta
        );

        remove_action( 'post_updated', [$this, 'modified_date'] );
        wp_update_post(
            [
                'ID'           => $post_id,
                'post_content' => $content,
            ]
        );
        update_post_meta( $post_id, 'rafalocsv-meta', $old_meta );

        add_action( 'post_updated', [$this, 'modified_date'], 10, 2 );
    }
}

/**
 * Creates the main instance
 *
 * @return void
 */
function create() {
    Rafalocsv::create();
}

create();
