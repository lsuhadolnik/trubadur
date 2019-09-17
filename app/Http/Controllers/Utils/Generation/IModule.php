<?php

namespace App\Http\Controllers\Utils\Generation;

interface IModule {
    public function PreStep(&$result, &$lengths);
    public function PostStep(&$result, &$lengths);
    public function RemLenStep(&$result, $length, &$barInfo, $barId);
}