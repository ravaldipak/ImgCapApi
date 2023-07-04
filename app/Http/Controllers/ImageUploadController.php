<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class ImageUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageUpload  $imageUpload
     * @return \Illuminate\Http\Response
     */
    public function show(ImageUpload $imageUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImageUpload  $imageUpload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ImageUpload $imageUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageUpload  $imageUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImageUpload $imageUpload)
    {
        //
    }

    public function upload(Request $request)
    {

        $status = [
            'status'=>false,
            'message'=> "You Are Not Able To Upload Images"
        ];

        $token = JWTAuth::getToken();

        if(!$token){
            $status = [
                'status'=>false,
                'message'=> "Token Not Available"
            ];
            return response()->json($status,200);
        }

        $response = (int) auth('api')->check();

        $user = JWTAuth::toUser( $token);

        if(!$response){
            $status = [
                'status'=>false,
                'message'=> "Token Invalid"
            ];
            return response()->json($status,200);
        }
        
        $validator = Validator::make($request->all(),[ 
            'image' => 'required|mimes:jpg,jpeg,png',
        ]);   
        
        if($validator->fails()) {          
            
            $status = [
                'status'=>false,
                'message'=> ""
            ];

            foreach(json_decode($validator->messages()) as $message){
                if(empty($status['message'])){
                    $status['message'] = $message[0];
                }
                $status['errors'][] = $message[0];
            }
            
            return response()->json($status,404);    
        } 


        if ($file = $request->file('image')) {

            $user_id = $user["id"];

            $path = $file->store('public/images/'.$user_id.'/');
            $name = $file->getClientOriginalName();
  
            //store your file into directory and db
            $storagePath = \Storage::path("/images/kYr6bwGc6DkIqjyW0CuyRLwQ1eV1vAGGjVYq2CZK.png");

            $save = new ImageUpload();
            $save->name = $file;
            $save->path= $storagePath;
            $save->user_id= $user_id;
            $save->save();            

            $status = [
                'status'=>false,
                'message'=> "File successfully uploaded"
            ];
   
        }


        return response()->json($status,200);
    }
}
