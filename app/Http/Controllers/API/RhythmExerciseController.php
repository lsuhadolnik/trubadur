<?php

namespace App\Http\Controllers\API;

use App\RhythmExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\BarInfo;
use App\RhythmBar;
use App\RhythmExerciseBar;
use App\RhythmDifficulty;
use App\RhythmFeatureOccurrence;

use App\Http\Controllers\Utils\Generation\ModuleLoader;

class RhythmExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function show(RhythmExercise $rhythmExercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function edit(RhythmExercise $rhythmExercise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RhythmExercise $rhythmExercise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function destroy(RhythmExercise $rhythmExercise)
    {
        //
    }

    public static function resolve(int $id){

        $ex = RhythmExercise::find($id);
        $bars = $ex->bars->all();

        $bar_info_info = BarInfo::find($ex->bar_info_id);
        $bar_info = json_decode($bar_info_info->bar_info);

        // split bar jsons with {type: 'bar'}
        $notes = json_decode($bars[0]->content, true);
        for($i = 1; $i < count($bars); $i++){
            $notes = array_merge($notes, json_decode($bars[$i]->content, true));
        }

        // return the exercise
        return array(
            "id" => $ex->id,
            "BPM" => $ex->BPM,
            "name" => $ex->name,
            "timeSignature" => $bar_info,
            "notes" => $notes
        );

    }


    /*
        Returns time_signature bar duration in number of quarter notes
    */
    private function getBarInfoLength(&$ts){

        if(isset($ts->d) && isset($ts->n)){
            return (4/$ts->d) * $ts->n;
        }

        return (4/$ts->base_note) * $ts->num_beats;
    }

    private function chooseFeatureBar($feature, $spaceLeft) {
        $coll = DB::select("SELECT 
            b.id as id, 
            o.bar_probability as prob, 
            b.length as length,
            b.content as content 
        from rhythm_bar_occurrences o 
            JOIN rhythm_bars b on b.id = o.rhythm_bar_id 
        
        WHERE (b.cross_bar = 0 OR b.cross_bar IS NULL) AND o.rhythm_feature_id = :fid AND b.length <= :len", ['fid' => $feature->id, 'len' => $spaceLeft]);

        return $this->weightedRandomSelector($coll, function($b) { return $b->prob; });
    }

    private function chooseFeature(&$allF, $spaceLeft) {

        // Filtriraj tiste, kjer je minLength manjši od spaceLeft
        $available = array_filter($allF, function($el) use (&$spaceLeft){ 
            return $el->minBarLength <= $spaceLeft; 
        });

        return $this->weightedRandomSelector($available, function($c) { return $c->id; });
    }

    private function getFirstFreeBar($spaceNeeded, &$lengths, $currentBar) {
        
        $i = $currentBar;
        for($v = 0; $v < count($lengths); $v++){
            if($lengths[$i] >= $spaceNeeded){
                return $i;
            }
            $i = $this->incrementWrap($i, count($lengths));
        }

        return -1;

    }

    private function filterFeatures(&$features){
        $res = (object) [
            'obligatory' => [],
            'other' => []
        ];
        
        foreach($features as $feature){
            if(isset($feature->min) && $feature->min > 0){
                $res->obligatory[] = $feature;
            }else {
                $res->other[] = $feature;
            }
        }

        shuffle($res->obligatory);

        return $res;
    }

    private function incrementWrap($val, $max, $min = 0){
        if($val >= $max){
            return $min;
        }
        return $val + 1;
    }

    private function incrementArrayValue(&$arr, $idx) {
        if(!isset($arr[$idx])){
            $arr[$idx] = 0; 
        }

        $arr[$idx]++;
    }

    private function getFeaturesForLevelAndBar($level, $bar_info_id){

        // - Prenesi vse pojavitve značilnosti (skupaj z značilnostmi in minimalnimi dolžinami taktov), ki so primerne za ta level in BarInfo
        $features = DB::select("SELECT 
        f.id as id, fo.feature_probability as prob, 
        f.name as name, f.min_occurrences as min, f.max_occurrences as max,
        v.minBarLength as minBarLength
    
      FROM rhythm_feature_occurrences fo 
        JOIN rhythm_features f ON f.id = fo.rhythm_feature_id 
        JOIN (
          SELECT f.id as fid, MIN(b.length) as minBarLength
          FROM rhythm_feature_occurrences fo
            JOIN rhythm_features f ON f.id = fo.rhythm_feature_id
            JOIN rhythm_bar_occurrences bo ON bo.rhythm_feature_id = f.id
            JOIN rhythm_bars b ON b.id = bo.rhythm_bar_id
          WHERE fo.rhythm_level = :level1 AND fo.bar_info_id = :barinfo1 AND f.cross_bar = 0
          GROUP BY f.id
        ) v ON v.fid = f.id
    
      WHERE fo.rhythm_level = :level2 AND fo.bar_info_id = :barinfo2", 
            ['level1' => $level, 'barinfo1' => $bar_info_id, 'level2' => $level, 'barinfo2' => $bar_info_id]);

        
        return $this->filterFeatures($features);

    }

    private function saveExercise($ex, &$bars) {

        $ex = RhythmExercise::create($ex);
        
        $idx = 0;
        $tuplets = implode(',', array_map(function($barId) use ($ex, &$idx) {
            return '(' . $ex->id . ", $barId, ". $idx++ . ")";
        }, $bars));

        DB::insert("INSERT INTO rhythm_exercise_bars (rhythm_exercise_id, rhythm_bar_id, seq) VALUES $tuplets");

        return $ex->id;

    }

    private function getCrossBarForLevel($level, $barInfo){
        
        $defaultObj = (object) [
            'id' => 1,
            'length' => 0,
            'cross_bar' => 0
        ];

        $fts = DB::select("SELECT f.id as rhythm_feature_id
        from rhythm_features f 
            join rhythm_feature_occurrences fo on fo.rhythm_feature_id = f.id
        where f.cross_bar = 1 AND fo.bar_info_id = ? AND fo.rhythm_level = ?", [$barInfo->id, $level]);

    
        if(count($fts) == 0){ return $defaultObj; }

        $ids = implode(', ', array_map(function($f) {return $f->rhythm_feature_id;}, $fts));

        // Select all cross bars from appropriate features
        $coll = DB::select("SELECT b.id as id, b.cross_bar as cross_bar, b.length as length, bo.bar_probability as prob
        from rhythm_bars b 
            join rhythm_bar_occurrences bo on bo.rhythm_bar_id = b.id 
        where b.cross_bar IS NOT NULL AND bo.rhythm_feature_id IN (?)", [$ids]); // TODO

        if(count($coll) == 0) { return $defaultObj; }

        return $this->weightedRandomSelector($coll, function($b) { return $b->prob; });
        
    }

    private function barLengthIterator(&$bar_info, &$crossBar, $numbars) {

        $inverted = false;
        for($barIdx = 0; $barIdx < $numbars; $barIdx++){

            if(isset($bar_info->subdivisions)){

                if(!$inverted) {

                    $lastIdx = count($bar_info->subdivisions) - 1;

                    for($i = 0; $i < $lastIdx; $i++) {
                        yield $this->getBarInfoLength($bar_info->subdivisions[$i]);
                    }

                    yield $this->getBarInfoLength($bar_info->subdivisions[$lastIdx]) - $crossBar->cross_bar;

                }
                         
                else {
                    
                    $firstIdx = count($bar_info->subdivisions) - 1;
                    // BarIdx is greater than 0 - it's at least second run.
                    yield $this->getBarInfoLength($bar_info->subdivisions[$i]) - $crossBar->length + $crossBar->cross_bar;

                    for($i = $firstIdx - 1; $i >= 0; $i--) {
                        yield $this->getBarInfoLength($bar_info->subdivisions[$i]);
                    }
                        
                }
                    

            } else {

                if($inverted){
                    yield $this->getBarInfoLength($bar_info) - $crossBar->length + $crossBar->cross_bar;
                } else {
                    yield $this->getBarInfoLength($bar_info) - $crossBar->cross_bar;
                }

            }


            $inverted = true;
        }

    }

    private function getBarInfosCollection($level) {
        // BarInfo::where('min_rhythm_level', '<=', $level)->get()->all();
        return DB::select("SELECT bi.* from bar_infos bi where bi.min_rhythm_level <= ? and bi.id in (
            SELECT b.id 
            from bar_infos b
                join rhythm_feature_occurrences fo on fo.bar_info_id = b.id
            where fo.rhythm_level >= ?
            group by b.id
            having COUNT(*) > 0
        )", [$level, $level]);
    }

    private function generateForLevel($level) {

        $numbars = 2;
        $numSubdivisions = 1;

        // - Poglej ker rhythm_level je user 
        // - Izberi naključni bar_info, ki je primeren za ta level 
        // - Inicializiraj arraye za bare in inicializiraj maksimalne dolžine
        // - Prenesi vse pojavitve značilnosti (skupaj z značilnostmi in minimalnimi dolžinami taktov), ki so primerne za ta level in BarInfo
        // - Pojdi čez značilnosti
        //     ○ V boben po vrsti dodaj bare tistih značilnosti, ki imajo nastavljen min_occurrence; Za vsakega naključno določi, v kater bar (1.,2.) gre. 
        //         § ChooseCategoryBar($catId, $spaceLeft)
        //         § Če zmanjka prostora, odnehaj in nadaljuj algoritem.
        //     ○ V vrsto za generiranje dodaj vse verjetnosti (kumulativna vsota) (normaliziraj jih s številom verjetnosti), ki imajo minimalno dolžino manjšo ali enako kot $spaceLeft in max_occurrences večji kot število pojavitev te kategorije, da nastane kot nek številski trak 
        //     ○ Naključno generiraj številko in jo lociraj na traku - tja kamor pade, tisto kategorijo generiraj:
        //         § Naredi nekaj podobnega za rhythm_bar_occurrence
        //         § Zmanjšaj potrebno dolžino; Če je takt poln, povečaj index generiranega takta
        //         § Povečaj število pojavitev kategorije
        //     ○ Če je index enak številu taktov, odnehaj in shrani vajo.


        // - Poglej ker rhythm_level je user ✅ ($level)
        // - Izberi naključni bar_info, ki je primeren za ta level 
        $bar_infos_collection = $this->getBarInfosCollection($level);
        $bar_info_info = $bar_infos_collection[array_rand($bar_infos_collection)];

        $bar_info = json_decode($bar_info_info->bar_info);

        if(isset($bar_info->subdivisions)){
            $numSubdivisions = count($bar_info->subdivisions);
        }
        
        // - Inicializiraj arraye za bare in inicializiraj maksimalne dolžine
        $currentBar = 0; 
        $numResults = $numSubdivisions * $numbars;

        // Choose bar splitters
        // - Randomly choose a cross-bar
        $crossBar = $this->getCrossBarForLevel($level, $bar_info_info);

        $result  = array_fill(0, $numResults, []); // Fill with empty arrays
        $lengths = iterator_to_array($this->barLengthIterator($bar_info, $crossBar, $numbars));
        $featureUseCounter = [];


        $featureTypes = $this->getFeaturesForLevelAndBar($level, $bar_info_info->id);

        // Najprej obvezne sestavine
        // Generiraj Bar iz značilnosti
        // Najdi prvi prost bar od trenutno izbranega naprej. Če ga ni, odstrani ta feature in pojdi naprej.
        // Dodaj bar index v $result
        // povečaj featureUseCount; Če je večji od min
        $currentBar = 0;
        while(count($featureTypes->obligatory) > 0){
            $f = $featureTypes->obligatory[0];
            
            // Choose bar
            $bar = $this->chooseFeatureBar($f->id, $spaceLeft[$obligatoryFill]);
            
            // Find its place
            $idx = $this->getFirstFreeBar($bar->length, $lengths, $currentBar);
            if($idx < 0){
                unset($featureTypes->obligatory[0]);
            }
            $currentBar = $idx;

            // Add bar to result
            $result[$idx][] = $bar->id;
    
            // Increment and remove if there are enough bas of this type
            $this->incrementArrayValue($featureUseCounter, $f->id);                
            if($featureUseCounter[$f->id] >= $f->min){
                
                if(isset($f->max) && $f->max > $f->min){
                    $featureTypes->other[] = $featureTypes->obligatory[0];
                }

                unset($featureTypes->obligatory[0]);
            }
        }

        $allF = $featureTypes->other;
        $currentBar = 0;

        // Until all bars are full
        for($currentBar = 0; $currentBar < $numResults; $currentBar++){

            $remLength = $lengths[$currentBar];

            if($remLength <= 0.001) continue;
            
            // Izberi uteženo naključno značilnost
            $f = $this->chooseFeature($allF, $remLength);
            if(!is_object($f)) { 
                throw new \Exception("FEATURE FOR SPECIFIED MIN LENGTH NOT AVAILABLE!");
            }

            // Izberi uteženo naključen bar
            $bar = $this->chooseFeatureBar($f, $remLength);
            if(!is_object($bar)) { 
                throw new \Exception("ERROR! FEATURE DEFINITION CORRUPTED! BAR OF SPECIFIED LENGTH NOT AVAILABLE DESPITE THE INITIAL MIN LENGTH CHECK!");
            }

            // Dodaj bar index v array
            $result[$currentBar][] = $bar->id; 
            $lengths[$currentBar] -= $bar->length;

            // Zmanjšaj številke
            $this->incrementArrayValue($featureUseCounter, $f->id);
            if(isset($f->max) && $f->max >= $featureUseCounter[$f->id]){
                $allF = array_filter($allF, function($c) use (&$f) {return $c->id != $f->id; });
            }

            $currentBar -= 1;
        }

        // Basic 2 bars merge
        // $bars = array_merge($result[0], [$crossBar->id], $result[1]);

        $bars = [];
        for($i = 0; $i < $numResults; $i++) {

            if($i > 0 && ($i % $numSubdivisions == 0)){
                $bars = array_merge($bars, [$crossBar->id]);
            }

            $bars = array_merge($bars, $result[$i]);
        }

        // Shrani vajo in vrni številko
        return $this->saveExercise([
            'bar_info_id' => $bar_info_info->id,
            'BPM' => 70,
            'rhythm_level' => $level
        ], $bars);

    }

    /**
     * Generate a weighted random value based on the probabilities of each key.
     *
     * @param  array  $options
     * @return stdClass
     */
    private function weightedRandomSelector($options, $selector)
    {
        $rand = rand(0, 1000) / 1000;
        $sum = array_sum(array_map($selector, $options));
        $cumsum = 0;
        $vals = array_map(function($el) use(&$sum, &$selector, &$cumsum) { 
            $cumsum += $selector($el) / $sum;
            return $cumsum; 
        }, $options);
        for($i = 0; $i < count($vals); $i++){
            if ($rand <= $vals[$i]) {
                return $options[$i];
            }
        } 
        return null;
    }

    public function generateNew($level){
        return $this->generateForLevel($level);
    }

    
    public function generateNForLevel($n, $level) {
        $k = [];
        for($i = 0; $i < 10; $i++){
            $k[] = static::resolve($this->generateForLevel($level));
        }
        
        return $k;

    }
}
