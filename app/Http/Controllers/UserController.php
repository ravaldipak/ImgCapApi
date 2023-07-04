<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status'=> false,
            'message'=>'You don\' have access'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $status = [
            'status'=>false,
            'message'=> 'Sorry! User Not create' 
        ];
        try {
            
            $users = new User;

            $users->name = $request->user_name;
            $users->email = $request->email;
            $users->password = \Hash::make($request->password);

            $users->save();

            $status = [
                'status'=>true,
                'message'=> "User Created Successfully"
            ];

        } catch (\Throwable $th) {

            

            if(!empty($th->errorInfo)){
                $errorCode = $th->errorInfo[1];          
                switch ($errorCode) {
                    case 1062://code dublicate entry 
                        $status = [
                            'status'=>false,
                            'message'=> "Sorry! User Already Exists"
                        ]; 
                        break;
                    default: 
                        $status = [
                            'status'=>false,
                            'message'=> $th->getMessage()
                        ];     
                        break;
                }
            }else{
                $status = [
                    'status'=>false,
                    'message'=> $th->getMessage()
                ];  
            }        
            
        }

        return response()->json($status,200);
        
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(Users $users)
    {
        //
    }



    public function register(Request $request) 
    {

        $status = [
            'status'=>false,
            'message'=> 'Sorry! Email or Password Wrong' 
        ];


        $input = $request->only('name', 'email', 'password');

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){

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

        $input['password'] = bcrypt($input['password']); // use bcrypt to hash the passwords
        $user = User::create($input); // eloquent creation of data

        $success['user'] = $user;

        $status = [
            'status'=>true,
            'message'=> 'User Signup Successfully',
            'user'=>$user 
        ];               
        return response()->json($status,200);

    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){

        $status = [
            'status'=>false,
            'message'=> 'Sorry! Email or Password Wrong' 
        ];

        try {
            $input = $request->only('email', 'password');

            $validator = Validator::make($input, [
                'email' => 'required',
                'password' => 'required',
            ]);

            if($validator->fails()){
                $status = [
                    'status'=>false,
                    'message'=> '' 
                ];
                foreach(json_decode($validator->messages()) as $message){
                    if(empty($status['message'])){
                        $status['message'] = $message[0];
                    }
                    $status['errors'][] = $message[0];
                }
                return response()->json($status,404);
            }

            if ($token = JWTAuth::attempt($input)) { 

                $status = [
                    'status'=>true,
                    'message'=> 'User Login Successfully',
                    'token'=>$token 
                ];               
                return response()->json($status,404);
            }
        } catch (\Throwable $th) {
            $status = [
                'status'=>false,
                'message'=> $th->getMessage()
            ];      
            
        }

        return response()->json($status,200);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Users $users)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(Users $users)
    {
        //
    }
}
