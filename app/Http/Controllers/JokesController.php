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
   $jokes = Joke::with(
   array('User'=>function($query){
     $query->select('id','name');
     })
   )->select('id', 'body', 'user_id')->paginate(5);


   return response()->json($this->transformCollection($jokes), 200);
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
    'data' => $this->transform($joke)
  ], 200);
}


public function store(Request $request)
{

    if(! $request->body or ! $request->user_id)
    {
        return Response::json([
            'error' => [
                'message' => 'Please Provide Both body and user_id'
            ]
        ], 422);
    }
    $joke = Joke::create($request->all());

    return response()->json([
            'message' => 'Joke Created Succesfully',
            'data' => $this->transform($joke)
    ]);
}

public function update(Request $request, $id){
        if(! $request->body or ! $request->user_id){
            return Response::json([
                'error' => [
                    'message' => 'Please Provide Both body and user_id'
                ]
            ], 422);
        }

        $joke = Joke::find($id);
        $joke->body = $request->body;
        $joke->user_id = $request->user_id;
        $joke->save();

        return response()->json([
                'message' => 'Joke Updated Succesfully'
        ]);
    }
public function destroy($id){
  Joke::destroy($id);
}

public function transformCollection($jokes){
  //return array_map([$this, 'transform'], $jokes->toArray());
  $jokesArray = $jokes->toArray();
  return [
    'total' => $jokesArray['total'],
    'per_page' => $jokesArray['per_page'],
    'current_page' => $jokesArray['current_page'],
    'last_page' => $jokesArray['last_page'],
    'next_page_url' => $jokesArray['next_page_url'],
    'prev_page_url' => $jokesArray['prev_page_url'],
    'from' => $jokesArray['from'],
    'to' => $jokesArray['to'],
    'data' => array_map([$this, 'transform'], $jokesArray['data'])
  ];
}

public function transform($joke){
  return [
    'joke_id' => $joke['id'],
    'joke' => $joke['body'],
    'submitted_by' => $joke['user']['name']
  ];
}

}
