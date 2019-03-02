<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class MovieGenre extends Model {

    protected $table = 'movie_genre';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public function movie_translations()
    {   
        return $this->belongsTo('App\MovieTranslations', 'movie_translations');
    }

    public function findMovieGenres($conditions = [])
    {
        try {
            $genres = DB::table('movie_genre');

            if(array_key_exists('ids', $conditions) && is_array($conditions['ids'])) {
                $genres->whereIn('movie_genre.movie_translations_id', $conditions['ids']);
            }

            if(array_key_exists('id', $conditions)) {
                $genres->where('movie_genre.movie_translations_id', $conditions['id']);
            }
            return $genres->get();

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function getMovieGenres($conditions = [])
    {
        try {
            $genres = DB::table('movie_genre');
            if(array_key_exists('genre', $conditions) && is_array($conditions['genre'])) {
                foreach($conditions['genre'] as $genre) {
                    $genres->orwhere('name', 'like', '%'.$genre.'%');
                }
            } else {
                $genres->where('name', 'like', '%'.$conditions['genre'].'%');
            }
// var_dump($genres->toSql());die;
            return $genres->get();

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function deleteOldAndInsert($conditions = [], $fields = [])
    {
        try {

            $deleted = DB::table('movie_genre')
                ->where('movie_genre.movie_translations_id', $conditions['movie_translations_id'])
                ->delete();
            
            foreach($fields as $field) {
                $this->insertNewGenres($conditions['movie_translations_id'], $field);
            }    
            return TRUE;

        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function insertNewGenres($translations_id, $genre_name)
    {
        try {

            DB::table('movie_genre')->insert([
                'name' => $genre_name,
                'movie_translations_id' => $translations_id
            ]);
        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
