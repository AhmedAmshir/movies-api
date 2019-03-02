<?php

namespace App\Managers;

use App\Movie;
use App\MovieGenre;
use App\Managers\MovieManagerInterface;

class MovieManager implements MovieManagerInterface {

    protected $movie_model;
    protected $movie_genres_model;
    
    public function __construct(Movie $movie_model, MovieGenre $movie_genres_model) {
        $this->movie_model = $movie_model;
        $this->movie_genres_model = $movie_genres_model;
    }

    public function getAllMovies($options = []) {

        if(array_key_exists('genre', $options)) {
            $filterd_genres = $this->movie_genres_model->getMovieGenres(['genre' => $options['genre']]);
            $genre_ids = [];
            foreach($filterd_genres as $genre) {
                $genre_ids[] = $genre->movie_translations_id;
            }
            $options['genre_ids'] = array_unique($genre_ids);
        }
        $movies = $this->movie_model->findAllMovies($options);
        if(empty($movies)) {
            return false;
        }

        $ids = [];
        foreach($movies as $movie) {
            $ids[] = $movie->id;
        }
        $genres = $this->movie_genres_model->findMovieGenres(['ids' => $ids]);

        $return_data = [];
        foreach($movies as $movie) {
            $genres_name = [];
            foreach($genres as $genre) {
                if($movie->id == $genre->movie_translations_id) {
                    $genres_name[] = trim($genre->name);
                }
            }
            $return_data[] = [
                'id' => $movie->movie_id,
                'title' => trim($movie->title),
                'description' => trim($movie->description),
                'genre' => $genres_name,
                'image_url' => url().'/uploads/movies/'.trim($movie->image_url),
                'rating' => $movie->rating,
                'profit' => $movie->gross_profit,
                'director' => trim($movie->director),
                'release_year' => $movie->release_year,
                'actors' => trim($movie->main_actors)
            ];

        }
        return $return_data;
    }

    public function getMovieDetails($options = []) {

        if(array_key_exists('id', $options)) {
            $movie = $this->movie_model->findMovieDetails($options);
            if(empty($movie[0])) return FALSE;

            $genres = $this->movie_genres_model->findMovieGenres(['id' => $movie[0]->id]);
            $genres_name = [];
            foreach($genres as $genre) {
                if($movie[0]->id == $genre->movie_translations_id) {
                    $genres_name[] = trim($genre->name);
                }
            }
            
            $return_data = [
                'id' => $movie[0]->id,
                'title' => trim($movie[0]->title),
                'description' => trim($movie[0]->description),
                'genre' => $genres_name,
                'image_url' => url().'/uploads/movies/'.trim($movie[0]->image_url),
                'rating' => $movie[0]->rating,
                'profit' => $movie[0]->gross_profit,
                'director' => trim($movie[0]->director),
                'release_year' => $movie[0]->release_year,
                'actors' => trim($movie[0]->main_actors)
            ];

            return $return_data;
        }
        return FALSE;
    }

    public function updateMovieInfo($options = [], $fields = []) {

        $genres = [];
        if(array_key_exists('genre', $fields)) {
            $genres = $fields['genre'];
            $genres = explode(',', $genres);
            unset($fields['genre']);
        }

        $updated = $this->movie_model->updateMovie($options, $fields);
        if($updated) {
            if(!empty($genres)) {
                $movie = $this->movie_model->findMovieDetails($options);
                $this->movie_genres_model->deleteOldAndInsert(['movie_translations_id' => $movie[0]->id], $genres);
                return TRUE;
            }
            return TRUE;
        }
        return FALSE;
    }

    public function deleteMovie($options = []) {

        return $this->movie_model->deleteMovie($options);
    }

    public function saveMovie($options = []) {

        if(is_array($options) && !empty($options)) {
            
            $genres = explode(',', $options['genre']);
            $options['genre'] = $genres;
            $translations_id = $this->movie_model->save($options);

            if($translations_id) {
                foreach($genres as $genre) {
                    $this->movie_genres_model->insertNewGenres($translations_id, $genre);
                }
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }
    

}
