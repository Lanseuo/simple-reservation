const simpleReservationStore = new Vuex.Store({
    state: {
        service: new SimpleReservationService(window.wpApiBaseUrl),
        user: {
            id: null,
            username: null
        },
        notice: {
            visible: false,
            message: '',
            type: null
        }
    },

    mutations: {
        setUser(state, user) {
            state.user = user
        },
        setNotice(state, notice) {
            state.notice = notice
        }
    },

    actions: {
        setUser({ commit }, user) {
            commit('setUser', user)
        },
        showNotice({ commit }, notice) {
            commit('setNotice', {
                visible: true,
                ...notice
            })
        },
        hideNotice({ commit }) {
            commit('setNotice', {
                visible: false,
                message: '',
                type: null
            })
        }
    }
})