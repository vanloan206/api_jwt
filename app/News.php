<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['name', 'preview', 'detail', 'picture'];
    protected $table = 'news';
    protected $primaryKey = 'id';
    public $timestamp = false;

}
