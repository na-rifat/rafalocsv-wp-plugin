<div class="cp-admin-body">
    <?php
        $schema = new \Rafalocsv\Schema\Schema();
    ?>
    <div class="rafalocsv-admin-container">
        <h1><?php _e( 'Rafalocsv - general settings', 'rafalocsv' )?></h1>
        <hr>
        <div class="rafalocsv-admin-row">
            <?php
                echo $schema::submit(
                    [
                        'label' => __( 'Reset settings', 'rafalocsv' ),
                        'class' => ['rafalocsv-settings-reset-button'],
                    ]
                );

            ?>

            <table class="form-table">
                <th>
                    <label for="logo-selector">Select website logo</label>
                </th>
                <td>
                    <div class="site-logo logo-selector-holder" id="logo-selector">
                        <div class="logo-selector">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <img src="<?php echo get_option( 'compt-logo', '' ) ?>" alt="Site logo">
                    </div>
                </td>
            </table>
            <?php
                echo $schema::create_settings_form(
                    [
                        'settings_key' => 'header',
                        'class'        => ['rafalocsv-settings-form'],
                        'admin'        => true,
                    ]
                );
                echo $schema::create_settings_form(
                    [
                        'settings_key' => 'header_buttons',
                        'class'        => ['rafalocsv-settings-form'],
                        'admin'        => true,
                    ]
                );
                echo $schema::submit(
                    [
                        'label' => __( 'Save', 'rafalocsv' ),
                        'class' => ['rafalocsv-save-settings'],
                    ]
                );

            ?>
        </div>
    </div>
</div>