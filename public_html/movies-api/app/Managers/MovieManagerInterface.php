<?php

namespace App\Managers;

interface MovieManagerInterface {
    
    public function getAllMovies($options = []);

    public function getMovieDetails($options = []);

    public function updateMovieInfo($options = [], $fields = []);

    public function deleteMovie($options = []);

    public function saveMovie($options = []);
    
}