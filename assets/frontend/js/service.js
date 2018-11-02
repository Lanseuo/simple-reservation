class SimpleReservationService {
    constructor(wpAPIBaseUrl) {
        this.wpAPIBaseUrl = wpAPIBaseUrl

        this.api = axios.create({
            baseURL: `${window.simpleReservationWpApiBaseUrl}simplereservation`,
            headers: {
                'X-WP-Nonce': window.simpleReservationNonce
            }
        })

        this.api.interceptors.response.use(response => {
            if (response.data.message) {
                simpleReservationStore.dispatch('showNotice', { message: response.data.message, type: 'success' })
            }

            return response
        }, e => {
            console.log(e)

            if (!e.response) {
                simpleReservationStore.dispatch('showNotice', { message: 'Es konnte keine Verbindung zum Server hergestellt werden', type: 'error' })
            } else if (e.response.data.message) {
                simpleReservationStore.dispatch('showNotice', { message: e.response.data.message, type: 'error' })
            } else {
                simpleReservationStore.dispatch('showNotice', { message: 'Es ist ein Fehler aufgetreten.', type: 'error' })
            }

            return Promise.reject(e)
        })


    }

    info() {
        return this.api.get('/info  ')
    }

    getRooms() {
        return this.api.get('rooms')
    }

    getReservations(roomId) {
        return this.api.get(`rooms/${roomId}/reservations`)
    }

    addReservation(roomId, date, timeId, userId, description, length) {
        return this.api.post(`rooms/${roomId}/reservations`, {
            date,
            time_id: timeId,
            user_id: userId,
            description,
            length
        })
    }

    deleteReservation(roomId, reservationId) {
        return this.api.delete(`rooms/${roomId}/reservations/${reservationId}`)
    }
}