@extends('layouts.app')
@section('title','Uniformes')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Catálogo de Uniformes</h1></div>
    <div class="flex gap-8">
        <a href="{{ route('uniformes.entregas') }}" class="btn btn-secondary"><i class="fas fa-box-open"></i> Entregas</a>
        <a href="{{ route('uniformes.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Uniforme</a>
    </div>
</div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>UNIFORME</th><th>TIPO</th><th>FORNECEDOR</th><th>GRADE DE ESTOQUE</th><th>CUSTO</th><th>STATUS</th><th>AÇÕES</th></tr></thead>
<tbody>
@forelse($uniformes as $u)
<tr>
    <td class="font-bold text-13">{{ $u->nome }}</td>
    <td><span class="badge badge-secondary">{{ $u->tipo }}</span></td>
    <td class="text-12">{{ $u->fornecedor??'—' }}</td>
    <td>
        <div class="grade-chips">
            @foreach($u->estoques as $est)
            @php $nivel=$est->quantidade<=0?'danger':($est->baixo_estoque?'warn':'ok'); @endphp
            <span class="grade-chip grade-{{ $nivel }}" onclick="openEstModal({{ $u->id }},{{ $est->tamanho_id }},'{{ $est->tamanho->codigo }}',{{ $est->quantidade }})" title="Clique para editar">
                <span class="grade-code">{{ $est->tamanho->codigo }}</span>
                <span class="grade-qty">{{ $est->quantidade }}</span>
            </span>
            @endforeach
            <button onclick="openEstModal({{ $u->id }},null,'',0)" class="grade-chip grade-add">+</button>
        </div>
    </td>
    <td class="text-12">{{ $u->custo_unitario ? 'R$ '.number_format($u->custo_unitario,2,',','.') : '—' }}</td>
    <td><span class="badge {{ $u->status==='Ativo'?'badge-success':'badge-danger' }}">{{ $u->status }}</span></td>
    <td><div class="flex gap-4">
        <a href="{{ route('uniformes.edit',$u->id) }}" class="btn btn-secondary btn-icon"><i class="fas fa-pencil-alt"></i></a>
        <form method="POST" action="{{ route('uniformes.destroy',$u->id) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir?"><i class="fas fa-trash-alt"></i></button></form>
    </div></td>
</tr>
@empty
<tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fas fa-tshirt"></i></div><h3>Nenhum uniforme</h3></div></td></tr>
@endforelse
</tbody></table></div></div>

<div class="modal-overlay" id="estModal"><div class="modal modal-sm">
<div class="modal-header"><div class="modal-title"><i class="fas fa-ruler"></i> <span id="estTitle">Estoque por Tamanho</span></div><button class="modal-close" onclick="closeModal('estModal')"><i class="fas fa-times"></i></button></div>
<div class="modal-body"><form method="POST" id="estForm">@csrf
    <div class="flex flex-col gap-16">
        <div class="form-group"><label class="form-label">Tamanho *</label><select name="tamanho_id" id="estTam" class="form-select" required><option value="">Selecione</option>@foreach($tamanhos as $t)<option value="{{ $t->id }}">{{ $t->codigo }} — {{ $t->descricao }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Quantidade</label><input type="number" name="quantidade" id="estQty" class="form-control" min="0" value="0" required></div>
        <div class="form-group"><label class="form-label">Mínimo</label><input type="number" name="minimo" class="form-control" min="0" value="0"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal('estModal')">Cancelar</button><button type="submit" class="btn btn-primary">Salvar</button></div>
</form></div></div></div>
@endsection
@push('scripts')
<script>
function openEstModal(uniId,tamId,tamCod,qty){
    document.getElementById('estTitle').textContent='Estoque — '+tamCod;
    document.getElementById('estForm').action=`/uniformes/${uniId}/estoque`;
    document.getElementById('estTam').value=tamId||'';
    document.getElementById('estQty').value=qty||0;
    openModal('estModal');
}
</script>
@endpush
