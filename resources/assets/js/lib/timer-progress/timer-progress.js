/**
 * @file        SVG-based native JS circular timer progress bar.
 * @author      Ziga Vucko 2017
 */

/**
 * Creates an SVG-based circular timer progress bar, which functions like an actual timer.
 * @param {!Object.<string, any>} options   an object containing attributes of timer progress bar:
 *                                              'container': an SVG object, which functions as a placeholder for other SVG elements with dimensions already set (obligatory),
 *                                              'stroke-width': width of the timer progress bar in px (optional),
 *                                              'color-container': background color of the placeholder SVG element (optional; default: color inherited from parent),
 *                                              'color-circle': stroke color of the underlying circle SVG element (optional; default: lightgray),
 *                                              'color-path': stroke color of the path SVG element (optional; default: black),
 *                                              'color-text': fill color of the text SVG element (optional; default: black),
 *                                              'color-alert': fill color of the text SVG element when time is under 3 seconds (optional; default: red),
 *                                              'font-size': size of the text font in px (optional),
 *                                              'font-family': font family of the text font (optional; default: sans-serif)
 *
 * @constructor
 */
function TimerProgress (options) {
    this.container = new SVGElement(options['container'], {
        'style': 'background-color: ' + (options['color-container'] || 'inherit')
    })

    this.strokeWidth = options['stroke-width']

    this.circle = new SVGElement('circle', {
        'fill': 'none',
        'stroke': options['color-circle'] || 'lightgray'
    })
    this.container.appendChild(this.circle)

    this.path = new SVGElement('path', {
        'fill': 'none',
        'stroke': options['color-path'] || 'black',
        'd': ''
    })
    this.container.appendChild(this.path)

    this.fontSize = options['font-size'] || 0
    this.colorText = options['color-text'] || 'black'
    this.text = new SVGElement('text', {
        'fill': this.colorText,
        'text-anchor': 'middle',
        'font-family': options['font-family'] || 'sans-serif'
    })
    this.container.appendChild(this.text)
    this.colorAlert = options['color-alert'] || 'red'
}

TimerProgress.prototype.polarToCartesian = function (cx, cy, r, degrees) {
    const radians = (degrees - 90) * Math.PI / 180.0
    return {
        x: cx + (r * Math.cos(radians)),
        y: cy + (r * Math.sin(radians))
    }
}

TimerProgress.prototype.describeArc = function (x, y, r, startAngle, endAngle) {
    var start = this.polarToCartesian(x, y, r, endAngle)
    var end = this.polarToCartesian(x, y, r, startAngle)
    var largeArcSweep = endAngle - startAngle <= 180 ? '0' : '1'
    return [
        'M', start.x, start.y,
        'A', r, r, 0, largeArcSweep, 0, end.x, end.y
    ].join(' ')
}

TimerProgress.prototype.secondsLeft = function (time, degrees) {
    return time * (degrees / 360) / 1000
}

TimerProgress.prototype.formatTime = function (seconds, nDecimals) {
    return seconds.toFixed(nDecimals)
}

TimerProgress.prototype.frame = function (context) {
    if (context.degrees === 0) {
        if (context.infinite) {
            context.degrees = 360
        } else {
            clearInterval(context.id)
            context.started = false
            context.running = false
        }
    } else {
        context.degrees--

        let d = 0
        if (context.infinite) {
            d = context.describeArc(context.cx, context.cy, context.r, context.degrees - 75, context.degrees)
        } else {
            d = context.describeArc(context.cx, context.cy, context.r, context.degrees, 360)
        }
        context.path.set('d', d)
    }

    const seconds = context.secondsLeft(context.time, context.degrees)
    context.text.element().textContent = context.formatTime(seconds, context.nDecimals)

    if (context.alertTime > 0 && seconds < context.alertTime) {
        context.text.set('fill', context.colorAlert)
    }
}

/**
 * Runs the timer progress.
 * @param {number|string} time      number of milliseconds defining the timer duration (if 'inf', then the timer will be executed for indefinite duration; compulsory)
 * @param {number} alertTime        number of milliseconds defining how much time before the end of the timer should the text in the middle of the circle change its color (optional; default: 0)
 * @param {boolean} displayText     flag indicating whether the text in the middle of the circle representing the time left (in seconds) is displayed (optional; default: true)
 * @param {number} nDecimals        number of decimals of the text representing the time left (optional; default: 0)
 * @param {boolean} displayCircle   flag indicating whether the circle indicating progress is displayed (optional; default: true)
 */
TimerProgress.prototype.run = function (time, alertTime = 0, displayText = true, nDecimals = 0, displayCircle = true) {
    var width = this.container.element().clientWidth || this.container.element().parentNode.clientWidth
    var height = this.container.element().clientHeight || this.container.element().parentNode.clientHeight
    var shorter =  width > height ? height : width
    this.cx = width / 2
    this.cy = height / 2
    this.r = (shorter / 2) * 0.85

    this.circle.set('cx', this.cx)
    this.circle.set('cy', this.cy)
    this.circle.set('r', this.r)
    this.circle.set('stroke-width', this.strokeWidth || shorter / 10)

    this.path.set('stroke-width', (this.strokeWidth || shorter / 10) + 1)

    this.text.set('x', width / 2)
    this.text.set('y', (height / 2) * 1.145)
    this.text.set('font-size', (this.fontSize > 0 ? this.fontSize : shorter / 6) + 'px')

    this.infinite = time === 'inf'

    if (!displayCircle) {
        this.circle.set('visibility', 'hidden')
        this.path.set('visibility', 'hidden')
    }

    if (this.infinite || !displayText) {
        this.text.set('visibility', 'hidden')
    }

    this.alertTime = alertTime / 1000
    this.nDecimals = nDecimals

    this.time = this.infinite ? 3000 : time
    this.degrees = 360
    this.speed = this.time / this.degrees

    this.started = true
    this.running = true
    this.id = setInterval(this.frame, this.speed, this)
}

/**
 * Tells whether the timer progress is already running.
 */
TimerProgress.prototype.isRunning = function () {
    return this.started
}

/**
 * Pauses the timer progress.
 */
TimerProgress.prototype.pause = function () {
    this.running = false
    clearInterval(this.id)
}

/**
 * Tells whether the timer progress is paused.
 */
TimerProgress.prototype.isPaused = function () {
    return !this.running
}

/**
 * Resumes the timer progress.
 */
TimerProgress.prototype.resume = function () {
    this.running = true
    this.id = setInterval(this.frame, this.speed, this)
}

/**
 * Resets the timer progress.
 */
TimerProgress.prototype.reset = function () {
    this.running = false
    clearInterval(this.id)

    this.text.set('fill', this.colorText)
    this.degrees = 360
    this.running = true
    this.started = true
    this.id = setInterval(this.frame, this.speed, this)
}

/**
 * Helper class for creating and manipulating SVG elements.
 * @param {string|Object} element           SVG element type (string) or an existing SVG element (object)
 * @param {!Object.<string, any>} options   an object containing attributes of the SVG element
 * @constructor
 */
function SVGElement(element, options) {
    if (typeof(element) === 'string') {
        this.create(element)
    } else if (typeof(element) === 'object') {
        this.wrap(element)
    }
    this.setBulk(options || null)
}

SVGElement.prototype.create = function (element) {
    this.elem = document.createElementNS("http://www.w3.org/2000/svg", element)
}

SVGElement.prototype.wrap = function (element) {
    this.elem = element
}

SVGElement.prototype.get = function (key) {
    return this.elem.getAttributeNS(null, key)
}

SVGElement.prototype.set = function (key, val) {
    this.elem.setAttributeNS(null, key, val)
}

SVGElement.prototype.setBulk = function (options) {
    if (options !== null) {
        for (var key in options) {
            if (options.hasOwnProperty(key))
                this.set(key, options[key])
        }
    }
}

SVGElement.prototype.addClass = function (cls) {
    this.elem.classList.add(cls)
}

SVGElement.prototype.removeClass = function (cls) {
    this.elem.classList.remove(cls)
}

SVGElement.prototype.addEventListener = function (type, func) {
    this.elem.addEventListener(type, func)
}

SVGElement.prototype.removeEventListener = function (type, func) {
    this.elem.removeEventListener(type, func)
}

SVGElement.prototype.appendChild = function (child) {
    this.elem.appendChild(child.element())
}

SVGElement.prototype.removeChild = function (child) {
    this.elem.removeChild(child.element())
}

SVGElement.prototype.element = function () {
    return this.elem
}
