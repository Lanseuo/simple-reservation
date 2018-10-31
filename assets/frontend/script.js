let app = new Vue({
    el: '#simple-reservation-app',

    data: {
        service: new SimpleReservationService(window.wpApiBaseUrl),
        loading: true,
        rooms: [],
        room: null,
    },

    created() {
        this.loading = true
        this.service.getRooms()
            .then(rooms => {
                this.rooms = rooms
                this.room = rooms[0]
                this.loading = false
            })
            .catch(e => {
                this.loading = false
            })
    },

    methods: {
        changeRoom(roomId) {
            this.room = this.rooms.filter(room => room.id == roomId)[0]
        }
    }
})

Vue.component('tabs', {
    template: `
        <div class="tabs">
            <li v-for="room in rooms" :class="{ active: roomId == room.id }" @click="$emit('changeroom', room.id)" :key="room.id">
                <a>{{ room.name }}</a>
            </li>
        </div>
    `,

    props: ['rooms', 'roomId']
})

Vue.component('room', {
    template: `
        <div class="room">
            <p>{{ room.description }}</p>

            <div class="week">
                <div class="header">Reservierungen</div>
                <week-grid :room="room" :service="service"></week-grid>
            </div>
        </div>
    `,

    props: ['room', 'service']
})

Vue.component('week-grid', {
    template: `
        <div class="week-grid">
            <p v-if="loading">Loading ...</p>
            <div class="day-topbar empty"></div>
            <div class="time-sidebar" v-for="time in times">
                <p><strong>{{ time.name }}</strong></p>
                <p>{{ time.description}}</p>
            </div>

            <template v-for="day in days">
                <div class="day-topbar"><strong>{{ day.weekday }}</strong></div>
                <period
                    v-for="time in times"
                    :day="day" :time="time"
                    :room="room"
                    :reservations="reservations"
                    :service="service"
                    :key="room.id + '-' + day.date + '-' + time.id"
                    @updatereservations="updateReservations"
                ></period>
            </template>
        </div>
    `,

    props: ['room', 'service'],

    data() {
        return {
            loading: false,
            times: [
                { id: 0, name: '1. Stunde', description: '8:05 - 8:50 Uhr' },
                { id: 1, name: '2. Stunde', description: '8:55 - 9:40 Uhr' },
                { id: 2, name: '3. Stunde', description: '10:00 - 10:45 Uhr' },
                { id: 3, name: '4. Stunde', description: '10:50 - 11:35 Uhr' },
                { id: 4, name: '5. Stunde', description: '11:45 - 12:30 Uhr' },
                { id: 5, name: '6. Stunde', description: '12:35 - 13:20 Uhr' },
                { id: 6, name: '7. Stunde', description: '14:10 - 14:55 Uhr' },
                { id: 7, name: '8. Stunde', description: '15.00 - 15:45 Uhr' },
                { id: 8, name: '9. Stunde', description: '15.50 - 16:35 Uhr' },
                { id: 9, name: '10. Stunde', description: '16.40 - 17:25 Uhr' }
            ],
            days: [
                { date: '20181029', weekday: 'Montag' },
                { date: '20181030', weekday: 'Dienstag' },
                { date: '20181031', weekday: 'Mittwoch' },
                { date: '20181101', weekday: 'Donnerstag' },
                { date: '20181102', weekday: 'Freitag' },
            ],
            reservations: []
        }
    },

    created() {
        this.service.getReservations(this.room.id)
            .then(reservations => {
                this.reservations = reservations
            })
            .catch(e => {
                console.error(e)
            })
    },

    methods: {
        updateReservations(reservations) {
            this.reservations = reservations
        }
    }
})

Vue.component('period', {
    template: `
        <form v-if="reservation" class="period reserved deletable remove-style" :style="'grid-row: span ' + reservation.length">
            <button>
                <div class="content">
                    <p><strong>{{ reservation.user }}</strong></p>
                    <p>{{ reservation.description }}</p>
                </div>
                <span class="delete-symbol dashicons dashicons-trash"></span>
            </button>
        </form>
        <a v-else class="period free thickbox" :href="'#TB_inline?width=600&height=550&inlineId=' + periodKey">
            <span class="add-symbol">+</span>

            <!-- Thickbox -->
            <div class="modal" :id="periodKey" style="display:none;">
                <div class="simple-reservation-modal">
                    <h4>Neue Reservierung</h4>

                    <div class="row">
                        <p>Name</p>
                        <input value="Replace with username" disabled type="text">
                    </div>

                    <div class="row">
                        <p>Raum</p>
                        <input :value="room.name" disabled type="text">
                    </div>

                    <div class="row">
                        <p>Datum</p>
                        <input :value="day.date" disabled type="text">
                    </div>

                    <div class="row">
                        <p>Zeit</p>
                        <input :value="time.name" disabled type="text">
                    </div>

                    <div class="row">
                        <p>Länge</p>
                        <input v-model="length" type="number" value="1" min="1" max="room.max_length">
                    </div>

                    <div class="row">
                        <p>Anmerkung</p>
                        <input v-model="description" type="text">
                    </div>

                    <div class="button-wrapper">
                        <button class="simple-reservation primary" @click="addReservation">Speichern</button>
                    </div>
                </div>
            </div>
        </a>
    `,

    props: ['day', 'time', 'room', 'reservations', 'service'],

    data() {
        return {
            description: '',
            length: 1
        }
    },

    methods: {
        addReservation() {
            this.service.addReservation(this.room.id, this.day.date, this.time.id, this.description, this.length)
                .then(response => {
                    this.$emit('updatereservations', response.data.reservations)
                })
                .catch(e => {
                    console.error(e)
                })
        }
    },

    computed: {
        reservation() {
            return this.reservations.filter(reservation => (
                reservation.date == this.day.date && reservation.time_id == this.time.id
            ))[0]
        },

        periodKey() {
            return this.$vnode.key
        }
    }
})