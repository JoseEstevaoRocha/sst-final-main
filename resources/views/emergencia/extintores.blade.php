@extends('layouts.app')
@section('title','Extintores')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Controle de Extintores</h1><p class="page-sub">{{ $extintores->total() }} extintores</p></div>
    <a href="{{ route('extintores.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Extintor</a>
</div>
<div class="kpi-row mb-20" style="grid-template-columns:repeat(4,1fr)">
    @foreach([['Total','total','blue'],['Regulares','regulares','green'],['Vencidos','vencidos','red'],['Manutenção','manutencao','yellow']] as [$l,$k,$c])
    <div class="kpi kpi-{{ $c }}"><div class="kpi-label">{{ $l }}</div><div class="kpi-val">{{ $stats[$k]??0 }}</div></div>
    @endforeach
</div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>Nº SÉRIE</th><th>TIPO</th><th>SETOR</th><th>LOCALIZAÇÃO</th><th>ÚLTIMA RECARGA</th><th>PRÓXIMA RECARGA</th><th>STATUS</th><th>AÇÕES</th></tr></thead>
<tbody>
@forelse($extintores as $e)
@php $s=$e->status_calculado; @endphp
<tr class="{{ $s==='vencido'?'tr-danger':'' }}">
    <td class="font-mono text-12">{{ $e->numero_serie??'—' }}</td>
    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$e->tipo)) }}</span></td>
    <td class="text-12">{{ $e->setor?->nome??'—' }}</td>
    <td class="text-12">{{ Str::limit($e->localizacao??'—',30) }}</td>
    <td class="font-mono text-12">{{ $e->ultima_recarga?->format('d/m/Y')??'—' }}</td>
    <td class="font-mono text-12 {{ $s==='vencido'?'text-danger':'' }}">{{ $e->proxima_recarga?->format('d/m/Y')??'—' }}</td>
    <td><span class="badge {{ $s==='regular'?'badge-success':($s==='vencido'?'badge-danger':'badge-warning') }}">{{ ucfirst($s) }}</span></td>
    <td><div class="flex gap-4">
        <a href="{{ route('extintores.edit',$e->id) }}" class="btn btn-secondary btn-icon"><i class="fas fa-pencil-alt"></i></a>
        <form method="POST" action="{{ route('extintores.destroy',$e->id) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir?"><i class="fas fa-trash-alt"></i></button></form>
    </div></td>
</tr>
@empty
<tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><i class="fas fa-fire-extinguisher"></i></div><h3>Nenhum extintor</h3></div></td></tr>
@endforelse
</tbody></table></div></div>
@endsection
