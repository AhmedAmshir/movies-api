<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enum\GeneralEnum;

class Movie extends Model {

    protected $table = 'movie';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['image_url', 'rating', 'gross_profit', 'director', 'release_year', 'main_actors'];


    public function translations()
    {
        return $this->hasMany('App\MovieTranslations');
    }

    public function findAllMovies($conditions = [])
    {
        try {

            $movie = DB::table('movie')
                ->join('movie_translations', 'movie.id', '=', 'movie_translations.movie_id');

            if(array_key_exists('lang', $conditions)) {
                $movie->where('movie_translations.lang', $conditions['lang']);
            }    
            
            if(array_key_exists('genre_ids', $conditions)) {
                $movie->whereIn('movie_translations.id', $conditions['genre_ids']);
            }

            if(array_key_exists('greater_rating', $conditions)) {
                $movie->where('movie.rating', '>=',$conditions['greater_rating']);
            }

            if(array_key_exists('lower_rating', $conditions)) {
                $movie->where('movie.rating', '<=',$conditions['lower_rating']);
            }

            if(array_key_exists('equal_rating', $conditions)) {
                $movie->where('movie.rating', $conditions['equal_rating']);
            }

            $order_by = 'desc';
            if(array_key_exists('desc', $conditions)) {
                $order_by = 'desc';
            }
            if(array_key_exists('asc', $conditions)) {
                $order_by = 'asc';
            }

            if(array_key_exists('title', $conditions)) {
                $movie->orderBy('movie_translations.title', $order_by);
            }

            if(array_key_exists('rating', $conditions)) {
                $movie->orderBy('movie.rating', $order_by);
            }

            if(array_key_exists('release_year', $conditions)) {
                $movie->orderBy('movie.release_year', $order_by);
            }

            if(array_key_exists('offset', $conditions)) {
                $movie->offset($conditions['offset']);
            }

            if(array_key_exists('limit', $conditions)) {
                $movie->limit($conditions['limit']);
            }
            
            return $movie->get();

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        
    }

    public function findMovieDetails($conditions = []) {
        
        try {

            $movie = DB::table('movie')
                ->join('movie_translations', 'movie.id', '=', 'movie_translations.movie_id');

            if(array_key_exists('lang', $conditions)) {
                $movie->where('movie_translations.lang', $conditions['lang']);
            }

            if(array_key_exists('id', $conditions)) {
                $movie->where('movie.id', $conditions['id']);
            }

            return $movie->get();
            
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function updateMovie($option = [], $fields = []) {
        try {
            $updated = DB::table('movie')
                ->join('movie_translations', 'movie.id', '=', 'movie_translations.movie_id')
                ->where('movie.id', $option['id'])
                ->where('movie_translations.lang', $option['lang']);

            foreach ($fields as $key => $item) {
                $updated->update([$key => $item]);
            }
            
            return !$updated ? FALSE : TRUE;

        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function deleteMovie($option = []) {
        try {
            $deleted = DB::table('movie')
                ->where('movie.id', $option['id'])
                ->delete();
            
            return !$deleted ? FALSE : TRUE;

        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function insertMovieData($data = [], $lang) {

        try {
            $now = Carbon::now();
            $movie_id = DB::table('movie')->insertGetId([
                'image_url' => $data['image_name'],
                'rating' => $data['rating'],
                'gross_profit' => $data['profit'],
                'director' => $data['director'],
                'release_year' => $data['year'],
                'main_actors' => $data['actors'],
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $trans_id = $this->insertTranslations($data, $lang, $movie_id);
            return [$movie_id, $trans_id];

        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function insertTranslations($options, $lang, $movie_id)
    {
        $now = Carbon::now();
        return DB::table('movie_translations')->insertGetId([
            'title' => $options['title'],
            'description' => $options['description'],
            'lang' => $lang,
            'movie_id' => $movie_id,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
