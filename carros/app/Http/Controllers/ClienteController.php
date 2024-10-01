<?php declare(strict_types=1); 

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private $cliente;
    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }
  
    public function index(Request $request)
    {

        $ClienteRepository = new ClienteRepository($this->cliente);

        if($request->has('filtro')) {
          $ClienteRepository->filtro($request->filtro);
           
        }

        if($request->has('atributos')) {
             $ClienteRepository->selectAtributos($request->atributos);      
        }

        return response()->json( $ClienteRepository->getResultado(), 200);
         //all() -> criando um obj de consulta + get() = collection
        //get() -> modificar a consulta ->collection
    }


    public function store(Request $request)
    {
        $request->validate( $this->cliente->rules());


       $cliente = $this->cliente->create([
        'nome'=>$request->nome
       ]);

        return response()->json($cliente, 201);
    }

    
    public function show($id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($cliente, 200);
    }

    
    public function update(Request $request, $id)
    {
        
         $cliente = $this->cliente->find($id);

         if($cliente === null){
            return response()->json(['erro'=>'Impossivel realizar a atualização. o recuso solicitado não existe'], 404);
         }
         if($request->method() === 'PATCH'){
           
            $regrasDinamica = array();
        
        foreach($cliente->rules() as $input => $regra){
           
             if(array_key_exists($input, $request->all())){
               $regrasDinamica[$input] = $regra;
              
            }
        }
            $request->validate( $regrasDinamica);

         }else{
            $request->validate( $this->cliente->rules());
         }
        
         //preencher o objeto $marca com os dados do request
         $cliente->fill($request->all());
         $cliente->save();

         return response()->json($cliente, 200);
    }

    
    public function destroy($id)
    {
       $cliente =$this->cliente->find($id);
       if($cliente === null){
        return response()->json(['erro'=>'Impossivel realizar a exclusão. o recuso solicitado não existe'], 404 );
       }
     
       $cliente->delete();
        return response()->json(['msg'=>'O cliente foi removido com succeso'], 200);
        
    }
}
