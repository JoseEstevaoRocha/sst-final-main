<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EpiMovimentacao extends Model {
    protected $table = 'epi_movimentacoes';
    protected $fillable = ['epi_id','empresa_id','tipo','quantidade','motivo','usuario'];
}
