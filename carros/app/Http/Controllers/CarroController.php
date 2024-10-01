<?php declare(strict_types=1); 

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarroController extends Controller
{
    private $carro;
    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
  
    public function index(Request $request)
    {

        $CarroRepository = new CarroRepository($this->carro);

        if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,'.$request->atributos_modelo;  
            $CarroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        }else{
            $CarroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if($request->has('filtro')) {
          $CarroRepository->filtro($request->filtro);
           
        }

        if($request->has('atributos')) {
             $CarroRepository->selectAtributos($request->atributos);      
        }

        return response()->json( $CarroRepository->getResultado(), 200);
         //all() -> criando um obj de consulta + get() = collection
        //get() -> modificar a consulta ->collection
    }


    public function store(Request $request)
    {
        $request->validate( $this->carro->rules());


       $carros = $this->carro->create([
        'modelo_id'=>$request->modelo_id,
        'placa'=>$request->placa,
        'disponivel'=>$request->disponivel,
        'km'=>$request->km,
       ]);

        return response()->json($carros, 201);
    }

    
    public function show($id)
    {
        $carros = $this->carro->with('modelo')->find($id);
        if($carros === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($carros, 200);
    }

    
    public function update(Request $request, $id)
    {
        
         $carros = $this->carro->find($id);

         if($carros === null){
            return response()->json(['erro'=>'Impossivel realizar a atualização. o recuso solicitado não existe'], 404);
         }
         
         if($request->method() === 'PATCH'){
           
            $regrasDinamica = array();
        
        foreach($carros->rules() as $input => $regra){
           
             if(array_key_exists($input, $request->all())){
               $regrasDinamica[$input] = $regra;
              
            }
        }
            $request->validate( $regrasDinamica);

         }else{
            $request->validate( $this->carro->rules());
         }
        
         //preencher o objeto $marca com os dados do request
         $carros->fill($request->all());
         $carros->save();

         return response()->json($carros, 200);
    }

    
    public function destroy($id)
    {
       $carros =$this->carro->find($id);
       if($carros === null){
        return response()->json(['erro'=>'Impossivel realizar a exclusão. o recuso solicitado não existe'], 404 );
       }
     
       $carros->delete();
        return response()->json(['msg'=>'O carro foi removido com succeso'], 200);
        
    }
}
