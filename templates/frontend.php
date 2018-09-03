<?php
    require_once( plugin_dir_path( dirname( __FILE__, 2 ) )."simple-reservation/inc/Api/Callbacks/FrontendCallbacks.php" );
    $frontend_callbacks = new FrontendCallbacks();

    $rooms = $frontend_callbacks->get_rooms();

    $days_of_week = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
    $days = [
        [ 'name' => $days_of_week[date('w')], 'date' => date('d.m.Y') ],
        [ 'name' => $days_of_week[date('w', strtotime('+1 day'))], 'date' =>  date('d.m.Y', strtotime('+1 day')) ],
        [ 'name' => $days_of_week[date('w', strtotime('+2 day'))], 'date' =>  date('d.m.Y', strtotime('+2 day')) ],
        [ 'name' => $days_of_week[date('w', strtotime('+3 day'))], 'date' =>  date('d.m.Y', strtotime('+3 day')) ],
        [ 'name' => $days_of_week[date('w', strtotime('+4 day'))], 'date' =>  date('d.m.Y', strtotime('+4 day')) ]
    ];

    // TODO
    $room_times = [
        [ 'name' => '1. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '2. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '3. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '4. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '5. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '6. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '7. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '8. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '9. Stunde', 'description' => '8.05 - 8:55 Uhr' ],
        [ 'name' => '10. Stunde', 'description' => '8.05 - 8:55 Uhr' ]
    ];
?>
<div class="simple-reservation">
    <?php foreach ($rooms as $room) { ?>
        <h2><?php echo $room->name; ?></h2>
        <p><?php echo $room->description; ?></p>
    
        <div class="week">
            <div class="header">Week 1, 2018</div>
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
                    <?php foreach ( $days as $day ) { ?>
                        <div class="period free">
                            <span class="plus-symbol">+</span>
                        </div>
                        <!-- <div class="period reserved">
                            <p><strong><php echo 'Name' ?></strong></p>
                            <p><php echo 'Description' ?></p>
                        </div> -->
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>