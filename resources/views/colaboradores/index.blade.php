@extends('layouts.app')
@section('title','Colaboradores')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Colaboradores</h1>
        <p class="page-sub">{{ $cols->total() }} colaborador(es) encontrado(s)</p>
    </div>
    <div class="flex gap-8">
        <a href="{{ route('importacao.index') }}" class="btn btn-secondary"><i class="fas fa-file-import"></i> Importar</a>
        <a href="{{ route('colaboradores.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Colaborador</a>
    </div>
</div>

<form method="GET" action="{{ route('colaboradores.index') }}">
<div class="filter-bar">
    <div class="filter-search-wrap"><i class="fas fa-search"></i><input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, CPF ou matrícula..." class="filter-input filter-input-icon" style="width:240px"></div>
    <select name="setor_id" class="filter-select" onchange="this.form.submit()" style="width:180px">
        <option value="">Todos os setores</option>
        @foreach($setores as $s)<option value="{{ $s->id }}" {{ request('setor_id')==$s->id?'selected':'' }}>{{ $s->nome }}</option>@endforeach
    </select>
    <select name="status" class="filter-select" onchange="this.form.submit()" style="width:150px">
        <option value="">Todos os status</option>
        @foreach(['Contratado','Demitido','Afastado','INSS'] as $st)<option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-filter"></i></button>
    @if(request()->hasAny(['search','setor_id','status']))<a href="{{ route('colaboradores.index') }}" class="btn btn-ghost btn-sm">✕</a>@endif
    <a href="{{ route('colaboradores.index') }}?export=1" class="btn btn-ghost btn-sm ml-auto"><i class="fas fa-download"></i> Exportar</a>
</div>
</form>

<div class="bulk-bar" id="bulkBar">
    <span id="bulkCount">0</span> selecionado(s)
    <button onclick="bulkDelete()" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Excluir</button>
</div>

<div class="card p-0">
<div class="table-wrap">
<table class="table">
<thead><tr>
    <th style="width:40px"><input type="checkbox" id="selectAll" style="accent-color:var(--brand)"></th>
    <th data-sort="nome">COLABORADOR</th>
    <th>CPF</th>
    <th>EMPRESA</th>
    <th>SETOR / FUNÇÃO</th>
    <th>SEXO</th>
    <th data-sort="data_admissao">ADMISSÃO</th>
    <th>STATUS</th>
    <th style="width:110px">AÇÕES</th>
</tr></thead>
<tbody>
@forelse($cols as $c)
<tr>
    <td><input type="checkbox" class="row-check" value="{{ $c->id }}" style="accent-color:var(--brand)"></td>
    <td>
        <div class="flex gap-10 align-center">
            <div class="avatar-sm">{{ $c->initials }}</div>
            <div>
                <div class="font-bold text-13">{{ $c->nome }}</div>
                @if($c->matricula)<div class="text-11 text-muted">{{ $c->matricula }}</div>@endif
            </div>
        </div>
    </td>
    <td class="font-mono text-12">{{ $c->cpf }}</td>
    <td class="text-12">{{ $c->empresa->nome_display ?? '—' }}</td>
    <td>
        <div class="font-bold text-13">{{ $c->setor->nome ?? '—' }}</div>
        <div class="text-11 text-muted">{{ $c->funcao->nome ?? '—' }}</div>
    </td>
    <td><span class="badge {{ $c->sexo==='M' ? 'badge-info' : 'badge-purple' }}">{{ $c->sexo }}</span></td>
    <td class="font-mono text-12">{{ $c->data_admissao?->format('d/m/Y') ?? '—' }}</td>
    <td>
        @php $badgeMap=['Contratado'=>'badge-success','Demitido'=>'badge-danger','Afastado'=>'badge-warning','INSS'=>'badge-secondary']; @endphp
        <span class="badge {{ $badgeMap[$c->status] ?? 'badge-secondary' }}">{{ $c->status }}</span>
    </td>
    <td>
        <div class="flex gap-4">
            <a href="{{ route('ficha.show',$c->id) }}" class="btn btn-ghost btn-icon" title="Ficha"><i class="fas fa-id-card"></i></a>
            <a href="{{ route('colaboradores.edit',$c->id) }}" class="btn btn-secondary btn-icon" title="Editar"><i class="fas fa-pencil-alt"></i></a>
            <form method="POST" action="{{ route('colaboradores.destroy',$c->id) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir {{ $c->nome }}?" title="Excluir"><i class="fas fa-trash-alt"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="9"><div class="empty-state"><div class="empty-icon"><i class="fas fa-users"></i></div><h3>Nenhum colaborador</h3><p>Ajuste os filtros ou cadastre um novo.</p></div></td></tr>
@endforelse
</tbody>
</table>
</div>
@if($cols->hasPages())
<div class="pagination-bar">
    <span class="pag-info">{{ $cols->firstItem() }}–{{ $cols->lastItem() }} de {{ $cols->total() }}</span>
    <div class="pagination">
        @if(!$cols->onFirstPage())<a href="{{ $cols->previousPageUrl() }}" class="page-btn">‹</a>@else<span class="page-btn disabled">‹</span>@endif
        @foreach($cols->getUrlRange(max(1,$cols->currentPage()-2),min($cols->lastPage(),$cols->currentPage()+2)) as $p=>$u)<a href="{{ $u }}" class="page-btn {{ $p==$cols->currentPage()?'active':'' }}">{{ $p }}</a>@endforeach
        @if($cols->hasMorePages())<a href="{{ $cols->nextPageUrl() }}" class="page-btn">›</a>@else<span class="page-btn disabled">›</span>@endif
    </div>
</div>
@endif
</div>

<form id="bulkForm" method="POST" action="{{ route('colaboradores.bulk-destroy') }}">
    @csrf @method('DELETE')
    <div id="bulkInputs"></div>
</form>
@endsection
@push('scripts')
<script>
function bulkDelete(){
    const ids=getSelectedIds();
    if(!ids.length)return;
    if(!confirm(`Excluir ${ids.length} colaborador(es)? Esta ação não pode ser desfeita.`))return;
    const f=document.getElementById('bulkForm'),c=document.getElementById('bulkInputs');
    c.innerHTML='';ids.forEach(id=>{const i=document.createElement('input');i.type='hidden';i.name='ids[]';i.value=id;c.appendChild(i);});
    f.submit();
}
</script>
@endpush
