import '../bootstrap'

import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        user: null,
        settings: null,
        midi: null
    },
    getters: {
        user: state => state.user,
        settings: state => state.settings,
        midi: state => state.midi
    },
    mutations: {
        setUser: (state, user) => {
            state.user = user
        },
        setMidi: (state, midi) => {
            state.midi = midi
        }
    },
    actions: {
        fetchUser ({ commit, state }) {
            if (!state.user) {
                axios.get('/api/me')
                    .then(response => {
                        commit('setUser', response.data)
                    })
                    .catch(error => console.log(error))
            }
        },
        setupMidi ({ commit, state }) {
            return new Promise((resolve, reject) => {
                if (state.midi) {
                    resolve()
                }
                MIDI.loadPlugin({
                    soundfontUrl: '/soundfonts/',
                    instruments: ['acoustic_grand_piano'],
                    onsuccess: () => {
                        MIDI.setVolume(0, 127)
                        MIDI.programChange(0, MIDI.GM.byName['acoustic_grand_piano'].number)
                        commit('setMidi', MIDI)
                        resolve()
                    }
                })
            })
        }
    }
})
