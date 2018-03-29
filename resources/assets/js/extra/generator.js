const pitches = ['A#3', 'B3', 'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4', 'C5', 'C#5']

const intervals = {
    0: ['P1'],
    1: ['A1', 'm2'],
    2: ['M2'],
    3: ['m3'],
    4: ['M3'],
    5: ['P4'],
    6: ['A4', 'd5'],
    7: ['P5'],
    8: ['m6'],
    9: ['M6'],
    10: ['m7'],
    11: ['M7'],
    12: ['P8']
}

function generateIntervalSequence (output = true) {
    const levelRange = parseInt(document.getElementById('levelRange').value)
    const nNotes = document.getElementById('nNotes').value.split('+').map(val => parseInt(val.replace(/ /g, ''))).reduce((a, b) => a + b, 0)

    const sample = []
    const semitones = []

    const nPitches = pitches.length
    let pitch = pitches[Math.floor(Math.random() * nPitches)]
    sample.push(pitch)

    let pitchIndex = 0
    let topRange = 0
    let bottomRange = 0
    let rangeSum = 0
    let direction = ''
    let range = 0
    let nSemitones = 0
    let intervalIndex = 0

    for (let i = 1; i < nNotes; i++) {
        pitchIndex = pitches.indexOf(pitch)
        topRange = nPitches - pitchIndex - 1
        bottomRange = pitchIndex
        rangeSum = topRange + bottomRange
        direction = weightedRand({ 'down': bottomRange / rangeSum, 'up': topRange / rangeSum })
        range = direction === 'down' ? Math.min(levelRange, bottomRange) : Math.min(levelRange, topRange)
        nSemitones = Math.floor(Math.random() * (range + 1))
        intervalIndex = direction === 'down' ? (pitchIndex - nSemitones) : (pitchIndex + nSemitones)
        pitch = pitches[intervalIndex]
        if (i >= 2 && sample[i - 2] === sample[i - 1] && sample[i - 1] === pitch) {
            i--
            continue
        }
        sample.push(pitch)
        semitones.push(nSemitones)
    }

    const sampleIntervals = semitones.map(n => intervals[n])

    if (output) {
        document.getElementById('sequence').innerHTML = sample.join(', ')
        document.getElementById('intervals').innerHTML = sampleIntervals.join(' | ')
    } else {
        return {
            sample: sample,
            semitones: semitones
        }
    }
}

function generateBulk () { // eslint-disable-line no-unused-vars
    const nNotes = document.getElementById('nNotes').value.split('+').map(val => parseInt(val.replace(/ /g, ''))).reduce((a, b) => a + b, 0)
    const nSamples = parseInt(document.getElementById('nSamples').value)

    let result = null
    const noteStatistics = {}
    const intervalStatistics = {}

    for (let i = 0; i < pitches.length; i++) {
        noteStatistics[pitches[i]] = 0
    }

    for (let i = 0; i < Object.keys(intervals).length; i++) {
        intervalStatistics[i] = 0
    }

    for (let i = 0; i < nSamples; i++) {
        result = generateIntervalSequence(false)

        for (let j = 0; j < nNotes; j++) {
            let note = result.sample[j]
            noteStatistics[note]++
        }

        for (let j = 0; j < nNotes - 1; j++) {
            let nSemitones = result.semitones[j]
            intervalStatistics[nSemitones]++
        }
    }

    let noteStatisticsOutput = ''
    for (let i = 0; i < pitches.length; i++) {
        noteStatisticsOutput += '<div class="generator__container">'
        noteStatisticsOutput += '<label class="generator__label--statistics">' + pitches[i] + '</label>'
        noteStatisticsOutput += '<label class="generator__label--statistics">' + noteStatistics[pitches[i]] + '</label>'
        noteStatisticsOutput += '<label class="generator__label--statistics">' + (Math.round(noteStatistics[pitches[i]] / (nSamples * nNotes) * 10000) / 100) + ' %</label>'
        noteStatisticsOutput += '</div>'
    }

    let intervalStatisticsOutput = ''
    for (let i = 0; i < Object.keys(intervals).length; i++) {
        intervalStatisticsOutput += '<div class="generator__container">'
        intervalStatisticsOutput += '<label class="generator__label--statistics">' + intervals[i] + '</label>'
        intervalStatisticsOutput += '<label class="generator__label--statistics">' + intervalStatistics[i] + '</label>'
        intervalStatisticsOutput += '<label class="generator__label--statistics">' + Math.round((intervalStatistics[i] / (nSamples * (nNotes - 1)) * 10000) / 100) + ' %</label>'
        intervalStatisticsOutput += '</div>'
    }

    document.getElementById('noteStatistics').innerHTML = noteStatisticsOutput
    document.getElementById('intervalStatistics').innerHTML = intervalStatisticsOutput
}

function weightedRand (specification) {
    let i = 0
    let sum = 0
    let rand = Math.random()

    for (i in specification) {
        sum += specification[i]
        if (rand <= sum) {
            return i
        }
    }
}
