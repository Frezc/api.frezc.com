<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\BgmInfo;
use App\Models\RelateInfo;
use App\Models\AnnInfo;
use DB;

class AnimeStatisticsController extends Controller
{
  public function test(Request $request){
    return 'ip: '.$request->ip()
      .' path: ['.$request->method().'] '.$request->path();
  }

  public function showBgmInfo($id){
      $bgm_info = BgmInfo::findOrFail($id);
      return response()->json($bgm_info);
  }

  public function showRelateInfo($id){
      $relate_info = RelateInfo::findOrFail($id);
      $ann_info = AnnInfo::where('url', $relate_info->ann_url)->firstOrFail();
      $relate_info->type = $ann_info->anime_type;
      $relate_info->eps = $ann_info->number_of_episodes;
      return response()->json($relate_info);
  }

  public function getAnimeRank(Request $request){
    $this->validate($request, [
      // 'time' => 'date_format:Y-m',
      'start' => 'integer|min:0',
      'limit' => 'integer|min:0',
      'lang' => 'in:jp,en,cn'
    ]);

    $params = $request->only('start', 'limit', 'lang');
    
    $now = time();
    $month = date('Y-m', $now);
    if ($now > strtotime($month.'-2 00:00:00'))
      $params['time'] = date('Ym', $now);
    else
      $params['time'] = date('Ym', strtotime('-2 day'));

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

    if ($results == null){
      return response()->json(['error' => 'no results'], 404);
    } else {
      $update_date = substr($params['time'], 0, 4).'-'.substr($params['time'], 4).'-01';
      return response()->json(['rank' => $results, 'updated_date' => $update_date]);
    }
  }
}
