@extends('layouts.app')
@section('title','Gestão de ASO')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Gestão de ASO</h1><p class="page-sub">Atestados de Saúde Ocupacional</p></div>
    <a href="{{ route('asos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo ASO</a>
</div>

<div class="kpi-row mb-20" style="grid-template-columns:repeat(5,1fr)">
    @foreach([['Total','total','blue','fas fa-clipboard-list'],['Em Dia','em_dia','green','fas fa-check-circle'],['Vencidos','vencidos','red','fas fa-times-circle'],['A Vencer (30d)','a_vencer','yellow','fas fa-clock'],['Agendados','agendados','cyan','fas fa-calendar-check']] as [$l,$k,$c,$i])
    <div class="kpi kpi-{{ $c }} {{ $k==='vencidos'&&$stats[$k]>0?'kpi-pulse':'' }}">
        <div class="kpi-icon"><i class="{{ $i }}"></i></div>
        <div class="kpi-label">{{ $l }}</div>
        <div class="kpi-val">{{ $stats[$k] }}</div>
    </div>
    @endforeach
</div>

<form method="GET">
<div class="filter-bar">
    <div class="filter-search-wrap"><i class="fas fa-search"></i><input type="text" name="search" value="{{ request('search') }}" placeholder="Nome do colaborador..." class="filter-input filter-input-icon" style="width:220px"></div>
    <select name="tipo" class="filter-select" onchange="this.form.submit()">
        <option value="">Todos os tipos</option>
        @foreach(['admissional'=>'Admissional','periodico'=>'Periódico','demissional'=>'Demissional','retorno'=>'Retorno ao Trabalho','mudanca_funcao'=>'Mudança de Função'] as $v=>$l)
        <option value="{{ $v }}" {{ request('tipo')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <select name="status" class="filter-select" onchange="this.form.submit()" style="width:180px">
        <option value="">Todos os status</option>
        @foreach(['pendente'=>'Pendente','agendado'=>'Agendado','em_atendimento'=>'Em Atendimento','finalizado'=>'Finalizado','em_transito'=>'Em Trânsito','recebido_empresa'=>'Recebido Empresa','entregue_colaborador'=>'Entregue Colaborador'] as $v=>$l)
        <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <select name="resultado" class="filter-select" onchange="this.form.submit()">
        <option value="">Qualquer resultado</option>
        @foreach(['pendente'=>'Pendente','apto'=>'Apto','apto_restricoes'=>'Apto c/ Restrições','inapto'=>'Inapto'] as $v=>$l)
        <option value="{{ $v }}" {{ request('resultado')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-filter"></i></button>
    @if(request()->hasAny(['search','tipo','status','resultado']))<a href="{{ route('asos.index') }}" class="btn btn-ghost btn-sm">✕</a>@endif
</div>
</form>

<div class="card p-0">
<div class="table-wrap"><table class="table">
<thead><tr>
    <th>COLABORADOR</th><th>TIPO</th><th>DATA EXAME</th><th>VENCIMENTO</th>
    <th>RESULTADO</th><th>CLÍNICA</th><th>STATUS LOGÍSTICO</th><th>AÇÕES</th>
</tr></thead>
<tbody>
@forelse($asos as $a)
@php $dias=$a->dias_restantes; @endphp
<tr class="{{ $dias !== null && $dias < 0 ? 'tr-danger' : ($dias !== null && $dias <= 15 ? 'tr-warning' : '') }}">
    <td>
        <div class="font-bold text-13">{{ $a->colaborador->nome ?? '—' }}</div>
        <div class="text-11 text-muted">{{ $a->colaborador->funcao->nome ?? '' }} · {{ $a->empresa->nome_display ?? '' }}</div>
    </td>
    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$a->tipo)) }}</span></td>
    <td class="font-mono text-12">{{ $a->data_exame?->format('d/m/Y') ?? '—' }}</td>
    <td>
        <div class="font-mono text-12 {{ $dias !== null && $dias < 0 ? 'text-danger' : ($dias !== null && $dias <= 15 ? 'text-warning' : '') }}">{{ $a->data_vencimento?->format('d/m/Y') ?? '—' }}</div>
        @if($dias !== null)<div class="text-10 text-muted">{{ $dias < 0 ? abs($dias).'d vencido' : $dias.'d restantes' }}</div>@endif
    </td>
    <td>
        @php $rm=['pendente'=>'badge-secondary','apto'=>'badge-success','apto_restricoes'=>'badge-warning','inapto'=>'badge-danger']; @endphp
        <span class="badge {{ $rm[$a->resultado]??'badge-secondary' }}">{{ ucfirst(str_replace('_',' ',$a->resultado)) }}</span>
    </td>
    <td class="text-12">{{ $a->clinica_nome ?: '—' }}</td>
    <td>
        <form method="POST" action="{{ route('asos.logistica',$a->id) }}" style="display:inline">
            @csrf
            <select name="status_logistico" class="filter-select text-11" style="padding:4px 8px" onchange="this.form.submit()">
                @foreach(['pendente'=>'Pendente','agendado'=>'Agendado','em_atendimento'=>'Em Atendimento','finalizado'=>'Finalizado','em_transito'=>'Em Trânsito','recebido_empresa'=>'Recebido','entregue_colaborador'=>'Entregue'] as $v=>$l)
                <option value="{{ $v }}" {{ $a->status_logistico===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </form>
    </td>
    <td>
        <div class="flex gap-4">
            <a href="{{ route('asos.edit',$a->id) }}" class="btn btn-secondary btn-icon"><i class="fas fa-pencil-alt"></i></a>
            <a href="{{ route('whatsapp.index') }}?aso={{ $a->id }}" class="btn btn-ghost btn-icon" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            <form method="POST" action="{{ route('asos.destroy',$a->id) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir este ASO?"><i class="fas fa-trash-alt"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><i class="fas fa-clipboard-list"></i></div><h3>Nenhum ASO encontrado</h3></div></td></tr>
@endforelse
</tbody></table></div>
@if($asos->hasPages())
<div class="pagination-bar">
    <span class="pag-info">{{ $asos->firstItem() }}–{{ $asos->lastItem() }} de {{ $asos->total() }}</span>
    <div class="pagination">
        @if(!$asos->onFirstPage())<a href="{{ $asos->previousPageUrl() }}" class="page-btn">‹</a>@else<span class="page-btn disabled">‹</span>@endif
        @foreach($asos->getUrlRange(max(1,$asos->currentPage()-2),min($asos->lastPage(),$asos->currentPage()+2)) as $p=>$u)<a href="{{ $u }}" class="page-btn {{ $p==$asos->currentPage()?'active':'' }}">{{ $p }}</a>@endforeach
        @if($asos->hasMorePages())<a href="{{ $asos->nextPageUrl() }}" class="page-btn">›</a>@else<span class="page-btn disabled">›</span>@endif
    </div>
</div>
@endif
</div>
@endsection
