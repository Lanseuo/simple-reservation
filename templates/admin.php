<div class="wrap">
    <h1>SimpleReservation</h1>
    <?php settings_errors(); ?>

    <form action="options.php" method="post">
        <?php
            settings_fields('simple_reservation_settings');
            do_settings_sections( 'simple_reservation' );
            submit_button();
        ?>
    </form>
</div>