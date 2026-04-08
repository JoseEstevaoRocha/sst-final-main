@extends('layouts.app')
@section('title','Tamanhos')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Tamanhos</h1><p class="page-sub">Grade completa de tamanhos para uniformes</p></div>
    <div class="flex gap-8">
        <form method="POST" action="{{ route('tamanhos.seed') }}">@csrf<button type="submit" class="btn btn-secondary" data-confirm="Criar grade padrão (PP→XG)?"><i class="fas fa-magic"></i> Criar padrão</button></form>
        <button class="btn btn-primary" onclick="openModal('tamModal')"><i class="fas fa-plus"></i> Novo Tamanho</button>
    </div>
</div>
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><div class="card-title"><i class="fas fa-ruler"></i> Grade Visual</div></div>
    <div class="grade-chips" style="gap:10px">
        @foreach($tamanhos as $t)
        <span class="grade-chip grade-ok" style="cursor:pointer;min-width:80px" onclick="editTam({{ json_encode($t) }})">
            <span class="grade-code">{{ $t->codigo }}</span>
            @if($t->descricao)<span style="font-size:9px;opacity:.7;text-align:center">{{ $t->descricao }}</span>@endif
        </span>
        @endforeach
        <button onclick="openModal('tamModal')" class="grade-chip" style="border-style:dashed;background:none;cursor:pointer"><span style="font-size:20px">+</span></button>
    </div>
</div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>ORDEM</th><th>CÓDIGO</th><th>DESCRIÇÃO</th><th>AÇÕES</th></tr></thead>
<tbody>
@forelse($tamanhos as $t)
<tr>
    <td class="font-mono text-muted text-12">{{ $t->ordem }}</td>
    <td class="font-bold">{{ $t->codigo }}</td>
    <td class="text-12">{{ $t->descricao??'—' }}</td>
    <td><div class="flex gap-4">
        <button onclick="editTam({{ json_encode($t) }})" class="btn btn-secondary btn-icon"><i class="fas fa-pencil-alt"></i></button>
        <form method="POST" action="{{ route('tamanhos.destroy',$t->id) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir {{ $t->codigo }}?"><i class="fas fa-trash-alt"></i></button></form>
    </div></td>
</tr>
@empty
<tr><td colspan="4"><div class="empty-state"><p>Nenhum tamanho. Clique em "Criar padrão" para popular automaticamente.</p></div></td></tr>
@endforelse
</tbody></table></div></div>

<div class="modal-overlay" id="tamModal"><div class="modal modal-sm">
<div class="modal-header"><div class="modal-title" id="tamTitle"><i class="fas fa-ruler"></i> Novo Tamanho</div><button class="modal-close" onclick="closeModal('tamModal')"><i class="fas fa-times"></i></button></div>
<div class="modal-body"><form method="POST" id="tamForm" action="{{ route('tamanhos.store') }}">@csrf<div id="tamMethod"></div>
    <div class="flex flex-col gap-16">
        <div class="form-group"><label class="form-label">Código *</label><input type="text" name="codigo" id="tamCod" class="form-control" required placeholder="Ex: M, GG, 42..." style="text-transform:uppercase"></div>
        <div class="form-group"><label class="form-label">Descrição</label><input type="text" name="descricao" id="tamDesc" class="form-control" placeholder="Ex: Médio, Extra Grande..."></div>
        <div class="form-group"><label class="form-label">Ordem</label><input type="number" name="ordem" id="tamOrdem" class="form-control" value="{{ $tamanhos->count()+1 }}"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal('tamModal')">Cancelar</button><button type="submit" class="btn btn-primary">Salvar</button></div>
</form></div></div></div>
@endsection
@push('scripts')
<script>
function editTam(t){
    document.getElementById('tamTitle').innerHTML='<i class="fas fa-ruler"></i> Editar Tamanho';
    document.getElementById('tamForm').action=`/tamanhos/${t.id}`;
    document.getElementById('tamMethod').innerHTML='<input type="hidden" name="_method" value="PUT">';
    document.getElementById('tamCod').value=t.codigo;
    document.getElementById('tamDesc').value=t.descricao||'';
    document.getElementById('tamOrdem').value=t.ordem;
    openModal('tamModal');
}
</script>
@endpush
