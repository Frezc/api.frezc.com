<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BgmInfo extends Model
{
    protected $connection = 'anime_statistics_db';

    protected $table = 'bgm_info';

    protected $hidden = ['format_name'];
}
