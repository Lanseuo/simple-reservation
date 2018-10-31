<?php
    namespace Templates;
    add_thickbox();
?>
<div class="simple-reservation">
    <div id="simple-reservation-app">
        <tabs :rooms="rooms" :room-id="room.id" @changeroom="changeRoom"></tabs>
        <p v-show="loading">Loading ...</p>
        <room :room="room" :service="service"></room>
    </div>
</div>

<script>
    window.simpleReservationWpApiBaseUrl = '<?php echo get_rest_url(); ?>';
    window.simpleReservationNonce = '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
</script>