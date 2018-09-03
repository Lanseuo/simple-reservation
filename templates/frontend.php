<?php
    require_once( plugin_dir_path( dirname( __FILE__, 1 ) )."/inc/Api/Callbacks/FrontendCallbacks.php" );
    $frontend_callbacks = new FrontendCallbacks();
    $frontend_callbacks->action();

    $rooms = $frontend_callbacks->get_rooms();
    if ( ! $rooms ) echo '<p>Keine Räume eingerichtet.</p>';

    // TODO: Not weekend
    $days_of_week = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
    $days = [
        [ 'name' => $days_of_week[date('w')], 'date' => date('d.m.Y') ],
        [ 'name' => $days_of_week[date('w', strtotime('+1 day'))], 'date' =>  date('d.m.Y', strtotime('+1 day')) ],
        [ 'name' => $days_of_week[date('w', strtotime('+2 day'))], 'date' =>  date('d.m.Y', strtotime('+2 day')) ],
        [ 'name' => $days_of_week[date('w', strtotime('+3 day'))], 'date' =>  date('d.m.Y', strtotime('+3 day')) ],
        [ 'name' => $days_of_week[date('w', strtotime('+4 day'))], 'date' =>  date('d.m.Y', strtotime('+4 day')) ]
    ];

    // TODO: Get from options
    $room_times = [
        [ 'id' => 0, 'name' => '1. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 1, 'name' => '2. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 2, 'name' => '3. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 3, 'name' => '4. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 4, 'name' => '5. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 5, 'name' => '6. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 6, 'name' => '7. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 7, 'name' => '8. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 8, 'name' => '9. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'id' => 9, 'name' => '10. Stunde', 'description' => '8.05 - 8:55 Uhr' ]
    ];

    add_thickbox();
?>
<div class="simple-reservation">
    <ul class="tabs">
        <?php foreach ($rooms as $room_index => $room) { ?>
            <li
                class="<?php echo $room_index == 0 ? 'active ' : '' ?>"
                data-tab-id="<?php echo $room->id; ?>">
                <a><?php echo $room->name; ?></a></li>
        <?php } ?>
    </ul>

    <?php foreach ($rooms as $room_index => $room) { ?>
        <div
            class="tabs-content <?php echo $room_index == 0 ? 'active ' : '' ?>"
            data-tab-id="<?php echo $room->id; ?>">
            <p><?php echo $room->description; ?></p>

            <div class="week">
                <div class="header">Reservierungen</div>
                <div class="body">
                    <div class="day-topbar"></div>
                    <?php
                        foreach ( $days as $day ) {
                            echo '<div class="day-topbar"><strong>'.$day['name'].'</strong></div>';
                        }
                    ?>

                    <?php foreach ( $room_times as $room_time ) { ?>
                        <div class="time-sidebar">
                                <p><strong><?php echo $room_time['name'] ?></strong></p>
                                <p><?php echo $room_time['description'] ?></p>
                        </div>          
                        <?php foreach ( $days as $day ) {
                            $reservation = $frontend_callbacks->get_reservation( $room->id, $day['date'], $room_time['id'] );
                            if ($reservation) {
                                $deletable = wp_get_current_user()->ID == $reservation->user_id;
                                echo '
                                <form class="period reserved '.( $deletable ? ' deletable ' : '' ).' remove-style" method="post">
                                <input type="hidden" name="action" value="delete_reservation">
                                <input type="hidden" name="id" value="'.$reservation->id.'">
                                <button type="submit" '.( $deletable ? '' : 'disabled' ).'>
                                    <div class="content">
                                        <p><strong>'.$reservation->user.'</strong></p>
                                        <p>'.$reservation->description.'</p>
                                    </div>
                                    <span class="delete-symbol dashicons dashicons-trash"></span>
                                </button>
                                </form>
                                ';
                            } else {
                                $thickbox_id = 'thickbox-'.$room->id.'-'.str_replace('.', '', $day['date']).'-'.$room_time['id'];
                                echo '
                                <!-- Thickbox -->
                                <div id="'.$thickbox_id.'" style="display:none;">
                                    <h4>Neue Reservierung</h4>
                                    <p>
                                        This is my hidden content! It will appear in ThickBox when the link is clicked.
                                    </p>
                                    <form method="post">
                                        <input type="hidden" name="action" value="add_reservation">
                                        <input type="hidden" name="room_id" value="'.$room->id.'">
                                        <input type="hidden" name="date" value="'.$day['date'].'">
                                        <input type="hidden" name="time_id" value="'.$room_time['id'].'">

                                        <table class="form-table">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Name</th>
                                                    <td>
                                                        <input value="'.wp_get_current_user()->display_name.'" disabled type="text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Raum</th>
                                                    <td>
                                                        <input value="'.$room->name.'" disabled type="text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Datum</th>
                                                    <td>
                                                        <input value="'.$day['name'].', '.$day['date'].'" disabled type="text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Zeit</th>
                                                    <td>
                                                        <input value="'.$room_time['name'].'" disabled type="text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Anmerkung</th>
                                                    <td>
                                                        <input name="description" type="text">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="button-wrapper">
                                            <button class="simple-reservation primary" type="submit">Speichern</button>
                                        </div>
                                    </form>
                                </div>

                                <a class="thickbox period free" href="#TB_inline?width=600&height=550&inlineId='.$thickbox_id.'">
                                    <span class="add-symbol">+</span>
                                </a>
                                ';
                            }
                        ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    
        
    <?php } ?>
</div>