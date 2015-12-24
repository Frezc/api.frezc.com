<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnInfo extends Model
{
    protected $connection = 'anime_statistics_db';

    protected $table = 'ann_info';
}
