<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Joke;

class JokesController extends Controller
{

 public function index(){
   $jokes = Joke::all(); //Not a good idea
   return response()->json([
   ], 200);
 }

public function show($id){
  $joke = Joke::find($id);

  if(!$joke){
    return response()->json([
      'error' => [
        'message' => 'Joke does not exist'
      ]
    ], 404);
  }
  return response()->json([
  ], 200);
}

public function transformCollection($jokes){
  return array_map([$this, 'transform'], $jokes->toArray());
}

public function transform($joke){
  return [
    'joke_id' => $joke['id'],
    'joke' => $joke['body']
  ];
}

}
