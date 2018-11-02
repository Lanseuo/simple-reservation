function simpleReservationGetReservation(reservations, date, timeId) {
    let weekday = simpleReservationToJSDate(date).getDay() - 1  // 0 is Monday

    return reservations.filter(reservation => (
        reservation.timeId == timeId && (
            (reservation.repeatWeekly && weekday == reservation.repeatWeekday)
            || (!reservation.repeatWeekly && reservation.date == date)
        )
    ))[0]
}

function simpleReservationToJSDate(date) {
    return new Date(date.slice(0, 4), date.slice(4, 6) - 1, date.slice(6, 8))
}