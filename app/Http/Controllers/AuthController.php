<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use function GuzzleHttp\Promise\all;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','getPerfil']]);
    }
    public function getPerfil (){
       
        $select_consultant = 
            Consultant::select('consultant.id')
            ->where('consultant.id_user' ,auth()->user()->id)
            ->get();

        
        
        if(count($select_consultant)>0)
        {  
            $select = User::
            select('user.id as id_user','email','consultant.name','consultant.id as consultant_id','roles.name as description_role','id_role')
            ->join('consultant', 'consultant.id_user', '=', 'user.id')
            ->join('roles','roles.id', '=', 'user.id_role')
            ->where('user.id' ,auth()->user()->id)
            ->get();
        }
        else
        {
            $select = User::
            select('user.id as id_user','email','roles.name as description_role','id_role')         
            ->join('roles','roles.id', '=', 'user.id_role')
            ->where('user.id' ,auth()->user()->id)
            ->get();
        }
        $create = auth()->user()->created_at;
        $update = auth()->user()->updated_at;
                        
       
         //         echo '<pre>';
         // var_dump($select); die;
         // $users = User::with('perfis')->get()->where('id',$request->all()['id']);
         // return $users->toJson();
 
         $object =[];
         foreach($select as $value){
             $object['id_user'] = $value['id_user'];
             $object['email'] = $value['email'];
             $object['description_role'] = $value['description_role'];
             $object['id_role'] = $value['id_role'];
            if(count($select_consultant)>0)
            {
                $object['name'] = $value['name'];
                $object['consultant_id'] = $value['consultant_id'];
                $object['consultant'] = true;
            }
            else
            {
                $object['consultant'] = false; 
            }
            if($create == $update){
                $object['password_confirmation'] = false;
            }else{
                $object['password_confirmation'] = true;

            }

         }
         // echo '<pre>';
         // var_dump($object); die;
         return $object;
     }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Usuário e/ou senha inválido(s)'], 401);
        }

        return $this->respondWithToken($token);
    }
    public function register(Request $request){



        $this->validate($request, [
            'id_role' => ['required', 'integer'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string'],
            'confirm_password' => ['required', 'string'],
            // 'id_type_phone' => 'required|integer|min:1',
        ]);

        
        $user =  User::Create([
            'email' => request('email'),
            'id_role' => request('id_role'),
            'password' => Hash::make(request('password')),
        ]);
        if($user){
            return response()->json(['status' => true,'success'=>'Email  '.request('email').' cadastrado com sucesso'],200);
        }else{
            return response()->json(['status' => false,'error'=>'Erro interno. Não foi possível cadastrar o usuário. Tente novamente mais tarde'],500);

        }

            // $perfil = request('perfil');
            
            // $user_id = $user->id;
            // for($i=0;$i<count($perfil);$i++){

           
            //     $user_perfil=User_Perfil::create([
            //     'user_id' => $user_id,
            //     'perfil_id' => $perfil[$i]
            // ]);
                
            // }

        return $this->login(request());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->getPerfil());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
            'user'=>$this->getPerfil()
        ]);
    }
}