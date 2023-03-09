<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSacola extends Model
{
    use HasFactory;

    protected $fillable = ['quantidade','preço','produto_id'];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
    public function sacola()
    {
        return $this->belongsTo(Sacola::class);
    }
}
