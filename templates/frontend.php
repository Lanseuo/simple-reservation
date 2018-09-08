<?php
    namespace Templates;
    use Inc\Api\Callbacks\FrontendCallbacks;
    
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
    <?php $frontend_callbacks->show_notices(); ?>

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
                    
                    <div class="day-topbar empty"></div>
                    <?php foreach ( $room_times as $room_time ) { ?>
                        <div class="time-sidebar">
                                <p><strong><?php echo $room_time['name'] ?></strong></p>
                                <p><?php echo $room_time['description'] ?></p>
                        </div>
                    <?php } ?> 

                    <?php foreach ( $days as $day ) {
                        echo '<div class="day-topbar"><strong>'.$day['name'].'</strong></div>';
                    ?>
                        <?php for ( $room_time_index = 0; $room_time_index < count($room_times); $room_time_index++ ) {
                            $room_time = $room_times[$room_time_index];
                            $reservation = $frontend_callbacks->get_reservation( $room->id, $day['date'], $room_time['id'] );

                            if ($reservation) {
                                $deletable = wp_get_current_user()->ID == $reservation->user_id;

                                // TODO: Skip next periods, if current period has a span > 1
                                for ( $i = 1; $i < $reservation->length; $i++ ) {
                                    $room_time_index++;
                                }

                                echo '
                                <form class="period reserved '.( $deletable ? ' deletable ' : '' ).' remove-style" style="grid-row: span '.$reservation->length.'" method="post">
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
                                <div class="modal" id="'.$thickbox_id.'" style="display:none;">
                                    <div class="simple-reservation-modal">
                                        <h4>Neue Reservierung</h4>

                                        <form method="post">
                                            <input type="hidden" name="action" value="add_reservation">
                                            <input type="hidden" name="room_id" value="'.$room->id.'">
                                            <input type="hidden" name="date" value="'.$day['date'].'">
                                            <input type="hidden" name="time_id" value="'.$room_time['id'].'">

                                            <div class="row">
                                                <p>Name</p>
                                                <input value="'.wp_get_current_user()->display_name.'" disabled type="text">
                                            </div>

                                            <div class="row">
                                                <p>Raum</p>
                                                <input value="'.$room->name.'" disabled type="text">
                                            </div>

                                            <div class="row">
                                                <p>Datum</p>
                                                <input value="'.$day['name'].', '.$day['date'].'" disabled type="text">
                                            </div>

                                            <div class="row">
                                                <p>Zeit</p>
                                                <input value="'.$room_time['name'].'" disabled type="text">
                                            </div>

                                            <div class="row">
                                                <p>Länge</p>
                                                <input name="length" type="number" value="1" min="1" max="4">
                                            </div>

                                            <div class="row">
                                                <p>Anmerkung</p>
                                                <input name="description" type="text">
                                            </div>

                                            <div class="button-wrapper">
                                                <button class="simple-reservation primary" type="submit">Speichern</button>
                                            </div>
                                        </form>
                                    </div>
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