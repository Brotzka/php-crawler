<?php

namespace Brotzka\PhpCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    
    public function urls()
    {
        return $this->hasMany('Brotzka\PhpCrawler\Models\Url');
    }
}
