@extends('layouts.app')
@section('title','Ficha — '.$colab->nome)
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Ficha do Funcionário</h1></div>
    <div class="flex gap-8">
        <a href="{{ route('ficha.index') }}" class="btn btn-secondary">← Voltar</a>
        <a href="{{ route('ficha.pdf',$colab->id) }}" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> PDF</a>
        <a href="{{ route('colaboradores.edit',$colab->id) }}" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>
    </div>
</div>

{{-- Header --}}
<div class="card mb-20">
    <div class="flex align-center gap-20 mb-20">
        <div class="avatar-xl">{{ $colab->initials }}</div>
        <div>
            <h2 style="font-size:22px;font-weight:800">{{ $colab->nome }}</h2>
            <div class="flex flex-wrap gap-12 mt-8 text-13 text-muted">
                <span><i class="fas fa-briefcase"></i> {{ $colab->funcao->nome??'—' }}</span>
                <span><i class="fas fa-layer-group"></i> {{ $colab->setor->nome??'—' }}</span>
                <span><i class="fas fa-building"></i> {{ $colab->empresa->nome_display??'—' }}</span>
            </div>
        </div>
    </div>
    <div class="ficha-grid">
        @foreach([['CPF',$colab->cpf],['PIS',$colab->pis??'—'],['Matrícula',$colab->matricula??'—'],['eSocial',$colab->matricula_esocial??'—'],['CBO',$colab->cbo??'—'],['Nascimento',$colab->data_nascimento?->format('d/m/Y')??'—'],['Idade',$resumo['idadeAnos'].' anos'],['Sexo',$colab->sexo==='M'?'Masculino':'Feminino'],['Admissão',$colab->data_admissao?->format('d/m/Y')??'—'],['Tempo',$resumo['tempoMeses']<12?$resumo['tempoMeses'].'m':floor($resumo['tempoMeses']/12).'a '.($resumo['tempoMeses']%12).'m'],['Escolaridade',$colab->escolaridade??'—'],['Status',$colab->status]] as [$k,$v])
        <div class="ficha-dado"><div class="ficha-label">{{ $k }}</div><div class="ficha-val">{{ $v }}</div></div>
        @endforeach
    </div>
</div>

{{-- KPIs --}}
<div class="kpi-row mb-20" style="grid-template-columns:repeat(5,1fr)">
    <div class="kpi kpi-{{ $resumo['asoVencido']?'red':'green' }}"><div class="kpi-label">ASOs</div><div class="kpi-val">{{ $asos->count() }}</div></div>
    <div class="kpi kpi-{{ $resumo['epiVencidos']>0?'red':'blue' }}"><div class="kpi-label">EPIs Ativos</div><div class="kpi-val">{{ $epiEntregas->count() }}</div></div>
    <div class="kpi kpi-red {{ $resumo['epiVencidos']>0?'kpi-pulse':'' }}"><div class="kpi-label">EPIs Vencidos</div><div class="kpi-val">{{ $resumo['epiVencidos'] }}</div></div>
    <div class="kpi kpi-purple"><div class="kpi-label">Uniformes</div><div class="kpi-val">{{ $resumo['totalUniformes'] }}</div></div>
    <div class="kpi kpi-cyan"><div class="kpi-label">WhatsApp</div><div class="kpi-val">{{ $waMsgs->count() }}</div></div>
</div>

{{-- Tabs --}}
<div class="tabs mb-16">
    <button class="tab-btn active" data-tab="epi">🦺 EPI ({{ $epiEntregas->count() }})</button>
    <button class="tab-btn" data-tab="uni">👕 Uniformes ({{ $uniEntregas->count() }})</button>
    <button class="tab-btn" data-tab="aso">📋 ASO ({{ $asos->count() }})</button>
    <button class="tab-btn" data-tab="wpp">💬 WhatsApp ({{ $waMsgs->count() }})</button>
</div>

{{-- EPI --}}
<div id="tab-epi" class="tc active">
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>QTD</th><th>EPI</th><th>CA</th><th>DATA ENTREGA</th><th>PREV. TROCA</th><th>STATUS</th></tr></thead>
<tbody>
@forelse($epiEntregas as $e)
@php $s=$e->data_prevista_troca?($e->data_prevista_troca->isPast()?'Vencido':($e->data_prevista_troca->lte(today()->addDays(30))?'A Vencer':'Ativo')):'Ativo'; @endphp
<tr class="{{ $s==='Vencido'?'tr-danger':($s==='A Vencer'?'tr-warning':'') }}">
    <td class="font-bold text-16" style="color:var(--brand)">{{ $e->quantidade }}</td>
    <td><div class="font-bold text-13">{{ $e->epi->nome??'—' }}</div><div class="text-11 text-muted">{{ $e->epi->tipo??'' }}</div></td>
    <td class="font-mono text-11">{{ $e->epi->numero_ca??'—' }}</td>
    <td class="font-mono text-12">{{ $e->data_entrega->format('d/m/Y') }}</td>
    <td class="font-mono text-12 {{ $s==='Vencido'?'text-danger':($s==='A Vencer'?'text-warning':'') }}">{{ $e->data_prevista_troca?->format('d/m/Y')??'—' }}</td>
    <td><span class="badge {{ ['Ativo'=>'badge-success','A Vencer'=>'badge-warning','Vencido'=>'badge-danger'][$s] }}">{{ $s }}</span></td>
</tr>
@empty
<tr><td colspan="6"><div class="empty-state py-24"><p>Nenhum EPI registrado</p></div></td></tr>
@endforelse
</tbody></table></div></div>
</div>

{{-- Uniforme --}}
<div id="tab-uni" class="tc">
<div class="card mb-16" style="border-left:3px solid var(--brand)">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--brand)">{{ $colab->empresa->razao_social??'' }}</div>
    <div style="font-size:17px;font-weight:800;margin-top:2px">Termo de Responsabilidade — Uniforme</div>
    <div style="font-size:12px;color:var(--text-2);margin-top:8px;line-height:1.7;font-style:italic">Declaro ter recebido da empresa os uniformes abaixo relacionados, comprometendo-me a mantê-los em bom estado de conservação e limpeza.</div>
</div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>DATA</th><th>QTD</th><th>UNIFORME</th><th>TAMANHO</th><th>MOTIVO</th></tr></thead>
<tbody>
@forelse($uniEntregas as $e)
<tr><td class="font-mono text-12">{{ $e->data_entrega->format('d/m/Y') }}</td><td class="font-bold text-16" style="color:var(--brand)">{{ $e->quantidade }}</td><td class="font-bold text-13">{{ $e->uniforme->nome??'—' }}</td><td><span class="badge badge-info">{{ $e->tamanho->codigo??'—' }}</span></td><td class="text-12">{{ ucfirst($e->motivo??'—') }}</td></tr>
@empty
<tr><td colspan="5"><div class="empty-state py-24"><p>Nenhum uniforme registrado</p></div></td></tr>
@endforelse
</tbody></table></div></div>
</div>

{{-- ASO --}}
<div id="tab-aso" class="tc">
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>TIPO</th><th>DATA EXAME</th><th>VENCIMENTO</th><th>RESULTADO</th><th>CLÍNICA</th></tr></thead>
<tbody>
@forelse($asos as $a)
<tr class="{{ $a->dias_restantes !== null && $a->dias_restantes < 0 ? 'tr-danger' : '' }}">
    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$a->tipo)) }}</span></td>
    <td class="font-mono text-12">{{ $a->data_exame?->format('d/m/Y')??'—' }}</td>
    <td class="font-mono text-12 {{ $a->dias_restantes !== null && $a->dias_restantes < 0 ? 'text-danger' : '' }}">{{ $a->data_vencimento?->format('d/m/Y')??'—' }}</td>
    <td><span class="badge {{ ['apto'=>'badge-success','inapto'=>'badge-danger','pendente'=>'badge-secondary','apto_restricoes'=>'badge-warning'][$a->resultado]??'badge-secondary' }}">{{ ucfirst(str_replace('_',' ',$a->resultado)) }}</span></td>
    <td class="text-12">{{ $a->clinica_nome??'—' }}</td>
</tr>
@empty
<tr><td colspan="5"><div class="empty-state py-24"><p>Nenhum ASO</p></div></td></tr>
@endforelse
</tbody></table></div></div>
</div>

{{-- WhatsApp --}}
<div id="tab-wpp" class="tc">
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>DATA</th><th>TIPO EXAME</th><th>CLÍNICA</th><th>STATUS</th></tr></thead>
<tbody>
@forelse($waMsgs as $m)
<tr><td class="font-mono text-12">{{ $m->data_envio?->format('d/m/Y H:i')??'—' }}</td><td class="text-12">{{ $m->tipo_exame??'—' }}</td><td class="text-12">{{ $m->clinica?->nome??'—' }}</td><td><span class="badge badge-secondary">{{ $m->status }}</span></td></tr>
@empty
<tr><td colspan="4"><div class="empty-state py-24"><p>Nenhuma mensagem</p></div></td></tr>
@endforelse
</tbody></table></div></div>
</div>
@endsection
@push('scripts')
<script>document.querySelectorAll('.tab-btn').forEach(b=>{b.addEventListener('click',()=>{document.querySelectorAll('.tab-btn').forEach(x=>x.classList.remove('active'));document.querySelectorAll('.tc').forEach(x=>x.classList.remove('active'));b.classList.add('active');document.getElementById('tab-'+b.dataset.tab).classList.add('active');});});</script>
@endpush
