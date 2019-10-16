<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminMethods extends Controller {

    public function LastAnswers() {

        return DB::select("SELECT 
            a.success, g.type, g.mode, u.name, a.n_playbacks, 
            a.n_deletions, a.n_additions, a.created_at,
            g.rhythm_level
        
        from answers a
        join games g on g.id = a.game_id
        join game_user gu on gu.game_id = g.id
        join users u on u.id = gu.user_id
        order by a.id desc
        limit 10");
    }

    public function AllUsers() {

        return DB::select("SELECT 
            id, name, email, created_at, verified, grade_id, rhythm_level
            from users order by name");
    }

    public function LastGames() {

        return DB::select("SELECT 
            g.type, g.mode, u.name, gu.points, gu.finished,
            g.created_at,
            g.rhythm_level
        
        from games g
        join game_user gu on gu.game_id = g.id
        join users u on u.id = gu.user_id
        order by g.updated_at desc
        limit 10");
    }
    public function LastBadges() {

        return DB::select("SELECT bu.updated_at as created_at, u.name as Ime, b.name as Znacka
        from badge_user bu
        join users u on u.id = bu.user_id
        join badges b on b.id = bu.badge_id
        
        where bu.completed = 1
        order by bu.updated_at desc
        limit 10;");
    }

    public function Leaderboards() {

        DB::statement("SET @num_row = 0;");

        return DB::select("SELECT u.name, u.rating, v.leaderboard
        from users u
        join (
            select (@num_row := @num_row + 1) as leaderboard, rating from (
                select rating from users group by rating) k
            ) v on v.rating = u.rating order by u.rating desc limit 10;");
    }

    public function BestPlayers() {
        return DB::select("SELECT name, rating from users order by rating limit 9");
    }

}