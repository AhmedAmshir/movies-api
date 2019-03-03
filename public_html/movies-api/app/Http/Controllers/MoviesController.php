<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Managers\MovieManagerInterface;
use App\Handlers\RequestHandler;
use App\Handlers\ResponseHandler;
use App\Enum\GeneralEnum;
use Carbon\Carbon;

class MoviesController extends Controller
{
    protected $request_handler;
    protected $response_handler;
    protected $movie_service;
    protected $request_data;

    public function __construct(MovieManagerInterface $movie_service, Request $request, RequestHandler $request_handler, ResponseHandler $response_handler) {
        $this->movie_service = $movie_service;
        $this->request_data = $request_handler->ExtractRequestData($request);
        $this->response_handler = $response_handler;
    }

    private function CheckApiKey() {
        
        if(!array_key_exists('api_key', $this->request_data)) {
            return $this->response_handler->NotFound('API KEY not founded');
        }
        if($this->request_data['api_key'] != GeneralEnum::$API_KEY) {
            return $this->response_handler->NotFound('Wrong API KEY');
        }
    }

    // Get all movies..
    public function index() {

        $returned = $this->CheckApiKey();
        if($returned) return $returned;
        
        $options = [];
        if(array_key_exists('lang', $this->request_data)) {
            $options['lang'] = $this->request_data['lang'];

            if(array_key_exists('genre', $this->request_data)) {
                $genres = explode(',', $this->request_data['genre']);
                $options['genre'] = $genres;
            }

            if(array_key_exists('rating', $this->request_data)) {
                $rating = $this->request_data['rating'];
                if(!is_numeric($rating[0]) && in_array($rating[0], GeneralEnum::$greater_or_lower)) {

                    if($rating[0] == GeneralEnum::$greater_or_lower['greater']) {
                        $options['greater_rating'] = substr($rating, 1);
                    } elseif ($rating[0] == GeneralEnum::$greater_or_lower['lower']) {
                        $options['lower_rating'] = substr($rating, 1);
                    }
                } else {
                    $options['equal_rating'] = $rating;
                }
            }

            if(array_key_exists('order_by', $this->request_data)) {
                $order_by = $this->request_data['order_by'];
                if(!is_numeric($order_by[0]) && in_array($order_by[0], GeneralEnum::$desc_or_asc)) {
                    if($order_by[0] == GeneralEnum::$desc_or_asc['desc']) {
                        $options['desc'] = $order_by[0];
                    } elseif ($order_by[0] == GeneralEnum::$desc_or_asc['asc']) {
                        $options['asc'] = $order_by[0];
                    }

                    if(substr($order_by, 1) == 'title') {
                        $options['title'] = $this->request_data['order_by'];
                    } elseif(substr($order_by, 1) == 'rating') {
                        $options['rating'] = $this->request_data['order_by'];
                    } elseif(substr($order_by, 1) == 'year') {
                        $options['release_year'] = $this->request_data['order_by'];
                    }

                } else {
                    if($this->request_data['order_by'] == 'title') {
                        $options['title'] = $this->request_data['order_by'];
                    } elseif($this->request_data['order_by'] == 'rating') {
                        $options['rating'] = $this->request_data['order_by'];
                    } elseif($this->request_data['order_by'] == 'year') {
                        $options['release_year'] = $this->request_data['order_by'];
                    }
                }
            }

            $options['limit'] = GeneralEnum::$API_MOVIES_LIMIT;
            if(array_key_exists('limit', $this->request_data)) {
                $options['limit'] = $this->request_data['limit'];
            }

            if(array_key_exists('offset', $this->request_data)) {
                $options['offset'] = $this->request_data['offset'];
            }
        
            $movies = $this->movie_service->getAllMovies($options);

            if(!$movies || empty($movies)) {

                return $this->response_handler->NotFound('No Movies Found Matching Your Criteria');
            }
            $this->response_handler->AddParameter($movies);
            return $this->response_handler->OK();
        } else {
            return $this->response_handler->BadRequest('Invalid Parameters');
        }   
    }

    public function movieDetails() {

        $returned = $this->CheckApiKey();
        if($returned) return $returned;

        if(!array_key_exists('lang', $this->request_data)) {
            return $this->response_handler->BadRequest("Invalid Parameters 'lang'");
        }

        if(!array_key_exists('id', $this->request_data)) {
            return $this->response_handler->BadRequest("Invalid Parameters 'id'");
        }

        $options['id'] = $this->request_data['id'];
        $options['lang'] = $this->request_data['lang'];

        $movie = $this->movie_service->getMovieDetails($options);
        if(!$movie || empty($movie)) {

            return $this->response_handler->NotFound('No Movie Found Matching Your Criteria');
        }
        $this->response_handler->AddParameter($movie);
        return $this->response_handler->OK();
    }

    public function updateMovie() {

        $returned = $this->CheckApiKey();
        if($returned) return $returned;

        if(!array_key_exists('lang', $this->request_data)) {
            return $this->response_handler->BadRequest("Invalid Parameters 'lang'");
        }

        if(!array_key_exists('id', $this->request_data)) {
            return $this->response_handler->BadRequest("Invalid Parameters 'id'");
        }
	 if($this->request_data['rating'] > 10 || $this->request_data['rating'] < 0) {
            return $this->response_handler->NotFound('Rating must be in between 0 and 10');
        }

        $options['id'] = $this->request_data['id'];
        $options['lang'] = $this->request_data['lang'];
        unset($this->request_data['id']);
        unset($this->request_data['lang']);
	 unset($this->request_data['api_key']);
        if(empty($this->request_data)) {
            return $this->response_handler->NotFound('Not Found Parameters To Update');
        }

        if(array_key_exists('image_url', $this->request_data)) {
            $this->request_data['image_url'] = $this->uploadMovieImage();
        }
        $movie = $this->movie_service->updateMovieInfo($options, $this->request_data);
        if(!$movie) {
            return $this->response_handler->NotFound('No Movie Found Matching Your Criteria');
        }
        return $this->response_handler->OK('Movie Updated Successfully');
    }

    public function deleteMovie() {

        $returned = $this->CheckApiKey();
        if($returned) return $returned;

        if(!array_key_exists('id', $this->request_data)) {
            return $this->response_handler->BadRequest("Invalid Parameters 'id'");
        }

        $options['id'] = $this->request_data['id'];
        $deleted = $this->movie_service->deleteMovie($options);
        if(!$deleted) {
            return $this->response_handler->NotFound('No Movie Found Matching Your Criteria');
        }
        return $this->response_handler->OK('Movie Deleted Successfully');
    }

    function public_path($path = null)
    {
        return rtrim(app()->basePath('public' . $path), '/');
    }

    public function AddNewMovie() {

        $returned = $this->CheckApiKey();
        if($returned) return $returned;

        // if(!array_key_exists('lang', $this->request_data)) {
        //     return $this->response_handler->BadRequest("Invalid Parameters 'lang'");
        // }

        if(array_key_exists('title', $this->request_data) && array_key_exists('description', $this->request_data)
        && array_key_exists('rating', $this->request_data) && array_key_exists('director', $this->request_data)
        && array_key_exists('profit', $this->request_data) && array_key_exists('genre', $this->request_data)
        && array_key_exists('actors', $this->request_data) && array_key_exists('year', $this->request_data)
        && array_key_exists('image_url', $this->request_data) && array_key_exists('description_en', $this->request_data)
        && array_key_exists('title_en', $this->request_data)  && array_key_exists('genre_en', $this->request_data)) {

	     unset($this->request_data['api_key']);
            if($this->request_data['rating'] > 10 || $this->request_data['rating'] < 0) {
                return $this->response_handler->NotFound('Rating must be in between 0 and 10');
            }

            $this->request_data['image_name'] = $this->uploadMovieImage();

            $added = $this->movie_service->saveMovie($this->request_data);
            if(!$added) {
                return $this->response_handler->NotFound('Something Wrong');
            }
            return $this->response_handler->OK('Movie Saved Successfully');

        } else {

            return $this->response_handler->BadRequest("Invalid Parameters");
        }
        
    }

    private function uploadMovieImage()
    {
        $movie_image = $this->request_data['image_url']->getClientOriginalName();
        $movie_image = uniqid() . '_' . $movie_image;
        $destination_path = '/uploads'. DIRECTORY_SEPARATOR .'movies' . DIRECTORY_SEPARATOR;
        $destination_path = $this->public_path($destination_path);
        @mkdir($destination_path, 0777, true);
        $this->request_data['image_url']->move($destination_path, $movie_image);

        return $movie_image;
    }

}
