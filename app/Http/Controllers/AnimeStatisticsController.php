<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\BgmInfo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use DB;

class AnimeStatisticsController extends Controller
{
  public function test(){
    return 'hello';
  }

  public function showBgmInfo($id){
    try{
      $bgm_info = BgmInfo::findOrFail($id);
      return response()->json($bgm_info);
    } catch (ModelNotFoundException $e){
      return $this->response->errorNotFound();
    }
  }

  public function getAnimeRank(Request $request){
    $params = $request->only('time', 'start', 'limit', 'lang');
    $v = Validator::make($params, [
      'time' => 'date_format:Ym',
      'start' => 'integer|min:0',
      'limit' => 'integer|min:0',
      'lang' => 'in:jp,en,cn'
    ]);

    if ($v->fails()){
      return $this->response->error($v->errors(), 400);
    }

    if ($params['time'] == null){
      $params['time'] = date('Ym', time());
    }
    if ($params['start'] == null){
      $params['start'] = 0;
    }
    if ($params['limit'] == null){
      $params['limit'] = 100;
    }
    if ($params['lang'] == null){
      $params['lang'] = 'cn';
    }

    $selects = ['relate_id', 'score', 'name_'.$params['lang'],
                'ann_score', 'ann_score_rank', 'ann_pop_rank', 'ann_votes',
                'bgm_score', 'bgm_score_rank', 'bgm_pop_rank', 'bgm_votes',
                'sati_score', 'sati_score_rank', 'sati_pop_rank', 'sati_votes'];

    $results = DB::connection('anime_statistics_db')->table('rank'.$params['time'])
      ->select($selects)->orderBy('score', 'desc')->skip($params['start'])
      ->take($params['limit'])->get();

    dd($results);

    if ($results == null){
      return $this->response->errorNotFound();
    } else {
      return response()->json($results);
      return $results->toArray();
    }
  }
}
