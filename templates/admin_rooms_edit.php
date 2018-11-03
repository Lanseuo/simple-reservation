<?php
    namespace Templates;
    use Inc\Pages\Admin\AdminCallbacks;
    $admin_callbacks = new AdminCallbacks();
    
    $action_type = $_POST['action'] == 'start_edit_room' ? 'edit' : 'add';

    switch ( $action_type ) {
        case 'edit':
            $room_id = $_POST['id'];
            $room = $admin_callbacks->get_room( $room_id );
            $room_name = $room->name;
            $room_description = $room->description;
            break;
        case 'add':
            $room_id = '';
            $room_name = '';
            $room_description = '';
    }
?>

<h2><?php echo ucfirst($action_type); ?> Room</h2>

<form method="post">
    <input type="hidden" name="action" value="<?php echo $action_type; ?>_room">
    <input type="hidden" name="id" value="<?php echo $room_id; ?>">
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