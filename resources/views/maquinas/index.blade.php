@extends('layouts.app')
@section('title','Máquinas NR12')
@section('content')
<div class="page-header"><div><h1 class="page-title">Inventário de Máquinas — NR12</h1><p class="page-sub">{{ $maquinas->total() }} máquinas cadastradas</p></div><a href="{{ route('maquinas.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Máquina</a></div>
<div class="kpi-row mb-20" style="grid-template-columns:repeat(3,1fr)">
    @foreach([['Total','total','blue'],['Operacionais','operacionais','green'],['Inativas','inativas','secondary']] as [$l,$k,$c])
    <div class="kpi kpi-{{ $c }}"><div class="kpi-label">{{ $l }}</div><div class="kpi-val">{{ $stats[$k]??0 }}</div></div>
    @endforeach
</div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>MÁQUINA</th><th>SETOR</th><th>Nº SÉRIE</th><th>ANO</th><th>STATUS</th><th>AÇÕES</th></tr></thead>
<tbody>
@forelse($maquinas as $m)
<tr>
    <td><div class="font-bold text-13">{{ $m->nome }}</div><div class="text-11 text-muted">{{ $m->marca }}{{ $m->modelo?' — '.$m->modelo:'' }}</div></td>
    <td class="text-12">{{ $m->setor?->nome??'—' }}</td>
    <td class="font-mono text-11">{{ $m->numero_serie??'—' }}</td>
    <td class="text-12">{{ $m->ano_fabricacao??'—' }}</td>
    <td><span class="badge {{ ['operacional'=>'badge-success','inativo'=>'badge-danger'][$m->status]??'badge-secondary' }}">{{ ucfirst($m->status) }}</span></td>
    <td><div class="flex gap-4"><a href="{{ route('maquinas.manutencoes.index',['maquina'=>$m->id]) }}" class="btn btn-warning btn-icon" title="Manutenções"><i class="fas fa-wrench"></i></a><a href="{{ route('maquinas.edit',$m->id) }}" class="btn btn-secondary btn-icon"><i class="fas fa-pencil-alt"></i></a><form method="POST" action="{{ route('maquinas.destroy',$m->id) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir?"><i class="fas fa-trash-alt"></i></button></form></div></td>
</tr>
@empty
<tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><i class="fas fa-cogs"></i></div><h3>Nenhuma máquina</h3></div></td></tr>
@endforelse
</tbody></table></div></div>
@endsection
