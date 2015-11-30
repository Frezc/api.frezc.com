<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\BgmInfo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnimeStatisticsController extends Controller
{

  public function showBgmInfo($id){
    try{
      $bgm_info = BgmInfo::findOrFail($id);
      return response()->json($bgm_info);
    } catch (ModelNotFoundException $e){
      return $this->response->errorNotFound();
    }
  }
}
