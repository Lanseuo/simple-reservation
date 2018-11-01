const simpleReservationStore = new Vuex.Store({
    state: {
        service: new SimpleReservationService(window.wpApiBaseUrl),
        user: {
            id: null,
            username: null
        }
    },

    mutations: {
        setUser(state, user) {
            state.user = user
        }
    },

    actions: {
        setUser({ commit }, user) {
            commit('setUser', user)
        }
    }
})