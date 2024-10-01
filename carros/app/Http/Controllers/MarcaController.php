<?php declare(strict_types=1); 

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{   
    private $marca;
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
  
    public function index(Request $request)
    {

        $MarcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;  
            $MarcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        }else{
            $MarcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')) {
          $MarcaRepository->filtro($request->filtro);
           
        }

        if($request->has('atributos')) {
             $MarcaRepository->selectAtributos($request->atributos);      
        }

        return response()->json( $MarcaRepository->getResultado(), 200);
         //all() -> criando um obj de consulta + get() = collection
        //get() -> modificar a consulta ->collection
    }


    public function store(Request $request)
    {
        $request->validate( $this->marca->rules(),$this->marca->feedback());

        $imagem = $request->file('imagem');
       $imahem_urn = $imagem->store('imagens','public');

       $marcas = $this->marca->create([
        'nome'=>$request->nome,
        'imagem'=>$imahem_urn
       ]);

        return response()->json($marcas, 201);
    }

    
    public function show($id)
    {
        $marcas = $this->marca->with('modelos')->find($id);
        if($marcas === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($marcas, 200);
    }

    
    public function update(Request $request, $id)
    {
        
         $marcas = $this->marca->find($id);

         if($marcas === null){
            return response()->json(['erro'=>'Impossivel realizar a atualização. o recuso solicitado não existe'], 404);
         }
         if($request->method() === 'PATCH'){
           
            $regrasDinamica = array();
        
        foreach($marcas->rules() as $input => $regra){
           
             if(array_key_exists($input, $request->all())){
               $regrasDinamica[$input] = $regra;
              
            }
        }
            $request->validate( $regrasDinamica,$this->marca->feedback());

         }else{
            $request->validate( $this->marca->rules(),$this->marca->feedback());
         }
         
         //remove o arquivo antigo caso um novo arquivo seja enviado pelo request!
         if($request->file('imagem')){
            Storage::disk('public')->delete($marcas->imagem);
         }

         $imagem = $request->file('imagem');
         $imahem_urn = $imagem->store('imagens','public');
         
         //preencher o objeto $marca com os dados do request
         $marcas->fill($request->all());
         $marcas->imagem = $imahem_urn;
         $marcas->save();

         return response()->json($marcas, 200);
    }

    
    public function destroy($id)
    {
       $marcas =$this->marca->find($id);
       if($marcas === null){
        return response()->json(['erro'=>'Impossivel realizar a exclusão. o recuso solicitado não existe'], 404 );
       }
       //remove o arquivo antigo
        Storage::disk('public')->delete($marcas->imagem);
     

       $marcas->delete();
        return response()->json(['msg'=>'A marca foi removida com succeso'], 200);
        
    }
}
