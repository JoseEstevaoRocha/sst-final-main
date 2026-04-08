@extends('layouts.app')
@section('title', $colaborador ? 'Editar Colaborador' : 'Novo Colaborador')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $colaborador ? 'Editar Colaborador' : 'Novo Colaborador' }}</h1>
    </div>
    <a href="{{ route('colaboradores.index') }}" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <form method="POST" action="{{ $colaborador ? route('colaboradores.update',$colaborador->id) : route('colaboradores.store') }}">
        @csrf @if($colaborador) @method('PUT') @endif
        <div class="form-grid">
            <div class="form-section"><i class="fas fa-user"></i> Dados Pessoais</div>
            <div class="form-group form-full">
                <label class="form-label">Nome Completo <span class="required">*</span></label>
                <input type="text" name="nome" value="{{ old('nome',$colaborador->nome??'') }}" class="form-control @error('nome') is-invalid @enderror" required>
                @error('nome')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">CPF <span class="required">*</span></label>
                <input type="text" name="cpf" value="{{ old('cpf',$colaborador->cpf??'') }}" class="form-control @error('cpf') is-invalid @enderror" maxlength="11" placeholder="Apenas números" required>
                @error('cpf')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">PIS/PASEP</label>
                <input type="text" name="pis" value="{{ old('pis',$colaborador->pis??'') }}" class="form-control" maxlength="11">
            </div>
            <div class="form-group">
                <label class="form-label">Data de Nascimento <span class="required">*</span></label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento',$colaborador->data_nascimento?->format('Y-m-d')??'') }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Sexo <span class="required">*</span></label>
                <select name="sexo" class="form-select" required>
                    <option value="M" {{ old('sexo',$colaborador->sexo??'')==='M'?'selected':'' }}>Masculino</option>
                    <option value="F" {{ old('sexo',$colaborador->sexo??'')==='F'?'selected':'' }}>Feminino</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Escolaridade</label>
                <select name="escolaridade" class="form-select">
                    <option value="">Selecione</option>
                    @foreach(['Ensino Fundamental Incompleto','Ensino Fundamental Completo','Ensino Médio Incompleto','Ensino Médio Completo','Superior Incompleto','Superior Completo','Pós-graduação','Mestrado'] as $e)
                    <option value="{{ $e }}" {{ old('escolaridade',$colaborador->escolaridade??'')===$e?'selected':'' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone',$colaborador->telefone??'') }}" class="form-control" placeholder="(11) 99999-0000">
            </div>
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email',$colaborador->email??'') }}" class="form-control">
            </div>

            <div class="form-section"><i class="fas fa-building"></i> Vínculo Empregatício</div>
            <div class="form-group">
                <label class="form-label">Empresa <span class="required">*</span></label>
                <select name="empresa_id" id="empresa_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($empresas as $e)
                    <option value="{{ $e->id }}" {{ old('empresa_id',$colaborador->empresa_id??'')==$e->id?'selected':'' }}>{{ $e->nome_display }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Setor <span class="required">*</span></label>
                <select name="setor_id" id="setor_id" class="form-select" required>
                    <option value="">Selecione a empresa</option>
                    @foreach($setores as $s)
                    <option value="{{ $s->id }}" {{ old('setor_id',$colaborador->setor_id??'')==$s->id?'selected':'' }}>{{ $s->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Função <span class="required">*</span></label>
                <select name="funcao_id" id="funcao_id" class="form-select" required>
                    <option value="">Selecione o setor</option>
                    @foreach($funcoes as $f)
                    <option value="{{ $f->id }}" {{ old('funcao_id',$colaborador->funcao_id??'')==$f->id?'selected':'' }}>{{ $f->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Matrícula</label>
                <input type="text" name="matricula" value="{{ old('matricula',$colaborador->matricula??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Matrícula eSocial</label>
                <input type="text" name="matricula_esocial" value="{{ old('matricula_esocial',$colaborador->matricula_esocial??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">CBO</label>
                <input type="text" name="cbo" value="{{ old('cbo',$colaborador->cbo??'') }}" class="form-control" placeholder="Ex: 7171-10">
            </div>
            <div class="form-group">
                <label class="form-label">Data de Admissão <span class="required">*</span></label>
                <input type="date" name="data_admissao" value="{{ old('data_admissao',$colaborador->data_admissao?->format('Y-m-d')??'') }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Data de Demissão</label>
                <input type="date" name="data_demissao" value="{{ old('data_demissao',$colaborador->data_demissao?->format('Y-m-d')??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['Contratado','Demitido','Afastado','INSS'] as $st)
                    <option value="{{ $st }}" {{ old('status',$colaborador->status??'Contratado')===$st?'selected':'' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label checkbox-wrap">
                    <input type="checkbox" name="jovem_aprendiz" value="1" {{ old('jovem_aprendiz',$colaborador->jovem_aprendiz??false)?'checked':'' }}>
                    <span class="checkbox-box"></span>
                    <span>Jovem Aprendiz</span>
                </label>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes',$colaborador->observacoes??'') }}</textarea>
            </div>
        </div>
        <div class="form-footer">
            <a href="{{ route('colaboradores.index') }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $colaborador ? 'Salvar alterações' : 'Cadastrar colaborador' }}</button>
        </div>
    </form>
</div>
@endsection
