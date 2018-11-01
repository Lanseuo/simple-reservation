class SimpleReservationService {
    constructor(wpAPIBaseUrl) {
        this.wpAPIBaseUrl = wpAPIBaseUrl

        this.api = axios.create({
            baseURL: `${window.simpleReservationWpApiBaseUrl}simplereservation`,
            headers: {
                'X-WP-Nonce': window.simpleReservationNonce
            }
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

    addReservation(roomId, date, timeId, description, length) {
        return this.api.post(`rooms/${roomId}/reservations`, {
            date,
            time_id: timeId,
            description,
            length
        })
    }

    deleteReservation(roomId, reservationId) {
        return this.api.delete(`rooms/${roomId}/reservations/${reservationId}`)
    }
}