import '../bootstrap'

import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        me: null,
        schools: null,
        grades: null,
        midi: null
    },
    getters: {
        me: state => state.me,
        schools: state => state.schools,
        grades: state => state.grades,
        midi: state => state.midi
    },
    mutations: {
        setMe: (state, me) => {
            state.me = me
        },
        setSchools: (state, schools) => {
            state.schools = schools
        },
        setGrades: (state, grades) => {
            state.grades = grades
        },
        setMidi: (state, midi) => {
            state.midi = midi
        }
    },
    actions: {
        fetchMe ({ commit, state }) {
            return new Promise((resolve, reject) => {
                if (state.me) {
                    resolve(state.me)
                } else {
                    axios.get('/api/me')
                        .then(response => {
                            commit('setMe', response.data)
                            resolve(state.me)
                        })
                        .catch(error => {
                            console.log(error)
                            reject(error)
                        })
                }
            })
        },
        storeMe ({ dispatch, commit, state }, data) {
            const id = state.me.id

            return new Promise((resolve, reject) => {
                axios.put('/api/users/' + id, data)
                    .then(response => {
                        dispatch('fetchMe')
                        resolve()
                    })
                    .catch(error => {
                        console.log(error)
                        reject(error)
                    })
            })
        },
        fetchUser ({ state }, id) {
            return new Promise((resolve, reject) => {
                if (state.me && state.me.id === id) {
                    resolve(state.me)
                } else {
                    axios.get('/api/users/' + id)
                        .then(response => {
                            resolve(response.data)
                            resolve(state.user)
                        })
                        .catch(error => {
                            console.log(error)
                            reject(error)
                        })
                }
            })
        },
        fetchSchools ({ commit, state }) {
            return new Promise((resolve, reject) => {
                if (state.schools) {
                    resolve(state.schools)
                } else {
                    axios.get('/api/schools', { params: { per_page: 0 } })
                        .then(response => {
                            commit('setSchools', response.data)
                            resolve(state.schools)
                        })
                        .catch(error => {
                            console.log(error)
                            reject(error)
                        })
                }
            })
        },
        fetchGrades ({ commit, state }) {
            return new Promise((resolve, reject) => {
                if (state.grades) {
                    resolve(state.grades)
                } else {
                    axios.get('/api/grades', { params: { per_page: 0 } })
                        .then(response => {
                            commit('setGrades', response.data)
                            resolve(state.grades)
                        })
                        .catch(error => {
                            console.log(error)
                            reject(error)
                        })
                }
            })
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
