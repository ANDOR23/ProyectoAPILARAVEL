<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\v1\PostRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\v1\PostResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PostResource::collection(Post::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $request->validated();
		
		$user = Auth::user();
		
		$post = new Post();
		
		$post->user()->associate($user);

        $url_image = $this->upload($request ->file('image'));

        $post->image = $url_image;

        $post->title = $request->input('title');

        $post->description = $request->input('description');

        $res = $post->save();

        if ($res){

            return response()->json(['message' => ' Post creado'], 201);
        }
        return response()->json(['message' => ' Post no creado'], 500);
    }

    private function upload($image){
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/post';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path()."/$post_path", $rename);

        return "$post_path/$rename";
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        
        return new PostResource($post);
 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        Validator::make($request->all(),[
            'title' => 'max:70',
            'image'=>'image|max:1024',
            'description'=>'max:2000',
        ])->validate();

        if(Auth::id() !== $post->user->id){
            return response()-json(['mensaje'=>'no tienes permiso negro'], 403);
        }
        if(!empty($request->input('title'))){
            $post->title=$request->input('title');
        }
        if(!empty($request->input('description'))){
            $post->description=$request->input('description');
        }
        if(!empty($request->file('image'))){
            $url_image = $this->upload($request->file('image'));
            $post->description=$url_image;
        }

        $res=$post->save();
        
        if($res){
            return response()->json(['mensaje'=>'post actualizado ']);
        }
        return response()->json(['mensaje'=>'post no actualizado '], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $res = $post->delete();

        if($res){
            return response()->json(['mensaje'=>'post eliminado ']);
        }
        return response()->json(['mensaje'=>'post no eliminado '], 500);
    }
	
	public function __construct(){
		
		$this->middleware('auth:api', ['except' => ['index', 'show']]);
	
	
	}
	
}
