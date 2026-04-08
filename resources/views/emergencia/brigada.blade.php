@extends('layouts.app')
@section('title','Brigada de Incêndio')
@section('content')
<div class="page-header"><div><h1 class="page-title">Brigada de Incêndio</h1><p class="page-sub">{{ $brigadistas->count() }} brigadistas ativos</p></div><button class="btn btn-primary" onclick="openModal('modalBrigada')"><i class="fas fa-plus"></i> Adicionar Brigadista</button></div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>COLABORADOR</th><th>FUNÇÃO NA BRIGADA</th><th>INÍCIO</th><th>VALIDADE CERT.</th><th>STATUS</th><th>AÇÕES</th></tr></thead>
<tbody>
@forelse($brigadistas as $b)
<tr>
    <td><div class="font-bold text-13">{{ $b->colaborador->nome??'—' }}</div><div class="text-11 text-muted">{{ $b->colaborador->funcao->nome??'' }}</div></td>
    <td class="text-12">{{ $b->funcao_brigada??'—' }}</td>
    <td class="font-mono text-12">{{ $b->data_inicio?->format('d/m/Y')??'—' }}</td>
    <td class="font-mono text-12 {{ $b->data_validade_cert?->isPast()?'text-danger':'' }}">{{ $b->data_validade_cert?->format('d/m/Y')??'—' }}</td>
    <td><span class="badge {{ $b->ativo?'badge-success':'badge-danger' }}">{{ $b->ativo?'Ativo':'Inativo' }}</span></td>
    <td><form method="POST" action="{{ route('brigada.destroy',$b->id) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Remover?"><i class="fas fa-trash-alt"></i></button></form></td>
</tr>
@empty
<tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-user-shield"></i></div><h3>Nenhum brigadista</h3></div></td></tr>
@endforelse
</tbody></table></div></div>
<div class="modal-overlay" id="modalBrigada"><div class="modal modal-md">
<div class="modal-header"><div class="modal-title"><i class="fas fa-user-shield"></i> Adicionar Brigadista</div><button class="modal-close" onclick="closeModal('modalBrigada')"><i class="fas fa-times"></i></button></div>
<div class="modal-body"><form method="POST" action="{{ route('brigada.store') }}">@csrf
<div class="form-grid">
    <div class="form-group form-full"><label class="form-label">Colaborador *</label><select name="colaborador_id" class="form-select" required><option value="">Selecione</option>@foreach($colaboradores as $c)<option value="{{ $c->id }}">{{ $c->nome }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Função na Brigada</label><select name="funcao_brigada" class="form-select"><option value="">Selecione</option>@foreach(['Líder de Brigada','Brigadista','Suplente'] as $f)<option value="{{ $f }}">{{ $f }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Data de Início</label><input type="date" name="data_inicio" class="form-control"></div>
    <div class="form-group"><label class="form-label">Validade da Certificação</label><input type="date" name="data_validade_cert" class="form-control"></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal('modalBrigada')">Cancelar</button><button type="submit" class="btn btn-primary">Adicionar</button></div>
</form></div></div></div>
@endsection
