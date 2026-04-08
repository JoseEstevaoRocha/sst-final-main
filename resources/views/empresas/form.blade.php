@extends('layouts.app')
@section('title', $empresa ? 'Editar Empresa' : 'Nova Empresa')
@section('content')
<div class="page-header"><div><h1 class="page-title">{{ $empresa ? 'Editar' : 'Nova' }} Empresa</h1></div><a href="{{ route('empresas.index') }}" class="btn btn-secondary">← Voltar</a></div>
<div class="card"><form method="POST" action="{{ $empresa ? route('empresas.update',$empresa->id) : route('empresas.store') }}">@csrf @if($empresa)@method('PUT')@endif
<div class="form-grid">
    <div class="form-group form-full"><label class="form-label">Razão Social *</label><input type="text" name="razao_social" value="{{ old('razao_social',$empresa->razao_social??'') }}" class="form-control" required></div>
    <div class="form-group"><label class="form-label">Nome Fantasia</label><input type="text" name="nome_fantasia" value="{{ old('nome_fantasia',$empresa->nome_fantasia??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">CNPJ * (14 dígitos)</label><input type="text" name="cnpj" value="{{ old('cnpj',$empresa->cnpj??'') }}" class="form-control" maxlength="14" required></div>
    <div class="form-group"><label class="form-label">Telefone</label><input type="text" name="telefone" value="{{ old('telefone',$empresa->telefone??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">E-mail</label><input type="email" name="email" value="{{ old('email',$empresa->email??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">Cidade</label><input type="text" name="cidade" value="{{ old('cidade',$empresa->cidade??'') }}" class="form-control"></div>
    <div class="form-group"><label class="form-label">Estado</label><input type="text" name="estado" value="{{ old('estado',$empresa->estado??'') }}" class="form-control" maxlength="2" placeholder="SP"></div>
    <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-select"><option value="ativa" {{ old('status',$empresa->status??'ativa')==='ativa'?'selected':'' }}>Ativa</option><option value="inativa">Inativa</option></select></div>
    <div class="form-group form-full"><label class="form-label">Endereço</label><input type="text" name="endereco" value="{{ old('endereco',$empresa->endereco??'') }}" class="form-control"></div>
</div>
<div class="form-footer"><a href="{{ route('empresas.index') }}" class="btn btn-ghost">Cancelar</a><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button></div>
</form></div>
@endsection
