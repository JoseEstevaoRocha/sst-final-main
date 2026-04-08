@extends('layouts.app')
@section('title','Manutenções NR12')
@php
$tipos = [
    'preventiva'            => ['label'=>'Preventiva',             'badge'=>'badge-info'],
    'corretiva'             => ['label'=>'Corretiva',              'badge'=>'badge-danger'],
    'preditiva'             => ['label'=>'Preditiva',              'badge'=>'badge-warning'],
    'inspecao'              => ['label'=>'Inspeção NR12',          'badge'=>'badge-secondary'],
    'prensa_preventiva'     => ['label'=>'Prensa — Preventiva',    'badge'=>'badge-info'],
    'prensa_corretiva'      => ['label'=>'Prensa — Corretiva',     'badge'=>'badge-danger'],
    'ferramenta_preventiva' => ['label'=>'Ferramenta — Preventiva','badge'=>'badge-info'],
    'ferramenta_corretiva'  => ['label'=>'Ferramenta — Corretiva', 'badge'=>'badge-danger'],
];
@endphp
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Manutenções NR12</h1>
        <p class="page-sub">Histórico e registro de manutenções</p>
    </div>
    <div class="flex gap-8">
        <button class="btn btn-secondary" onclick="openModal('modalImportar')">
            <i class="fas fa-file-import"></i> Importar CSV
        </button>
        <button class="btn btn-primary" onclick="openModal('modalManutencao')">
            <i class="fas fa-plus"></i> Nova Manutenção
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mb-16">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-16">{{ session('error') }}</div>
@endif

{{-- KPIs --}}
<div class="kpi-row mb-20" style="grid-template-columns:repeat(4,1fr)">
    <div class="kpi kpi-blue"><div class="kpi-label">Total</div><div class="kpi-val">{{ $stats['total'] }}</div></div>
    <div class="kpi kpi-green"><div class="kpi-label">Preventivas</div><div class="kpi-val">{{ $stats['preventiva'] }}</div></div>
    <div class="kpi kpi-red"><div class="kpi-label">Corretivas</div><div class="kpi-val">{{ $stats['corretiva'] }}</div></div>
    <div class="kpi kpi-yellow"><div class="kpi-label">Inspeções NR12</div><div class="kpi-val">{{ $stats['inspecao'] }}</div></div>
</div>

{{-- Filtros --}}
<form method="GET">
<div class="filter-bar mb-16" style="flex-wrap:wrap;gap:8px">
    @if(auth()->user()->hasRole('super-admin'))
    <select name="empresa_id" class="filter-select" style="width:200px" onchange="this.form.submit()">
        <option value="">Todas as empresas</option>
        @foreach($empresas as $emp)
        <option value="{{ $emp->id }}" {{ request('empresa_id')==$emp->id?'selected':'' }}>{{ $emp->nome_display }}</option>
        @endforeach
    </select>
    @endif
    <select name="setor_id" class="filter-select" style="width:180px" onchange="this.form.submit()">
        <option value="">Todos os setores</option>
        @foreach($setores as $s)
        <option value="{{ $s->id }}" {{ request('setor_id')==$s->id?'selected':'' }}>{{ $s->nome }}</option>
        @endforeach
    </select>
    <select name="tipo" class="filter-select" style="width:220px" onchange="this.form.submit()">
        <option value="">Todos os tipos</option>
        <optgroup label="Geral">
            <option value="preventiva" {{ request('tipo')==='preventiva'?'selected':'' }}>Preventiva</option>
            <option value="corretiva" {{ request('tipo')==='corretiva'?'selected':'' }}>Corretiva</option>
            <option value="preditiva" {{ request('tipo')==='preditiva'?'selected':'' }}>Preditiva</option>
            <option value="inspecao" {{ request('tipo')==='inspecao'?'selected':'' }}>Inspeção NR12</option>
        </optgroup>
        <optgroup label="Prensa">
            <option value="prensa_preventiva" {{ request('tipo')==='prensa_preventiva'?'selected':'' }}>Prensa — Preventiva</option>
            <option value="prensa_corretiva" {{ request('tipo')==='prensa_corretiva'?'selected':'' }}>Prensa — Corretiva</option>
        </optgroup>
        <optgroup label="Ferramenta">
            <option value="ferramenta_preventiva" {{ request('tipo')==='ferramenta_preventiva'?'selected':'' }}>Ferramenta — Preventiva</option>
            <option value="ferramenta_corretiva" {{ request('tipo')==='ferramenta_corretiva'?'selected':'' }}>Ferramenta — Corretiva</option>
        </optgroup>
    </select>
    @if(request()->hasAny(['empresa_id','setor_id','tipo']))
    <a href="{{ route('manutencoes.index') }}" class="btn btn-ghost btn-sm">✕ Limpar</a>
    @endif
</div>
</form>

{{-- Tabela --}}
<div class="card p-0">
    <div class="table-wrap"><table class="table">
    <thead><tr><th>MÁQUINA</th><th>SETOR</th><th>TIPO</th><th>DATA</th><th>DURAÇÃO</th><th>RESPONSÁVEL</th><th>DESCRIÇÃO</th><th></th></tr></thead>
    <tbody>
    @forelse($manutencoes as $man)
    <tr>
        <td>
            <div class="font-bold text-13">{{ $man->maquina?->nome ?? '—' }}</div>
            <div class="text-11 text-muted">{{ $man->maquina?->empresa?->nome_display ?? '' }}</div>
        </td>
        <td class="text-12">{{ $man->maquina?->setor?->nome ?? '—' }}</td>
        <td>
            <span class="badge {{ $tipos[$man->tipo]['badge']??'badge-secondary' }}">
                {{ $tipos[$man->tipo]['label']??ucfirst($man->tipo) }}
            </span>
        </td>
        <td class="font-mono text-12">{{ $man->data_manutencao->format('d/m/Y') }}
            @if($man->hora_inicio)<div class="text-11 text-muted">{{ substr($man->hora_inicio,0,5) }} → {{ substr($man->hora_fim??'',0,5) }}</div>@endif
        </td>
        <td class="text-12 font-mono">
            @if($man->duracao_minutos !== null)
                @if($man->duracao_minutos >= 60)
                    {{ intdiv($man->duracao_minutos,60) }}h {{ $man->duracao_minutos % 60 }}min
                @else
                    {{ $man->duracao_minutos }} min
                @endif
            @else —
            @endif
        </td>
        <td class="text-12">{{ $man->responsavel ?? '—' }}</td>
        <td class="text-12" style="max-width:220px;white-space:normal">{{ $man->descricao ?? '—' }}</td>
        <td>
            <form method="POST" action="{{ route('manutencoes.destroy',$man->id) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-icon text-danger" data-confirm="Excluir registro?"><i class="fas fa-trash-alt"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="8">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-wrench"></i></div>
            <h3>Nenhuma manutenção registrada</h3>
            <p class="text-muted">Clique em "Nova Manutenção" para registrar ou importe um CSV.</p>
        </div>
    </td></tr>
    @endforelse
    </tbody></table></div>
    @if($manutencoes->hasPages())
    <div style="padding:12px 20px">{{ $manutencoes->links() }}</div>
    @endif
</div>

{{-- Modal Nova Manutenção --}}
<div class="modal-overlay" id="modalManutencao">
<div class="modal modal-lg">
<div class="modal-header">
    <div class="modal-title"><i class="fas fa-wrench"></i> Nova Manutenção</div>
    <button class="modal-close" onclick="closeModal('modalManutencao')"><i class="fas fa-times"></i></button>
</div>
<div class="modal-body">
<form method="POST" action="{{ route('manutencoes.geral.store') }}">@csrf
<div class="form-grid">

    @if(auth()->user()->hasRole('super-admin'))
    <div class="form-group">
        <label class="form-label">Empresa *</label>
        <select id="modalEmpresa" class="form-select" required onchange="loadMaquinas()">
            <option value="">Selecione</option>
            @foreach($empresas as $emp)
            <option value="{{ $emp->id }}">{{ $emp->nome_display }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="form-group">
        <label class="form-label">Máquina *</label>
        <select name="maquina_id" id="modalMaquina" class="form-select" required>
            <option value="">{{ auth()->user()->hasRole('super-admin') ? 'Selecione a empresa primeiro' : 'Carregando...' }}</option>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Tipo *</label>
        <select name="tipo" class="form-select" required>
            <optgroup label="Geral">
                <option value="preventiva">Preventiva</option>
                <option value="corretiva">Corretiva</option>
                <option value="preditiva">Preditiva</option>
                <option value="inspecao">Inspeção NR12</option>
            </optgroup>
            <optgroup label="Prensa">
                <option value="prensa_preventiva">Prensa — Preventiva</option>
                <option value="prensa_corretiva">Prensa — Corretiva</option>
            </optgroup>
            <optgroup label="Ferramenta">
                <option value="ferramenta_preventiva">Ferramenta — Preventiva</option>
                <option value="ferramenta_corretiva">Ferramenta — Corretiva</option>
            </optgroup>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Data da Manutenção *</label>
        <input type="date" name="data_manutencao" value="{{ date('Y-m-d') }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label class="form-label">Hora de Início</label>
        <input type="time" name="hora_inicio" id="horaInicioModal" class="form-control" oninput="calcDuracao('modal')">
    </div>

    <div class="form-group">
        <label class="form-label">Hora de Término</label>
        <input type="time" name="hora_fim" id="horaFimModal" class="form-control" oninput="calcDuracao('modal')">
    </div>

    <div class="form-group">
        <label class="form-label">Duração</label>
        <div id="duracaoModal" class="form-control" style="background:var(--bg-secondary);color:var(--text-muted);cursor:default">—</div>
    </div>

    <div class="form-group form-full">
        <label class="form-label">Responsável</label>
        <input type="text" name="responsavel" class="form-control" placeholder="Nome do técnico / empresa">
    </div>

    <div class="form-group form-full">
        <label class="form-label">Descrição / Serviços Realizados</label>
        <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva os serviços realizados..."></textarea>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-ghost" onclick="closeModal('modalManutencao')">Cancelar</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
</div>
</form>
</div>
</div>
</div>

{{-- Modal Importar CSV --}}
<div class="modal-overlay" id="modalImportar">
<div class="modal">
<div class="modal-header">
    <div class="modal-title"><i class="fas fa-file-import"></i> Importar Manutenções</div>
    <button class="modal-close" onclick="closeModal('modalImportar')"><i class="fas fa-times"></i></button>
</div>
<div class="modal-body">
    <div style="background:var(--bg-secondary);border-radius:var(--r-sm);padding:14px;margin-bottom:16px;font-size:13px">
        <div style="font-weight:600;margin-bottom:8px">Formato do arquivo CSV (separado por ponto-e-vírgula):</div>
        <code style="font-size:11px;display:block;line-height:1.8">
            empresa_cnpj ; maquina_nome ; tipo ; data_manutencao ; duracao_minutos ; descricao ; responsavel
        </code>
        <div style="margin-top:8px;color:var(--text-muted);font-size:12px">
            <strong>Tipos válidos:</strong> preventiva, corretiva, preditiva, inspecao, prensa_preventiva, prensa_corretiva, ferramenta_preventiva, ferramenta_corretiva<br>
            <strong>Data:</strong> AAAA-MM-DD ou DD/MM/AAAA<br>
            @if(!auth()->user()->hasRole('super-admin'))
            <strong>empresa_cnpj:</strong> pode deixar em branco (usa sua empresa automaticamente)
            @endif
        </div>
        <a href="{{ route('manutencoes.modelo-csv') }}" class="btn btn-ghost btn-sm" style="margin-top:10px">
            <i class="fas fa-download"></i> Baixar modelo CSV
        </a>
    </div>
    <form method="POST" action="{{ route('manutencoes.importar') }}" enctype="multipart/form-data">@csrf
        <div class="form-group">
            <label class="form-label">Arquivo CSV *</label>
            <input type="file" name="arquivo" class="form-control" accept=".csv,.txt" required>
        </div>
        <div class="modal-footer" style="padding:0;margin-top:16px">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalImportar')">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Importar</button>
        </div>
    </form>
</div>
</div>
</div>

@endsection
@push('scripts')
<script>
function calcDuracao(prefix) {
    const inicio = document.getElementById('horaInicio'+prefix.charAt(0).toUpperCase()+prefix.slice(1))?.value;
    const fim    = document.getElementById('horaFim'+prefix.charAt(0).toUpperCase()+prefix.slice(1))?.value;
    const el     = document.getElementById('duracao'+prefix.charAt(0).toUpperCase()+prefix.slice(1));
    if (!el) return;
    if (!inicio || !fim) { el.textContent = '—'; return; }
    const [ih,im] = inicio.split(':').map(Number);
    let   [fh,fm] = fim.split(':').map(Number);
    let total = (fh*60+fm) - (ih*60+im);
    if (total < 0) total += 1440; // virou meia-noite
    const h = Math.floor(total/60), m = total%60;
    el.textContent = h > 0 ? `${h}h ${m}min (${total} min)` : `${total} min`;
}

const isSuperAdmin = {{ auth()->user()->hasRole('super-admin') ? 'true' : 'false' }};

async function loadMaquinas(empresaId) {
    const eid = empresaId ?? (isSuperAdmin ? document.getElementById('modalEmpresa')?.value : '');
    const sel = document.getElementById('modalMaquina');
    if (isSuperAdmin && !eid) { sel.innerHTML = '<option value="">Selecione a empresa primeiro</option>'; return; }
    sel.innerHTML = '<option value="">Carregando...</option>';
    const r = await fetch(`/api/maquinas${eid ? '?empresa_id='+eid : ''}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await r.json();
    sel.innerHTML = '<option value="">Selecione a máquina</option>';
    data.forEach(m => {
        const o = document.createElement('option');
        o.value = m.id;
        o.textContent = m.nome + (m.numero_serie ? ' (S/N: '+m.numero_serie+')' : '');
        sel.appendChild(o);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Ao abrir modal de nova manutenção: carrega máquinas automaticamente para usuário normal
    document.querySelector('[onclick="openModal(\'modalManutencao\')"]')?.addEventListener('click', () => {
        if (!isSuperAdmin) loadMaquinas();
    });
});
</script>
@endpush
