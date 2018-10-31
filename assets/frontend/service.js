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

    async getRooms() {
        try {
            let response = await this.api.get('rooms')
            return response.data.rooms

        } catch (e) {
            console.error(e)
        }
    }

    async getReservations(roomId) {
        try {
            let response = await this.api.get(`reservations/${roomId}`)
            return response.data.reservations

        } catch (e) {
            console.error(e)
        }
    }

    async addReservation(roomId, date, timeId, description, length) {
        try {
            let response = await this.api.post('reservations', {
                room_id: roomId,
                date,
                time_id: timeId,
                description,
                length
            })
            return response

        } catch (e) {
            console.error(e)
        }
    }
}