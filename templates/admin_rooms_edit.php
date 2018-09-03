<?php
    global $wpdb;

    if ( isset( $_POST['edit_room'] ) ) {
        $room_id = $_POST['edit_room'];
        $rooms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms WHERE id = {$room_id}", OBJECT );
        $room = $rooms[0];
        $room_name = $room->name;
        $room_description = $room->description;
    } else if ( isset( $_POST['add_room'] ) ) {
        $room_name = '';
        $room_description = '';
    }
?>

<h2><?php echo isset( $_POST['edit_room'] ) ? 'Edit' : 'Add'; ?> Room</h2>

<form method="post">
    <input type="hidden" name="<?php echo isset( $_POST['edit_room'] ) ? 'edit' : 'add'; ?>_room_submit" value="<?php echo $_POST['edit_room']; ?>">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Name</th>
                <td>
                    <input class="regular-text" name="name" value="<?php echo $room_name; ?>" placeholder="Name of the Room" type="text">
                </td>
            </tr>
            <tr>
                <th scope="row">Description</th>
                <td>
                    <input class="regular-text" name="description" value="<?php echo $room_description; ?>" placeholder="Description of the Room" type="text">
                </td>
            </tr>
        </tbody>
    </table>

    <?php submit_button(); ?>
</form>