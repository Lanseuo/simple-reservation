<?php
    namespace Templates;
    add_thickbox();
?>
<div class="simple-reservation">
    <div id="simple-reservation-app">
        <notice></notice>
        <spinner v-if="loading"></spinner>
        <div v-else>
            <tabs :rooms="rooms" :room-id="room.id" @changeroom="changeRoom"></tabs>
            <room :room="room" :key="room.id"></room>
        </div>
    </div>
</div>

<script>
    window.simpleReservationWpApiBaseUrl = '<?php echo get_rest_url(); ?>';
    window.simpleReservationNonce = '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
</script>