@extends('layouts.app')
@section('title', $epi ? 'Editar EPI' : 'Novo EPI')
@section('content')
<div class="page-header"><div><h1 class="page-title">{{ $epi ? 'Editar EPI' : 'Novo EPI' }}</h1></div><a href="{{ route('epis.index') }}" class="btn btn-secondary">← Voltar</a></div>
<div class="card"><form method="POST" action="{{ $epi ? route('epis.update',$epi->id) : route('epis.store') }}">@csrf @if($epi)@method('PUT')@endif
<div class="form-grid">
    <div class="form-group form-full"><label class="form-label">Nome *</label><input type="text" name="nome" value="{{ old('nome',$epi->nome??'') }}" class="form-control" required></div>
    <div class="form-group"><label class="form-label">Tipo *</label><select name="tipo" class="form-select" required>@foreach(['Capacete','Luva','Óculos','Protetor Auricular','Calçado de Segurança','Respirador','Cinto de Segurança','Colete','Uniforme','Outros'] as $t)<option value="{{ $t }}" {{ old('tipo',$epi->tipo??'')===$t?'selected':'' }}>{{ $t }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Número do CA</label><input type="text" name="numero_ca" value="{{ old('numero_ca',$epi->numero_ca??'') }}" class="form-control" placeholder="Ex: 498232"></div>
    <div class="form-group"><label class="form-label">Validade do CA</label><input type="date" name="validade_ca" value="{{ old('validade_ca',$epi?->validade_ca?->format('Y-m-d')??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">Fornecedor</label><input type="text" name="fornecedor" value="{{ old('fornecedor',$epi->fornecedor??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">Fabricante</label><input type="text" name="fabricante" value="{{ old('fabricante',$epi->fabricante??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">Vida Útil (dias)</label><input type="number" name="vida_util_dias" value="{{ old('vida_util_dias',$epi->vida_util_dias??'') }}" class="form-control" placeholder="Ex: 365"></div>
    <div class="form-group"><label class="form-label">Estoque Mínimo</label><input type="number" name="estoque_minimo" value="{{ old('estoque_minimo',$epi->estoque_minimo??0) }}" class="form-control" min="0"></div>
    <div class="form-group"><label class="form-label">Unidade</label><select name="unidade" class="form-select">@foreach(['un','par','kit','cx','rolo'] as $u)<option value="{{ $u }}" {{ old('unidade',$epi->unidade??'un')===$u?'selected':'' }}>{{ $u }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Custo Unitário (R$)</label><input type="number" name="custo_unitario" value="{{ old('custo_unitario',$epi->custo_unitario??'') }}" class="form-control" step="0.01"></div>
    <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Ativo" {{ old('status',$epi->status??'Ativo')==='Ativo'?'selected':'' }}>Ativo</option><option value="Inativo" {{ old('status','')!=='Ativo'?'selected':'' }}>Inativo</option></select></div>
    <div class="form-group form-full"><label class="form-label">Descrição</label><textarea name="descricao" class="form-control" rows="2">{{ old('descricao',$epi->descricao??'') }}</textarea></div>
</div>
<div class="form-footer"><a href="{{ route('epis.index') }}" class="btn btn-ghost">Cancelar</a><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button></div>
</form></div>
@endsection
