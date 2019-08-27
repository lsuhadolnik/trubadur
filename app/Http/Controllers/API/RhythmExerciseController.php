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

        $bar_info = $ex->bar_info->get();

        // split bar jsons with {type: 'bar'}
        $notes = json_decode($bars[0]->content, true);
        for($i = 1; $i < count($bars); $i++){
            $notes = array_merge($notes, json_decode($bars[$i]->content, true));
        }

        // return the exercise
        return array(
            "BPM" => $ex->BPM,
            "name" => $ex->name,
            "bar" => json_decode($bar_info),
            "notes" => $notes
        );

    }


    /*
        Returns time_signature bar duration in number of quarter notes
    */
    private function getBarInfoLength(&$ts){
        
        return (4/$ts->d) * $ts->n;
    }

    private function chooseFeatureBar($featureId, $spaceLeft) {
        $coll = DB::select("SELECT 
            b.id as id, 
            o.bar_probability as prob, 
            b.length as length 
        from rhythm_bar_occurrences o 
            JOIN rhythm_bar b on b.id = rhythm_bar_id 
        
        WHERE o.rhythm_feature_id = :fid AND b.length < :len", ['fid' => $featureId, 'len' => $spaceLeft]);

        $brs = [];
        foreach($coll as $c) {
            $brs[$c->id] = $c->prob;
        }

        $bar_id = $this->weightedRandom($brs);
        return RhythmBar::find($bar_id);
    }

    private function chooseFeature(&$allF, $spaceLeft) {
        throw new \MethodNotImplemented();
    }

    private function getFirstFreeBar($spaceNeeded, &$lengths, $currentBar) {
        throw new \MethodNotImplemented();
    }

    private function filterFeatures(&$features){
        $res = [
            "obligatory" => [],
            "other" => []
        ];
        
        foreach($features as $feature){
            if(isset($feature->min) && $feature->min > 0){
                $res['obligatory'][] = $feature;
            }else {
                $res['other'][] = $feature;
            }
        }

        shuffle($res['obligatory']);

        return $res;
    }

    private function incrementWrap($val, $max, $min = 0){
        throw new \MethodNotImplemented();
    }

    private function incrementArrayValue(&$arr, $idx) {
        throw new \MethodNotImplemented();
    }

    

    private function generateForLevel($level) {

        $numbars = 2;

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
        $bar_infos_collection = BarInfo::where('min_rhythm_level', '>=', $level)->all();
        $bar_info_info = $bar_infos_collection[array_rand($bar_infos_collection)];

        $bar_info = json_decode($bar_info_string);
        $bar_length = $this->getBarInfoLength($bar_info);

        // - Inicializiraj arraye za bare in inicializiraj maksimalne dolžine
        $currentBar = 0;
        $result  = [];
        
        $crossBars = [];
        $lengths = [];
        $featureUseCounter = [];
        for($i = 0; $i < $numbars; $i++) {
            $result[] = []; $crossBars[] = []; $lengths[] = $bar_length;
        }

        
        // - Prenesi vse pojavitve značilnosti (skupaj z značilnostmi in minimalnimi dolžinami taktov), ki so primerne za ta level in BarInfo
        $features = DB::select("SELECT 
            f.id as feature_id, fo.feature_probability as probability, 
            f.name as name, f.min_occurrences as min, f.max_occurrences as max,
            v.minBarLength as minBarLength

        FROM rhythm_feature_occurrences fo 
            JOIN rhythm_features f ON f.id = fo.rhythm_feature_id 
            JOIN (
                SELECT f.id as fid, MIN(b.length) as minBarLength
                FROM rhythm_feature_occurrences fo
                    JOIN rhythm_feature f ON f.id = fo.rhythm_feature_id
                    JOIN rhythm_bar_occurrences bo ON bo.rhythm_feature_id = f.id
                    JOIN rhythm_bars b ON b.id = bo.rhythm_bar_id
                GROUP BY f.id
                WHERE fo.rhythm_level = :level AND fo.bar_info_id = :barinfo
            ) v ON v.fid = f.id

        WHERE fo.rhythm_level = :level AND fo.bar_info_id = :barinfo", 
            ['level' => $level, 'barinfo' => $bar_info_info->info]);

        
        $featureTypes = $this->filterFeatures($features);

        // Najprej obvezne sestavine
        // Generiraj Bar iz značilnosti
        // Najdi prvi prost bar od trenutno izbranega naprej. Če ga ni, odstrani ta feature in pojdi naprej.
        // Dodaj bar index v $result
        // povečaj featureUseCount; Če je večji od min
        $currentBar = 0;
        while(count($featureTypes['obligatory']) > 0){
            $f = $featureTypes['obligatory'][0];
            
            $bar = $this->chooseFeatureBar($f->id, $spaceLeft[$obligatoryFill]);
            
            $idx = $this->getFirstFreeBar($bar->length, $lengths, $currentBar);
            if($idx < 0){
                unset($featureTypes['obligatory'][0]);
            }
            $currentBar = $idx;

            $result[$idx][] = $bar->id;
    
            $this->incrementArrayValue($featureUseCounter, $f->id);                
            if($featureUseCounter[$f->id] >= $f->min){
                
                if(isset($f->max) && $f->max > $f->min){
                    $featureTypes['other'][] = $featureTypes['obligatory'][0];
                }

                unset($featureTypes['obligatory'][0]);
            }
        }

        $allF = $featureTypes['other'];
        $currentBar = 0;

        // Dokler nista zapolnjena oba bara
        for($currentBar = 0; $currentBar < $numbars; $currentBar++){

            if($lengths[$currentBar] <= 0.00001) continue;
        
            $currentBar -= 1;
            
            // Izberi uteženo naključno značilnost
            $f_info = $this->chooseFeature($allF, $lengths[$currentBar]);
            $f = $f_info['f']; $f_idx = $f_info['idx'];

            // Izberi uteženo naključen bar
            $bar = $this->chooseFeatureBar($f, $lengths[$currentBar]);

            // Dodaj bar index v array
            $result[$currentBar][] = $bar->id; 

            // Zmanjšaj številke
            $this->incrementArrayValue($featureUseCounter, $f->id);
            if(isset($f->max) && $f->max >= $featureUseCounter[$f->id]){
                unset($allF[$f_idx]);
            }
        }

        $ex = RhythmExercise::create([
            'bar_info_id' =>$bar_info->id,
            'BPM' => 70,
            'rhythm_level' => $level
        ]);

        
        // Shrani vajo in vrni številko
        $idx = 0;
        for($i = 0; $i < count($result); $i++){

            for($j = 0; $j < count($result[$i]); $j++){
                RhythmExerciseBar::create([
                    'rhythm_exercise_id' => $ex->id,
                    'rhythm_bar_id' => $part,
                    'seq' => $idx++
                ]);
            }

            if($i + 1 < count($result)) {
                RhythmExerciseBar::create([
                    'rhythm_exercise_id' => $ex->id,
                    'rhythm_bar_id' => 1, // barline
                    'seq' => $idx++
                ]);
            }
            
        }

        return $ex->id;
    

    }

    /**
     * Generate a weighted random value based on the probabilities of each key.
     *
     * @param  array  $options
     * @return mixed
     */
    private function weightedRandom($options)
    {
        $sum = 0;
        $rand = rand(0, 1000) / 1000;
        foreach ($options as $option => $probability) {
            $sum += $probability;
            if ($rand <= $sum) {
                return $option;
            }
        }
    }

    public function generateNew($level){

        //return $this->generateShufled($difficulty);
        return $this->generateForLevel($level);

    }
}
