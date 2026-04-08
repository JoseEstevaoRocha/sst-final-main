<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manutencao extends BaseModel {
    protected $table = 'manutencoes';
    protected $fillable = ['empresa_id','maquina_id','tipo','data_manutencao','hora_inicio','hora_fim','duracao_minutos','descricao','responsavel','custo','proxima_manutencao'];
    protected function casts(): array { return ['data_manutencao'=>'date','proxima_manutencao'=>'date','custo'=>'decimal:2']; }
    public function maquina(): BelongsTo { return $this->belongsTo(Maquina::class); }
    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
}
