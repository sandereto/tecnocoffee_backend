<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Consultant;
use App\Models\ConsultantAddress;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Consultant::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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


        $this->validate($request, [
            'id_user' =>  'required|integer',
            'nome' => 'required',
            'cpf' => 'required|min:11|max:11',
            'data_nascimento' => 'required|date_format:Y-m-d|',
            'cidade_atuacao' => 'required',
            'cep' => 'required',
            'estado' => 'required',
            'cidade' => 'required',
            'bairro' => 'required',
            'rua' => 'required',
            'complemento' => 'string',
            'numero' => 'required|integer',
            'id_type_phone' => 'required|integer',
            'telefone' => 'required|string|min:10|max:11'
        ]);
            
        DB::beginTransaction();
        try{
           $consultant_post = Consultant::create([
                'id_user' => request('id_user'),
                'name' => request('nome'),
                'cpf' => request('cpf'),
                'birth_date' => request('data_nascimento')
            
            ]);

            $post_phone = Phone::create([
                'number' => request('telefone'),
                'id_type_phone' => request('id_type_phone'),
                'id_consultant' => $consultant_post->id
            ]);
            $address_post = Address::create([
                'zipcode' => request('cep'),
                'state' => request('estado'),
                'city' => request('cidade'),
                'neighborhood' => request('bairro'),
                'street' => request('rua')
            ]);

 
            $address_consult = ConsultantAddress::create([
                'id_consultant' => $consultant_post->id,
                'id_address' => $address_post->id,
                'number' => request('numero'),
                'complement' => request('complemento'),
                'acting_city' => request('cidade_atuacao')
            ]);
            $consultant_post->save();
            $post_phone->save();
            $address_post->save();
            $address_consult->save();
            DB::commit();
            return response()->json(['success'=>'Usuário cadastrado com sucesso'],200);
            // if($consultant_post && $address_post && $address_consult && $post_phone){
            //     return response()->json(['success'=>'Usuário cadastrado com sucesso'],200);
    
            // }else{
            //     return response()->json(['error'=>'Cadastro incompleto. Verifique as informações'],401);
    
            // }
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error'=>'Cadastro incompleto. Verifique as informações: '.$e],401);
            // something went wrong
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        return User::select('user.id as user_id','user.email',
            'consultant.id as consultant_id','consultant.name','consultant.cpf','consultant.birth_date',
            'consultant_address.number','consultant_address.complement','consultant_address.acting_city',
            'phone.number as telefone','address.zipcode','address.state','address.city','address.neighborhood',
            'address.street'
        )
        ->join('consultant','consultant.id_user','=','user.id')
        ->join('consultant_address','consultant_address.id_consultant','=','consultant.id')
        ->join('address','address.id','=','consultant_address.id_address')
        ->join('phone','phone.id_consultant','=','consultant.id')
        ->where('user.id',$id)
        ->get();

        
        // return DB::select(
        //     DB::Raw(
        //         'SELECT 
        //         consultant.id as consultant_id,
        //         consultant.name,
        //         consultant.cpf,
        //         consultant.birth_date,
        //         consultant_address.number,
        //         consultant_address.complement,
        //         consultant_address.acting_city,
        //         phone.number,
        //         address.zipcode,
        //         address.state,
        //         address.city,
        //         address.neighborhood,
        //         address.street
        //         FROM consultant
        //             inner join consultant_address
        //                 on consultant.id = consultant_address.id_consultant
        //             inner join address
        //                 on address.id = consultant_address.id_address
        //             inner join phone
        //                 on consultant.id = phone.id_consultant
        //         where consultant.id = '.$consultant->id.'
        //         '
        //     )
        // );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id_user' =>  'required|integer',
            'nome' => 'required|min:3',
            'cpf' => 'required|min:11|max:11',
            'data_nascimento' => 'required|date_format:Y-m-d|',
            'cidade_atuacao' => 'required',
            'cep' => 'required',
            'estado' => 'required',
            'cidade' => 'required',
            'bairro' => 'required',
            'rua' => 'required',
            'complemento' => '',
            'numero' => 'required|integer',
            'id_type_phone' => 'required|integer',
            'telefone' => 'required|string|min:10|max:11'
        ]);
            
            $consultant_post = Consultant::findOrFail($id);
            $consultant_post->update([
                'name' => request('nome'),
                'cpf' => request('cpf'),
                'birth_date' => request('data_nascimento')
            ]);
            
            $post_phone = Phone::where('id_consultant',$id)->firstOrFail();
            $post_phone->update([
                'number' => request('telefone'),
                'id_type_phone' => request('id_type_phone'),
            ]);


            $address_consult = ConsultantAddress::where('id_consultant',$id)->firstOrFail();
            $address_consult->update([
                'number' => request('numero'),
                'complement' => request('complemento'),
                'acting_city' => request('cidade_atuacao')
            ]);
            
            $address_post = Address::where('id',$address_consult->id_address)->firstOrFail();
            $address_post->update([
                'zipcode' => request('cep'),
                'state' => request('estado'),
                'city' => request('cidade'),
                'neighborhood' => request('bairro'),
                'street' => request('rua')
            ]);

 
            
           
            if($consultant_post && $address_post && $address_consult && $post_phone){
                return response()->json(['success'=>'Usuário cadastrado com sucesso'],200);
    
            }else{
                return response()->json(['error'=>'Cadastro incompleto. Verifique as informações'],401);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}