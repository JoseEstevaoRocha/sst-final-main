<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model {
    use SoftDeletes;
    protected $fillable = ['razao_social','nome_fantasia','cnpj','endereco','cidade','estado','cep','telefone','email','logo_path','status'];
    protected function casts(): array { return ['status' => 'string']; }
    public function setores(): HasMany { return $this->hasMany(Setor::class); }
    public function colaboradores(): HasMany { return $this->hasMany(Colaborador::class); }
    public function asos(): HasMany { return $this->hasMany(ASO::class); }
    public function epis(): HasMany { return $this->hasMany(EPIEstoque::class); }
    public function getNomeDisplayAttribute(): string { return $this->nome_fantasia ?: $this->razao_social; }
    public function scopeAtivas($q) { return $q->where('status','ativa'); }
}
