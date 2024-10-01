<?php declare(strict_types=1); 

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    private $modelo;
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
  
    
    public function index(Request $request)
    {   
        $ModeloRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;  
            $ModeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        }else{
            $ModeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if($request->has('filtro')) {
          $ModeloRepository->filtro($request->filtro);
           
        }

        if($request->has('atributos')) {
             $ModeloRepository->selectAtributos($request->atributos);      
        }

        return response()->json( $ModeloRepository->getResultado(), 200);
        //all() -> criando um obj de consulta + get() = collection
        //get() -> modificar a consulta ->collection
      
    }


 
    public function store(Request $request)
    {
        $request->validate( $this->modelo->rules());

        $imagem = $request->file('imagem');
        $imahem_urn = $imagem->store('imagens/modelos','public');
 
        $modelos = $this->modelo->create([
         'marca_id'=>$request->marca_id,
         'nome'=>$request->nome,
         'imagem'=>$imahem_urn,
         'numero_portas'=>$request->numero_portas,
         'lugares'=>$request->lugares,
         'air_bag'=>$request->air_bag,
         'abs'=>$request->abs,

        ]);
 
         return response()->json($modelos, 201);
    }

    
    public function show($id)
    {
        $modelos = $this->modelo->with('marca')->find($id);
        if($modelos === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($modelos, 200);
    }

    public function update(Request $request, $id)
    {
        $modelos = $this->modelo->find($id);

        if($modelos === null){
           return response()->json(['erro'=>'Impossivel realizar a atualização. o recuso solicitado não existe'], 404);
        }
        if($request->method() === 'PATCH'){
          
       $regrasDinamica = array();
       
       foreach($modelos->rules() as $input => $regra){
          

           if(array_key_exists($input, $request->all())){
               $regrasDinamica[$input] = $regra;
           }
       }
           $request->validate($regrasDinamica);

        }else{
            $request->validate( $this->modelo->rules());
        }
        //remove o arquivo antigo caso um novo arquivo seja enviado pelo request!
        if($request->file('imagem')){
           Storage::disk('public')->delete($modelos->imagem);
        }

        $imagem = $request->file('imagem');
        $imahem_urn = $imagem->store('imagens/modelos','public');

        $modelos->fill($request->all());
        $modelos->imagem = $imahem_urn;
        $modelos->save();
        
        return response()->json($modelos, 200);
    }

    
    public function destroy($id)
    {
        $modelos =$this->modelo->find($id);
        if($modelos === null){
         return response()->json(['erro'=>'Impossivel realizar a exclusão. o recuso solicitado não existe'], 404 );
        }
        //remove o arquivo antigo
         Storage::disk('public')->delete($modelos->imagem);
      
 
        $modelos->delete();
         return response()->json(['msg'=>'O modelo foi removida com succeso'], 200);
    }
}
