const simpleReservationStore = new Vuex.Store({
    state: {
        service: new SimpleReservationService(window.wpApiBaseUrl),
        info: {},
        notice: {
            visible: false,
            message: '',
            type: null
        }
    },

    mutations: {
        setInfo(state, info) {
            state.info = info
        },
        setNotice(state, notice) {
            state.notice = notice
        }
    },

    actions: {
        setInfo({ commit }, info) {
            commit('setInfo', info)
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