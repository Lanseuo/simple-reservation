<div class="simple-reservation wrap">
    <h1>SimpleReservation Rooms</h1>
    <?php settings_errors(); ?>
    <?php
        global $wpdb;
    
        if (isset($_POST['edit_room']) || isset($_POST['add_room'])) {
            include ('admin_rooms_edit.php');
        } else if (isset($_POST['edit_room_submit'])) {
            $result = $wpdb->update(
                $wpdb->prefix.'simple_reservation_rooms',
                [
                    'name'        => $_POST['name'],
                    'description' => $_POST['description']
                ],
                [ 'id' => 1 ],
                [ '%s', '%s' ],
                [ '%d' ]
            );
            echo $result ? 'Changed room successfully' : 'Failure';
            include ('admin_rooms_all.php');

        } else if (isset($_POST['add_room_submit'])) {
            $result = $wpdb->insert(
                $wpdb->prefix.'simple_reservation_rooms',
                [
                    'name'        => $_POST['name'],
                    'description' => $_POST['description']
                ],
                [ '%s', '%s']
            );

            echo $result ? 'Added room successfully' : 'Failure';
            include ('admin_rooms_all.php');

        } else if (isset($_POST['delete_room'])) {
            $result = $wpdb->delete(
                $wpdb->prefix.'simple_reservation_rooms',
                [ 'id' => $_POST['delete_room'] ],
                [ '%d']
            );
            echo $result ? 'Deleted room successfully' : 'Failure';
            include ('admin_rooms_all.php');
            
        } else {
            include ('admin_rooms_all.php');
        }
    ?>
</div>