<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Curl\Curl;

class CrawlController extends Controller {

  public function fetchAnimelist(Request $request) {
    $curl = new Curl();
    $curl->get('http://api.bgm.tv/calendar');

    return response()->json($curl->response);
  }
}
