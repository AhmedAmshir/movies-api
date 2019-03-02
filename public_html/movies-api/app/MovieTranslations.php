<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;

class MovieTranslations extends Model {

    protected $table = 'movie_translations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'desription', 'genre', 'lang'];

    public function movie()
    {
        return $this->belongsTo('App\Movie', 'movie_id');
    }

    public function genres()
    {
        return $this->hasMany('App\MovieGenre');
    }
}
