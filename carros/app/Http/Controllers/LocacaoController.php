<?php declare(strict_types=1); 

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    private $locacao;
    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
  
    public function index(Request $request)
    {

        $locacaoRepository = new LocacaoRepository($this->locacao);

        if($request->has('filtro')) {
          $locacaoRepository->filtro($request->filtro);
           
        }

        if($request->has('atributos')) {
             $locacaoRepository->selectAtributos($request->atributos);      
        }

        return response()->json( $locacaoRepository->getResultado(), 200);
         //all() -> criando um obj de consulta + get() = collection
        //get() -> modificar a consulta ->collection
    }


    public function store(Request $request)
    {
        $request->validate( $this->locacao->rules());


       $locacao = $this->locacao->create([
        'cliente_id'=>$request->cliente_id,
        'carro_id'=>$request->carro_id,
        'data_inicio_periodo'=>$request->data_inicio_periodo,
        'data_final_previsto_periodo'=>$request->data_final_previsto_periodo,
        'data_final_realizado_periodo'=>$request->data_final_realizado_periodo,
        'valor_diaria'=>$request->valor_diaria,
        'km_inicial'=>$request->km_inicial,
        'km_final'=>$request->km_final
       ]);

        return response()->json($locacao, 201);
    }

    
    public function show($id)
    {
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($locacao, 200);
    }

    
    public function update(Request $request, $id)
    {
        
         $locacao = $this->locacao->find($id);

         if($locacao === null){
            return response()->json(['erro'=>'Impossivel realizar a atualização. o recuso solicitado não existe'], 404);
         }
         if($request->method() === 'PATCH'){
           
            $regrasDinamica = array();
        
        foreach($locacao->rules() as $input => $regra){
           
             if(array_key_exists($input, $request->all())){
               $regrasDinamica[$input] = $regra;
              
            }
        }
            $request->validate( $regrasDinamica);

         }else{
            $request->validate( $this->locacao->rules());
         }
        
         //preencher o objeto $marca com os dados do request
         $locacao->fill($request->all());
         $locacao->save();

         return response()->json($locacao, 200);
    }

    
    public function destroy($id)
    {
       $locacao =$this->locacao->find($id);
       if($locacao === null){
        return response()->json(['erro'=>'Impossivel realizar a exclusão. o recuso solicitado não existe'], 404 );
       }
     
       $locacao->delete();
        return response()->json(['msg'=>'A locação foi removido com succeso'], 200);
        
    }
}
