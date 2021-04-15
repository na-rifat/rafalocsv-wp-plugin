<?php

namespace Rafalocsv\Admin;

/**
 * Handles admin functions
 */
class Admin {
    /**
     * Useful variables
     */

    /**
     * Builds the class
     */
    function __construct() {
        add_action( 'admin_menu', [$this, 'register_menu'] );
    }

    /**
     * Initializes the class
     *
     * @return void
     */
    public function init() {
        // $this->register_menu();
    }

    /**
     * Registers menu for admin panel
     *
     * @return void
     */
    public function register_menu() {
        $parent_slug = 'rafalocsv';
        $capability  = 'manage_options';
        add_menu_page( 'Rafalocsv', 'Rafalocsv', $capability, $parent_slug, [$this, 'import_from_csv_page'], 'dashicons-admin-page', 2 );
        add_submenu_page( $parent_slug, __( 'Import from CSV', 'rafalocsv' ), __( 'Import from CSV', 'rafalocsv' ), $capability, $parent_slug, [$this, 'import_from_csv_page'] );
        add_submenu_page( $parent_slug, __( 'General settings', 'rafalocsv' ), __( 'General settings', 'rafalocsv' ), $capability, 'rafalocsv-general-settings', [$this, 'general_settings_page'] );
        add_submenu_page( $parent_slug, __( 'Style settings', 'rafalocsv' ), __( 'Style settings', 'rafalocsv' ), $capability, 'rafalocsv-style-settings', [$this, 'style_settings'] );
        // add_submenu_page( $parent_slug, __( 'Import settings', 'rafalocsv' ), __( 'Import settings', 'rafalocsv' ), $capability, 'rafalocsv-import-settings', [$this, 'import_settings'] );
    }

    // public function import_settings(){
    //     include __DIR__ . "/views/import_settings.php";
    // }
    /**
     * Returns admin page template
     *
     * @return void
     */
    public function import_from_csv_page() {
        include __DIR__ . "/views/admin_page.php";
    }

    /**
     * General settings page template
     *
     * @return void
     */
    public function general_settings_page() {
        include __DIR__ . "/views/general_settings.php";
    }

    /**
     * Styles settings page template
     *
     * @return void
     */
    public function style_settings() {
        include __DIR__ . "/views/style_settings.php";
    }

}