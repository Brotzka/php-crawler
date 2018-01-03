<?php

namespace Brotzka\PhpCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    protected $fillable = [
        'site_id',
        'full_url',
        'scheme',
        'host',
        'user',
        'pass',
        'port',
        'path',
        'query',
        'fragment'
    ];


    public function site()
    {
        return $this->belongsTo('Brotzka\PhpCrawler\Models\Site');
    }
}
