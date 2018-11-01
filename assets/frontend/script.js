Vue.component('notice', {
    template: `
        <div class="notice" :class="$store.state.notice.type" v-show="$store.state.notice.visible">
            <p>{{ $store.state.notice.message }}</p>
            <span @click="close" class="dashicons dashicons-no-alt"></span>
        </div>
    `,

    methods: {
        close() {
            this.$store.dispatch('hideNotice')
        }
    }
})

let app = new Vue({
    el: '#simple-reservation-app',

    data: {
        loading: true,
        rooms: [],
        room: null,
    },

    store: simpleReservationStore,

    created() {
        this.loading = true
        this.$store.state.service.getRooms()
            .then(response => {
                this.rooms = response.data.rooms
                this.room = this.rooms[0]
                this.loading = false
            })
            .catch(e => {
                this.loading = false
            })

        this.$store.state.service.info()
            .then(response => {
                this.$store.dispatch('setUser', { id: response.data.user_id, username: response.data.username })
            })
            .catch(e => {

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
                <div class="header">
                    <span @click="previousWeek" class="dashicons dashicons-arrow-left-alt2"></span>
                    <p>Reservierungen {{ startDayOfWeek.getDate() }}</p>
                    <span @click="nextWeek" class="dashicons dashicons-arrow-right-alt2"></span>
                </div>
                <week-grid :room="room" :start-day-of-week="startDayOfWeek"></week-grid>
            </div>
        </div>
    `,

    props: ['room'],

    data() {
        return {
            startDayOfWeek: null
        }
    },

    created() {
        this.startDayOfWeek = new Date();
        this.startDayOfWeek.setDate(this.startDayOfWeek.getDate() - (this.startDayOfWeek.getDay() + 6) % 7)
        this.startDayOfWeek = new Date(this.startDayOfWeek.getTime())
    },

    methods: {
        previousWeek() {
            this.startDayOfWeek.setDate(this.startDayOfWeek.getDate() - 7)

            // Create a copy
            this.startDayOfWeek = new Date(this.startDayOfWeek.getTime())
        },
        nextWeek() {
            this.startDayOfWeek.setDate(this.startDayOfWeek.getDate() + 7)

            // Create a copy
            this.startDayOfWeek = new Date(this.startDayOfWeek.getTime())
        }
    }
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
                <div class="day-topbar">
                    <p><strong>{{ day.weekday }}</strong></p>
                    <p>{{ day.date | beautifulDate }}</p>
                </div>
                <period
                    v-for="time in times"
                    :day="day"
                    :time="time"
                    :room="room"
                    :reservations="reservations"
                    :key="room.id + '-' + day.date + '-' + time.id"
                    @updatereservations="updateReservations"
                ></period>
            </template>
        </div>
    `,

    props: ['room', 'startDayOfWeek'],

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
            reservations: []
        }
    },

    created() {
        this.$store.state.service.getReservations(this.room.id)
            .then(response => {
                this.updateReservations(response.data.reservations)
            })
            .catch(e => {
                console.error(e)
            })
    },

    methods: {
        updateReservations(reservations) {
            this.reservations = reservations.map(reservation => ({
                id: reservation.id,
                date: reservation.date,
                description: reservation.description,
                userId: reservation.user_id,
                roomId: reservation.room_id,
                timeId: parseInt(reservation.time_id),
                length: parseInt(reservation.length),
                user: reservation.user
            }))
        },
    },

    computed: {
        days() {
            function pad(n) {
                n = n + ''
                return n.length >= 2 ? n : new Array(2 - n.length + 1).join('0') + n
            }

            let result = []

            function appendDate(d) {
                let weekdays = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag']

                result.push({
                    date: `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}`,
                    weekday: weekdays[d.getDay()]
                })
            }

            // First day of week
            let currentDay = new Date(this.startDayOfWeek.getTime())

            appendDate(currentDay)

            for (let i = 0; i < 4; i++) {
                currentDay.setDate(currentDay.getDate() + 1)
                appendDate(currentDay)
            }

            return result
        }
    },

    filters: {
        beautifulDate(date) {
            return `${date.slice(6, 8)}.${date.slice(4, 6)}.${date.slice(0, 4)}`
        }
    }
})

Vue.component('period', {
    template: `
        <div v-if="reservation && showPeriod" class="period reserved deletable remove-style" @click="deleteReservation" :style="'grid-row: span ' + reservation.length">
            <div class="content">
                <p><strong>{{ reservation.user }}</strong></p>
                <p>{{ reservation.description }}</p>
            </div>
            <span class="delete-symbol dashicons dashicons-trash"></span>
        </div>
        <a v-else-if="showPeriod" class="period free thickbox" :href="'#TB_inline?width=600&height=550&inlineId=' + periodKey">
            <span class="add-symbol">+</span>
            <!-- Thickbox -->
            <div class="modal" :id="periodKey" style="display:none;">
                <div class="simple-reservation-modal">
                    <h4>Neue Reservierung</h4>

                    <div class="row">
                        <p>Name</p>
                        <input :value="$store.state.user.username" disabled type="text">
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
                        <input v-model="length" type="number" value="1" min="1" :max="maxLength">
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

    props: ['day', 'time', 'room', 'reservations'],

    data() {
        return {
            description: '',
            length: 1
        }
    },

    methods: {
        addReservation() {
            this.$store.state.service.addReservation(this.room.id, this.day.date, this.time.id, this.description, this.length)
                .then(response => {
                    this.$emit('updatereservations', response.data.reservations)

                    // Close modal
                    document.getElementById('TB_closeWindowButton').click()
                })
                .catch(e => {
                    // Close modal
                    document.getElementById('TB_closeWindowButton').click()
                })
        },

        deleteReservation() {
            this.$store.state.service.deleteReservation(this.room.id, this.reservation.id)
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
                reservation.date == this.day.date && reservation.timeId == this.time.id
            ))[0]
        },

        showPeriod() {
            // Don't show period if period before has span > 1
            let reservationsBefore = this.reservations.filter(reservation => (
                reservation.date == this.day.date && reservation.timeId < this.time.id
            ))

            let overlappingReservations = reservationsBefore.filter(reservation => {
                return reservation.timeId + reservation.length > this.time.id
            })

            return overlappingReservations.length == 0
        },

        periodKey() {
            return this.$vnode.key
        },

        maxLength() {
            let maxLengths = []

            let reservationsAfter = this.reservations.filter(reservation => (
                reservation.date == this.day.date && reservation.timeId > this.time.id
            ))
            reservationsAfter.forEach(reservation => {
                maxLengths.push(reservation.timeId - this.time.id)
            })

            // Until end of day
            maxLengths.push(10 - this.time.id)

            return Math.min(...maxLengths)
        }
    }
})