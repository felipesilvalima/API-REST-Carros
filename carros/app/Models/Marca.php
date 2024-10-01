<?php declare(strict_types=1); 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{

   protected $fillable = [
    'nome',
    'imagem'
   ];

       public function rules(): array
    {
        return [
            'nome'=>'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem'=>'required|file|mimes:png'
        ];
    }

    public function feedback(): array
    
    {
     return 
     [
         'required'=>'O campo :attribute é obrigatorio',
         'nome'=>'O nome da marca já existe',
         'imagem.mimes'=>'O campo imagem deve ser um arquivo do tipo: png.',
         'nome.min'=>'O nome der ter no minimo 3 caracteres',
     ];
    }

   public function modelos(){
      return $this->hasMany('App\Models\Modelo');
   }

}
