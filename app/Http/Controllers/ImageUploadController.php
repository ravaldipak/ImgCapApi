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


        $response = (int) auth('api')->check();

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

            $path = $file->store('public/images');
            $name = $file->getClientOriginalName();
  
            //store your file into directory and db
            $save = new ImageUpload();
            $save->name = $file;
            $save->path= $path;
            $save->save();
               

            // $path = storage_path('images/' . $name);

            // if (!ImageUpload::exists($path)) {
            //     abort(404);
            // }


            // $file = ImageUpload::get($path);
            // $type = ImageUpload::mimeType($path);

            // $response = Response::make($file, 200);

            $status = [
                'status'=>false,
                'message'=> "File successfully uploaded",
                "file" => $name
            ];
   
        }


        return response()->json($status,200);
    }
}
