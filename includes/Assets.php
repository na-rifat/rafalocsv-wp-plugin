<?php

namespace Rafalocsv;

/**
 * Registers essential assets
 */
class Assets {
    /**
     * Construct assets class
     */
    function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'register'] );
        add_action( 'admin_enqueue_scripts', [$this, 'register'] );
        add_action( 'wp_enqueue_scripts', [$this, 'load'] );
        add_action( 'admin_enqueue_scripts', [$this, 'load'] );
    }

    /**
     * Return scripts from array
     *
     * @return array
     */
    public function get_scripts() {
        return [
            'rafalocsv-admin-script' => rafalocsv_jsfile( 'admin', ['jquery'] ),
        ];
    }

    /**
     * Return styles from array
     *
     * @return array
     */
    public function get_styles() {
        return [
            'rafalocsv-admin-styles' => rafalocsv_cssfile( 'admin' ),
            'rafalocsv-fontawesome'  => [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css',
                'version' => '5.15.2',
            ],
        ];
    }

    /**
     * Return localize variable from array
     *
     * @return array
     */
    public function get_localize() {
        global $post;
        return [
            'rafalocsv-admin-script' => [
                'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                'save_toggle_value_nonce'       => wp_create_nonce( 'save_toggle_value' ),
                'rafalocsv_upload_file_nonce'   => wp_create_nonce( 'rafalocsv_upload_file' ),
                'went_wrong'                    => __( 'Something went wrong!', 'rafalocsv' ),
                'compt_reset_theme_nonce'       => wp_create_nonce( 'compt_reset_theme' ),
                'rafalocsv_save_settings_nonce' => wp_create_nonce( 'rafalocsv_save_settings' ),
                'download_csv_template_nonce'   => wp_create_nonce( 'download_csv_template' ),
            ],
        ];
    }

    /**
     * Registers scripts, styles and localize variables
     *
     * @return void
     */
    public function register() {
        // Scripts
        $scripts = $this->get_scripts();

        foreach ( $scripts as $handle => $script ) {
            $deps = isset( $script['deps'] ) ? $script['deps'] : false;

            wp_register_script( $handle, $script['src'], $deps, ! empty( $script['version'] ) ? $script['version'] : false, true );

        }

        // Styles
        $styles = $this->get_styles();

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, ! empty( $style['version'] ) ? $style['version'] : false );
        }

        // Localization
        $localize = $this->get_localize();

        foreach ( $localize as $handle => $vars ) {
            wp_localize_script( $handle, 'rafalocsv', $vars );
        }
    }

    /**
     * Loads the scripts to frontend
     *
     * @return void
     */
    public function load() {
        if ( is_admin() ) {
            wp_enqueue_style( 'rafalocsv-admin-styles' );
            // wp_enqueue_style( 'rafalocsv-fontawesome' );

            wp_enqueue_script( 'rafalocsv-admin-script' );
            wp_enqueue_script( 'media-upload' );

            wp_enqueue_media();
            if ( isset( $_GET['page'] ) && ($_GET['page'] == 'rafalocsv' || $_GET['page'] == 'rafalocsv-general-settings' )) {
                wp_enqueue_style( 'rafalocsv-fontawesome' );
            }
        } else {

        }
    }
}