<?php

namespace Rafalocsv;

use Rafalocsv\Schema\Schema;

/**
 * Handles ajax requests
 */
class Ajax {
    use \Rafalocsv\Traits\File;
    /**
     * Useful variables
     */
    function __construct() {

    }

    /**
     * Initializes the class
     *
     * @return void
     */
    function init() {
        $this->register();
    }

    /**
     * Registers ajax requests
     *
     * @return void
     */
    public function register() {
        rafalocsv_ajax( 'rafalocsv_upload_file', [$this, 'upload_files'] );
        rafalocsv_ajax( 'save_toggle_value', [$this, 'save_toggle_value'] );
        rafalocsv_ajax( 'download_csv_template', [$this, 'download_csv_template'] );
        rafalocsv_ajax( 'save_logo_url', [$this, 'save_logo_url'] );
        rafalocsv_ajax( 'compt_reset_theme', [$this, 'compt_reset_theme'] );

        // New
        rafalocsv_ajax( 'rafalocsv_save_settings', [$this, 'rafalocsv_save_settings'] );

    }

    public function upload_files() {
        $csv_files   = [];
        $image_files = [];
        $csv         = new \Rafalocsv\Schema\CSV();

        // Nonce verification
        if ( ! wp_verify_nonce( rafalocsv_var( 'nonce' ), 'rafalocsv_upload_file' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid nonce!', 'rafalocsv' ),
                ]
            );
            exit;
        }

        // Collecting data from post
        $image_files = self::get_files_by_ext( $_FILES['all_files'], ['jpg', 'jpeg', 'bmp', 'png'] );
        $image_files = self::make_image_key( $image_files );

        // Merging CSV fields
        $csv_file = self::get_files_by_ext( $_FILES['all_files'], ['csv'] );

        if ( sizeof( $csv_file ) == 0 ) {
            \Rafalocsv\Processor\Processor::attach_non_csv_images( [], $image_files );
            \Rafalocsv\Processor\Processor::json_status(
                [
                    'success' => true,
                    'msg'     => __( 'No CSV file were found, uploaded images has been inserted.', 'rafalocsv' ),
                ]
            );
            exit;
        }

        if ( empty( $csv_file ) ) {
            \Rafalocsv\Processor\Processor::json_status(
                [
                    'success' => false,
                    'msg'     => __( 'No CSV file found!', 'rafalocsv' ),
                ]
            );
            exit;
        }

        $csv_data = $csv::file_to_array( $csv_file[0] );

        \Rafalocsv\Processor\Processor::begin_inserting_pages( $csv_data, $image_files );

    }

    /**
     * Handles CSV template download request
     *
     * @return void
     */
    public function download_csv_template() {
        // Nonce verification
        if ( ! wp_verify_nonce( rafalocsv_var( 'nonce' ), 'download_csv_template' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid nonce!', 'rafalocsv' ),
                ]
            );
            exit;
        }

        wp_send_json_success(
            [
                'msg' => __( 'File is ready to download.', 'rafalocsv' ),
                'url' => \Rafalocsv\Schema\CSV::create_template( 'csv_fields' ),
            ]
        );
        exit;
    }

    /**
     * Reset theme settings in Schema1
     *
     * @return void
     */
    public function compt_reset_theme() {

        if ( ! wp_verify_nonce( rafalocsv_var( 'nonce' ), 'compt_reset_theme' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid nonce!', 'rafalocsv' ),
                ]
            );
            exit;
        }

        \Rafalocsv\Schema\Schema::reset_all_settings();

        wp_send_json_success(
            [
                'msg' => __( 'Your theme reset operation succeeded', 'rafalocsv' ),
            ]
        );
        exit;

    }

    /**
     * Stores logo url to the database
     *
     * @return void
     */
    public function save_logo_url() {
        $url = rafalocsv_var( 'url' );

        update_option( 'compt-logo', $url );

        wp_send_json_success(
            [
                'msg' => __( 'Logo uploaded succesfully', 'rafalocsv' ),
            ]
        );
        exit;
    }

    /**
     * Stores toggle value
     *
     * @return void
     */
    public function save_toggle_value() {
        $atts = [
            'nonce' => rafalocsv_var( 'nonce' ),
            'key'   => rafalocsv_var( 'key' ),
            'value' => rafalocsv_var( 'value' ),
        ];

        if ( ! wp_verify_nonce( $atts['nonce'], 'save_toggle_value' ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Invalid nonce', 'rafalocsv' ),
                ]
            );
            exit;
        }

        update_option( $atts['key'], $atts['value'] );

        wp_send_json_success(
            [
                'message' => __( 'Key settings saved sucessfully', 'rafalocsv' ),
            ]
        );
        exit;
    }

    /**
     * Stores a schema settings from frontend request to the database
     *
     * @return void
     */
    public function rafalocsv_save_settings() {
        // Nonce check

        if ( ! wp_verify_nonce( rafalocsv_var( 'nonce' ), 'rafalocsv_save_settings' ) ) {
            wp_send_json_error(

                [
                    'msg' => __( 'Invalid nonce!', 'rafalocsv' ),
                ]
            );
            exit;
        }

        $schema  = new \Rafalocsv\Schema\Schema();
        $current = $schema::set_settings( rafalocsv_var( 'form' ),
            $schema::merge_schema_values(
                $schema::get_posted_settings_data(
                    rafalocsv_var( 'form' )
                ),
                $schema::get_settings( rafalocsv_var( 'form' ) )
            )
        );

        self::additional_settings( rafalocsv_var( 'form' ),
            $schema::merge_schema_values(
                $schema::get_posted_settings_data(
                    rafalocsv_var( 'form' )
                ),
                $schema::get_settings( rafalocsv_var( 'form' ) )
            )
        );

        wp_send_json_success(
            [
                'data' => $current,
                'msg'  => __( 'Settings saved successfully!', 'rafalocsv' ),
            ]
        );exit;

    }

    public static function additional_settings( $name, $schema ) {
        switch ( $name ) {
            case 'header':
                update_option( 'blogname', $schema['site-title']['value'] );
                break;
        }
    }

}