<form method="post" class="text-right margin-bottom">
    <input type="hidden" name="add_room">
    <?php submit_button( 'Add new room', 'primary', 'submit', false ); ?>
</form>

<table class="big">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>

<?php
    global $wpdb;

    $rooms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms", OBJECT );

    foreach ($rooms as $room) {
        echo '
            <tr>
                <td>'.$room->name.'</td>
                <td>'.$room->description.'</td>
                <td class="text-center">
                    <form method="post" style="display: inline-block">
                        <input type="hidden" name="edit_room" value="'.$room->id.'">';
        submit_button( 'Edit', 'primary small', 'submit', false );
        echo '
                    </form>
                    <form method="post" style="display: inline-block">
                        <input type="hidden" name="delete_room" value="'.$room->id.'">';
        submit_button( 'Delete', 'delete small', 'submit', false );
        echo '
                    </form>
                </td>
            </tr>
        ';
    }
?>

</table>