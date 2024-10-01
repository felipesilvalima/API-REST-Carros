<?php declare(strict_types=1); 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    protected $fillable = [
        'modelo_id',
        'placa',
        'disponivel',
        'km'
        
       ];

       public function rules(): array
    {
        return [
            'modelo_id'=>'exists:modelos,id',
            'placa'=>'required',
            'disponivel'=>'required',
            'km'=>'required',
            
        ];
    }
    public function modelo() {
        return $this->belongsTo('App\Models\Modelo');
       }

      
}
