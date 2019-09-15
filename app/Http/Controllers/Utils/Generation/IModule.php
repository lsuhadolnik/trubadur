<?php

namespace App\Http\Controllers\Utils;

interface GeneratingModule {
    public function PreStep(&$result, &$lengths);
    public function PostStep(&$result, &$lengths);
}