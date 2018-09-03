<?php
    namespace Templates;

    use Inc\Api\Callbacks\AdminCallbacks;;

    $admin_callbacks = new AdminCallbacks();
    $admin_callbacks->action();
?>

<div class="simple-reservation wrap">
    <h1>SimpleReservation Rooms</h1>
    <?php settings_errors(); ?>
    <?php
        if ( isset($_POST['action']) ) {
            switch ( $_POST['action'] ) {
                case 'start_add_room':
                    include ('admin_rooms_edit.php');
                    break;
                case 'start_edit_room':
                    include ('admin_rooms_edit.php');
                    break;
                default:
                    include ('admin_rooms_all.php');
            }
        } else {
            include ('admin_rooms_all.php');
        }
    ?>
</div>