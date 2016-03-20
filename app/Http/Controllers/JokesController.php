<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Joke;
use App\User;

class JokesController extends Controller
{

 public function index(){
   $jokes = Joke::all(); //Not a good idea
   return response()->json([
     'jokes_data' =>  $this->transformCollection($jokes)
   ], 200);
 }

public function show($id){
  $joke = Joke::with(
  array('User'=>function($query){
    $query->select('id', 'name');
    })
  )->find($id);

  if(!$joke){
    return response()->json([
      'error' => [
        'message' => 'Joke does not exist'
      ]
    ], 404);
  }

  // get previous joke id
  $previous = Joke::where('id', '<', $joke->id)->max('id');

  //get next joke id
  $next = Joke::where('id', '>', $joke->id)->min('id');

  return response()->json([
    'previous_joke_id' => $previous,
    'next_joke_id' =>$next,
    'jokes_data' => $this->transform($joke)
  ], 200);
}

public function transformCollection($jokes){
  return array_map([$this, 'transform'], $jokes->toArray());
}

public function transform($joke){
  return [
    'joke_id' => $joke['id'],
    'joke' => $joke['body'],
    'submitted_by' => $joke['user']['name']
  ];
}

}
