@extends('layouts.app')
@section('title', $uniforme ? 'Editar Uniforme' : 'Novo Uniforme')
@section('content')
<div class="page-header"><div><h1 class="page-title">{{ $uniforme ? 'Editar' : 'Novo' }} Uniforme</h1></div><a href="{{ route('uniformes.index') }}" class="btn btn-secondary">← Voltar</a></div>
<div class="card"><form method="POST" action="{{ $uniforme ? route('uniformes.update',$uniforme->id) : route('uniformes.store') }}">@csrf @if($uniforme)@method('PUT')@endif
<div class="form-grid">
    <div class="form-group form-full"><label class="form-label">Nome *</label><input type="text" name="nome" value="{{ old('nome',$uniforme->nome??'') }}" class="form-control" required></div>
    <div class="form-group"><label class="form-label">Tipo *</label><select name="tipo" class="form-select" required>@foreach(['Camisa','Calça','Bota','Jaleco','Colete','Cinto','Boné','Macacão','Outros'] as $t)<option value="{{ $t }}" {{ old('tipo',$uniforme->tipo??'')===$t?'selected':'' }}>{{ $t }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Fornecedor</label><input type="text" name="fornecedor" value="{{ old('fornecedor',$uniforme->fornecedor??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">Custo Unitário (R$)</label><input type="number" name="custo_unitario" value="{{ old('custo_unitario',$uniforme->custo_unitario??'') }}" class="form-control" step="0.01"></div>
    <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Ativo" {{ old('status',$uniforme->status??'Ativo')==='Ativo'?'selected':'' }}>Ativo</option><option value="Inativo">Inativo</option></select></div>
    <div class="form-group form-full"><label class="form-label">Descrição</label><textarea name="descricao" class="form-control" rows="2">{{ old('descricao',$uniforme->descricao??'') }}</textarea></div>
</div>
<div class="form-footer"><a href="{{ route('uniformes.index') }}" class="btn btn-ghost">Cancelar</a><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button></div>
</form></div>
@endsection
