<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Joke;

class JokesController extends Controller
{

 public function index(){
   $jokes = Joke::all(); //Not a good idea
   return $jokes;
}
}
