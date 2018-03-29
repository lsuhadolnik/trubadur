<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>GENERATOR ZAPOREDIJ INTERVALOV</title>

        <!-- Scripts -->
        <script src="{{ asset('js/extra/generator.js') }}" type="text/javascript"></script>

        <!-- Style -->
        <style lang="css">
            body { font-family: Calibri; }

            .generator__container { display: flex; }

            .generator__label {
                width       : 200px;
                font-weight : bold;
            }

            .generator__label--extra { margin-left: 10px; }

            .generator__label--statistics { width: 100px; }

            .generator__label--statistics-header {
                border-bottom : 1px solid black;
                font-style    : italic;
            }
        </style>
    </head>
    <body>
        <h1>GENERATOR ZAPOREDIJ INTERVALOV</h1>
        <hr/>
        <br/>
        <div>
            <div class="generator__container">
                <label for="levelRange" class="generator__label">Razpon: </label>
                <input type="text" id="levelRange" value="5"/>
                <label class="generator__label--extra">(5 za 1. letnike, 12 za 2. letnike)</label>
            </div>
            <div class="generator__container">
                <label for="nNotes" class="generator__label">Št. not: </label>
                <input type="text" id="nNotes" value="1+3"/>
                <label class="generator__label--extra">(od 1+3 do 1+5)</label>
            </div>
            <br/>
            <button onclick="generateIntervalSequence()">Generiraj zaporedje</button>
            <br/><br/>
            <div class="generator__container">
                <label for="sequence" class="generator__label">Zaporedje not:</label>
                <div id="sequence"></div>
            </div>
            <div class="generator__container">
                <label for="intervals" class="generator__label">Intervali:</label>
                <div id="intervals"></div>
            </div>
        </div>
        <br/>
        <hr/>
        <br/>
        <div>
            <div class="generator__container">
                <label for="nSamples" class="generator__label">Št. generiranih zaporedij: </label>
                <input type="text" id="nSamples" value="10000"/>
            </div>
            <br/>
            <button onclick="generateBulk()">Generiraj zaporedja (statistika)</button>
        </div>
        <br/>
        <label class="generator__label">Statistika not:</label>
        <div class="generator__container">
            <label class="generator__label--statistics generator__label--statistics-header">Nota</label>
            <label class="generator__label--statistics generator__label--statistics-header">Število</label>
            <label class="generator__label--statistics generator__label--statistics-header">Delež</label>
        </div>
        <div id="noteStatistics"></div>
        <br/>
        <label class="generator__label">Statistika intervalov:</label>
        <div class="generator__container">
            <label class="generator__label--statistics generator__label--statistics-header">Interval</label>
            <label class="generator__label--statistics generator__label--statistics-header">Število</label>
            <label class="generator__label--statistics generator__label--statistics-header">Delež</label>
        </div>
        <div id="intervalStatistics"></div>
    </body>
</html>
