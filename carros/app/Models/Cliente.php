<?php declare(strict_types=1); 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nome'];

       
    public function rules(): array
    {
        return [
            'nome'=>'required'
            
            
        ];
    }
   
}
