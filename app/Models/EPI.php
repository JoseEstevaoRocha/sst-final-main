<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EPI extends Model {
    protected $table = 'epis';
    protected $fillable = ['nome','descricao','tipo','numero_ca','validade_ca','fornecedor','fabricante','vida_util_dias','estoque_minimo','unidade','custo_unitario','status'];
    protected function casts(): array { return ['validade_ca'=>'date','custo_unitario'=>'decimal:2']; }
    public function estoques(): HasMany { return $this->hasMany(EPIEstoque::class, 'epi_id'); }
    public function entregas(): HasMany { return $this->hasMany(EntregaEPI::class, 'epi_id'); }
    public function scopeAtivos($q) { return $q->where('status','Ativo'); }
    public function getEstoqueEmpresaAttribute(): int {
        if (!app()->bound('tenant_id')) return 0;
        return $this->estoques()->where('empresa_id', app('tenant_id'))->value('quantidade') ?? 0;
    }
}
