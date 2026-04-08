@extends('layouts.app')
@section('title','Dashboard')
@push('styles')
<style>
/* ── DASHBOARD LAYOUT ───────────────────────────────────────────── */
.dash-grid{display:grid;gap:16px}
.dash-2{grid-template-columns:1fr 1fr}
.dash-3{grid-template-columns:1fr 1fr 1fr}
.dash-4{grid-template-columns:repeat(4,1fr)}
.dash-13{grid-template-columns:1fr 3fr}
.dash-31{grid-template-columns:3fr 1fr}

/* ── KPI CARDS ──────────────────────────────────────────────────── */
.kpi2{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--r);padding:20px;display:flex;flex-direction:column;gap:6px;position:relative;overflow:hidden;transition:box-shadow .2s,border-color .2s}
.kpi2:hover{box-shadow:var(--shadow-md);border-color:var(--brand)}
.kpi2::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.kpi2.k-blue::before{background:var(--brand)}
.kpi2.k-green::before{background:var(--success)}
.kpi2.k-red::before{background:var(--danger)}
.kpi2.k-yellow::before{background:var(--warning)}
.kpi2.k-cyan::before{background:#0891b2}
.kpi2.k-purple::before{background:#7c3aed}
.kpi2-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;margin-bottom:4px}
.k-blue .kpi2-icon{background:rgba(37,99,235,.12);color:var(--brand)}
.k-green .kpi2-icon{background:rgba(22,163,74,.12);color:var(--success)}
.k-red .kpi2-icon{background:rgba(220,38,38,.12);color:var(--danger)}
.k-yellow .kpi2-icon{background:rgba(217,119,6,.12);color:var(--warning)}
.k-cyan .kpi2-icon{background:rgba(8,145,178,.12);color:#0891b2}
.k-purple .kpi2-icon{background:rgba(124,58,237,.12);color:#7c3aed}
.kpi2-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3)}
.kpi2-val{font-size:32px;font-weight:800;color:var(--text-1);line-height:1}
.kpi2-sub{font-size:11px;color:var(--text-3);margin-top:2px}
.kpi2-trend{font-size:11px;font-weight:600;margin-top:4px}
.kpi2-trend.up{color:var(--success)}.kpi2-trend.down{color:var(--danger)}
.kpi2.pulse-red{animation:kpiPulse 2s infinite}
@keyframes kpiPulse{0%,100%{box-shadow:0 0 0 0 rgba(220,38,38,.3)}50%{box-shadow:0 0 0 8px rgba(220,38,38,0)}}

/* ── ALERTAS ────────────────────────────────────────────────────── */
.alert2{display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:var(--r-sm);margin-bottom:8px;font-size:13px;font-weight:500;text-decoration:none;transition:opacity .15s}
.alert2:hover{opacity:.85}
.alert2.a-danger{background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);color:var(--danger)}
.alert2.a-warning{background:rgba(217,119,6,.08);border:1px solid rgba(217,119,6,.2);color:var(--warning)}
.alert2-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px}
.a-danger .alert2-icon{background:rgba(220,38,38,.15)}.a-warning .alert2-icon{background:rgba(217,119,6,.15)}

/* ── CONFORMIDADE RING ──────────────────────────────────────────── */
.conf-ring-wrap{display:flex;align-items:center;justify-content:center;gap:24px;padding:16px 0}
.conf-ring{position:relative;width:120px;height:120px;flex-shrink:0}
.conf-ring svg{transform:rotate(-90deg)}
.conf-ring-text{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.conf-ring-pct{font-size:26px;font-weight:800;line-height:1}
.conf-ring-lbl{font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-top:2px}
.conf-stats{display:flex;flex-direction:column;gap:10px}
.conf-stat{display:flex;align-items:center;gap:10px}
.conf-stat-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.conf-stat-info{font-size:12px;color:var(--text-2)}
.conf-stat-num{font-size:16px;font-weight:700;color:var(--text-1)}

/* ── BARRA DE PROGRESSO ─────────────────────────────────────────── */
.prog-bar{height:6px;background:var(--bg-alt);border-radius:3px;overflow:hidden;margin-top:4px}
.prog-fill{height:100%;border-radius:3px;transition:width .6s ease}

/* ── LISTA DE SETORES ───────────────────────────────────────────── */
.setor-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)}
.setor-row:last-child{border-bottom:none}
.setor-avatar{width:34px;height:34px;border-radius:8px;background:rgba(37,99,235,.1);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0}
.setor-nome{font-size:13px;font-weight:600;color:var(--text-1)}
.setor-count{font-size:11px;color:var(--text-3)}

/* ── RISCO CHIPS ────────────────────────────────────────────────── */
.risco-chip{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:var(--r-sm);margin-bottom:6px;font-size:12px;font-weight:600}
.risco-chip.fisico{background:rgba(8,145,178,.1);color:#0891b2}
.risco-chip.quimico{background:rgba(124,58,237,.1);color:#7c3aed}
.risco-chip.biologico{background:rgba(22,163,74,.1);color:var(--success)}
.risco-chip.ergonomico{background:rgba(217,119,6,.1);color:var(--warning)}
.risco-chip.acidente{background:rgba(220,38,38,.1);color:var(--danger)}
.risco-chip-num{font-size:16px;font-weight:800}

/* ── EMPRESA INFO ────────────────────────────────────────────────── */
.emp-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;padding:4px 0}
.emp-info-item{}
.emp-info-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-3);margin-bottom:2px}
.emp-info-val{font-size:13px;font-weight:600;color:var(--text-1)}

/* ── TIMELINE ────────────────────────────────────────────────────── */
.timeline{padding:8px 0}
.tl-item{display:flex;gap:12px;padding:8px 0}
.tl-dot{width:10px;height:10px;border-radius:50%;background:var(--brand);flex-shrink:0;margin-top:4px}
.tl-content{font-size:12px;color:var(--text-2)}
.tl-name{font-weight:600;color:var(--text-1);font-size:13px}
.tl-date{font-size:11px;color:var(--text-3);margin-top:1px}

/* ── CIPA STATUS ─────────────────────────────────────────────────── */
.status-badge-big{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700}
.status-badge-big.ativa{background:rgba(22,163,74,.12);color:var(--success)}
.status-badge-big.vencida{background:rgba(220,38,38,.12);color:var(--danger)}
.status-badge-big.sem-dados{background:rgba(100,116,139,.12);color:var(--text-3)}

@media(max-width:1100px){.dash-4{grid-template-columns:1fr 1fr}.dash-3{grid-template-columns:1fr 1fr}.dash-2{grid-template-columns:1fr}.dash-13{grid-template-columns:1fr}.dash-31{grid-template-columns:1fr}}
@media(max-width:700px){.dash-4,.dash-3,.dash-2{grid-template-columns:1fr}.conf-ring-wrap{flex-direction:column}.emp-info-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')

{{-- HEADER ──────────────────────────────────────────────────────────────── --}}
<div class="page-header" style="margin-bottom:20px">
    <div>
        <h1 class="page-title">Painel de Controle SST</h1>
        <p class="page-sub">Saúde e Segurança do Trabalho — visão consolidada em tempo real</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        @if(auth()->user()->isSuperAdmin())
        <select class="filter-select" id="empFilter" style="width:220px">
            <option value="">Todas as empresas</option>
            @foreach($empresas as $e)
            <option value="{{ $e->id }}">{{ $e->nome_display }}</option>
            @endforeach
        </select>
        @endif
        <span style="font-size:11px;color:var(--text-3)"><i class="fas fa-circle" style="color:var(--success);font-size:7px"></i> Atualizado agora</span>
    </div>
</div>

{{-- ALERTAS ──────────────────────────────────────────────────────────────── --}}
@if(count($alertas) > 0)
<div class="card mb-16" style="border-left:3px solid var(--danger)">
    <div class="card-header" style="padding-bottom:12px">
        <div class="card-title" style="color:var(--danger)"><i class="fas fa-exclamation-triangle"></i> {{ count($alertas) }} Alerta(s) Crítico(s)</div>
        <a href="{{ route('dashboard.alertas') }}" class="btn btn-secondary btn-sm">Ver todos</a>
    </div>
    <div style="padding:0 16px 16px">
        @foreach($alertas as $a)
        <a href="{{ $a['link'] }}" class="alert2 a-{{ $a['nivel'] }}">
            <div class="alert2-icon"><i class="fas {{ $a['icon'] }}"></i></div>
            <span>{{ $a['msg'] }}</span>
            <i class="fas fa-chevron-right" style="margin-left:auto;font-size:10px;opacity:.5"></i>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- KPIs PRINCIPAIS ──────────────────────────────────────────────────────── --}}
<div class="dash-grid dash-4 mb-16">
    <div class="kpi2 k-blue">
        <div class="kpi2-icon"><i class="fas fa-users"></i></div>
        <div class="kpi2-label">Colaboradores Ativos</div>
        <div class="kpi2-val">{{ number_format($totalAtivos) }}</div>
        <div class="kpi2-sub">
            @if($totalAfastados > 0)<span style="color:var(--warning)">{{ $totalAfastados }} afastado(s)</span> · @endif
            {{ $admissoesRecentes }} admissão(ões) (30d)
        </div>
    </div>
    <div class="kpi2 {{ $asoVencidos > 0 ? 'k-red pulse-red' : 'k-green' }}">
        <div class="kpi2-icon"><i class="fas fa-clipboard-list"></i></div>
        <div class="kpi2-label">ASOs Vencidos</div>
        <div class="kpi2-val">{{ $asoVencidos }}</div>
        <div class="kpi2-sub">A vencer (30d): <strong>{{ $asoAVencer }}</strong> · Sem ASO: <strong>{{ $semAso }}</strong></div>
    </div>
    <div class="kpi2 {{ $epiVencidos > 0 ? 'k-red' : 'k-green' }}">
        <div class="kpi2-icon"><i class="fas fa-hard-hat"></i></div>
        <div class="kpi2-label">EPIs — Prazo Vencido</div>
        <div class="kpi2-val">{{ $epiVencidos }}</div>
        <div class="kpi2-sub">A vencer (30d): {{ $epiAVencer }} · Estoque zero: {{ $estoquesBaixos }}</div>
    </div>
    <div class="kpi2 {{ $extintVencidos > 0 ? 'k-red pulse-red' : 'k-cyan' }}">
        <div class="kpi2-icon"><i class="fas fa-fire-extinguisher"></i></div>
        <div class="kpi2-label">Extintores Vencidos</div>
        <div class="kpi2-val">{{ $extintVencidos }}</div>
        <div class="kpi2-sub">Total cadastrado: {{ $extintTotal }}</div>
    </div>
</div>

{{-- LINHA 2: EMPRESA + CONFORMIDADE ASO ─────────────────────────────────── --}}
<div class="dash-grid dash-2 mb-16">
    {{-- Informações da empresa --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-building"></i> Informações da Empresa</div>
            @if($empresa)
            <span class="badge {{ $empresa->status === 'ativa' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($empresa->status) }}</span>
            @endif
        </div>
        @if($empresa)
        <div style="padding:0 16px 16px">
            <div class="emp-info-grid">
                <div class="emp-info-item">
                    <div class="emp-info-label">Razão Social</div>
                    <div class="emp-info-val">{{ $empresa->razao_social }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Nome Fantasia</div>
                    <div class="emp-info-val">{{ $empresa->nome_fantasia ?: '—' }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">CNPJ</div>
                    <div class="emp-info-val font-mono">{{ substr($empresa->cnpj,0,2).'.'.substr($empresa->cnpj,2,3).'.'.substr($empresa->cnpj,5,3).'/'.substr($empresa->cnpj,8,4).'-'.substr($empresa->cnpj,12,2) }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Cidade / Estado</div>
                    <div class="emp-info-val">{{ $empresa->cidade ? $empresa->cidade.', '.$empresa->estado : '—' }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Total de Colaboradores</div>
                    <div class="emp-info-val" style="font-size:18px;color:var(--brand)">{{ $totalAtivos }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Status</div>
                    <div class="emp-info-val">
                        <span class="status-badge-big {{ $empresa->status === 'ativa' ? 'ativa' : 'vencida' }}">
                            <i class="fas fa-{{ $empresa->status === 'ativa' ? 'check-circle' : 'times-circle' }}"></i>
                            {{ ucfirst($empresa->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div style="padding:16px">
            <div class="emp-info-grid">
                <div class="emp-info-item">
                    <div class="emp-info-label">Empresas Ativas</div>
                    <div class="emp-info-val" style="font-size:24px;color:var(--brand)">{{ $empresas->count() }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Total Colaboradores</div>
                    <div class="emp-info-val" style="font-size:24px;color:var(--brand)">{{ number_format($totalAtivos) }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Afastados</div>
                    <div class="emp-info-val">{{ $totalAfastados }}</div>
                </div>
                <div class="emp-info-item">
                    <div class="emp-info-label">Demitidos (30d)</div>
                    <div class="emp-info-val">{{ $demissoesRecentes }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Conformidade ASO --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-shield-alt"></i> Conformidade — ASOs</div>
            <a href="{{ route('asos.index') }}" class="btn btn-secondary btn-sm">Ver ASOs</a>
        </div>
        <div class="conf-ring-wrap">
            @php
                $r = 52; $circ = 2 * pi() * $r;
                $dash = ($asoConformidade / 100) * $circ;
                $color = $asoConformidade >= 80 ? '#16a34a' : ($asoConformidade >= 60 ? '#d97706' : '#dc2626');
            @endphp
            <div class="conf-ring">
                <svg width="120" height="120" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="10"/>
                    <circle cx="60" cy="60" r="{{ $r }}" fill="none" stroke="{{ $color }}" stroke-width="10"
                        stroke-dasharray="{{ round($dash,1) }} {{ round($circ,1) }}" stroke-linecap="round"/>
                </svg>
                <div class="conf-ring-text">
                    <div class="conf-ring-pct" style="color:{{ $color }}">{{ $asoConformidade }}%</div>
                    <div class="conf-ring-lbl">conformidade</div>
                </div>
            </div>
            <div class="conf-stats">
                <div class="conf-stat">
                    <div class="conf-stat-dot" style="background:var(--success)"></div>
                    <div>
                        <div class="conf-stat-info">ASOs Válidos</div>
                        <div class="conf-stat-num" style="color:var(--success)">{{ $asoValidos }}</div>
                    </div>
                </div>
                <div class="conf-stat">
                    <div class="conf-stat-dot" style="background:var(--danger)"></div>
                    <div>
                        <div class="conf-stat-info">Vencidos</div>
                        <div class="conf-stat-num" style="color:var(--danger)">{{ $asoVencidos }}</div>
                    </div>
                </div>
                <div class="conf-stat">
                    <div class="conf-stat-dot" style="background:var(--warning)"></div>
                    <div>
                        <div class="conf-stat-info">A Vencer (30d)</div>
                        <div class="conf-stat-num" style="color:var(--warning)">{{ $asoAVencer }}</div>
                    </div>
                </div>
                <div class="conf-stat">
                    <div class="conf-stat-dot" style="background:var(--text-3)"></div>
                    <div>
                        <div class="conf-stat-info">Sem ASO</div>
                        <div class="conf-stat-num">{{ $semAso }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- GRÁFICOS ─────────────────────────────────────────────────────────────── --}}
<div class="dash-grid dash-2 mb-16">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-chart-line"></i> Tendência (6 meses)</div>
        </div>
        <canvas id="chartTrend" height="180" style="padding:0 16px 16px"></canvas>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-chart-pie"></i> Colaboradores por Setor</div>
        </div>
        <canvas id="chartSetor" height="180" style="padding:0 16px 16px"></canvas>
    </div>
</div>

{{-- LINHA 3: SETORES + INDICADORES COLABORADORES ──────────────────────────── --}}
<div class="dash-grid dash-2 mb-16">
    {{-- Setores --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-layer-group"></i> Colaboradores por Setor</div>
            <a href="{{ route('setores.index') }}" class="btn btn-ghost btn-sm">Gerenciar</a>
        </div>
        <div style="padding:0 16px 8px">
            @forelse($porSetor as $s)
            @php $pct = $totalAtivos > 0 ? round(($s->total / $totalAtivos) * 100) : 0; @endphp
            <div class="setor-row">
                <div class="setor-avatar">{{ strtoupper(substr($s->nome, 0, 2)) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="setor-nome">{{ $s->nome }}</div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%;background:var(--brand)"></div></div>
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div style="font-size:15px;font-weight:700;color:var(--text-1)">{{ $s->total }}</div>
                    <div style="font-size:10px;color:var(--text-3)">{{ $pct }}%</div>
                </div>
            </div>
            @empty
            <p style="color:var(--text-3);font-size:13px;padding:12px 0">Nenhum setor cadastrado.</p>
            @endforelse
        </div>
    </div>

    {{-- Indicadores de colaboradores --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-users"></i> Indicadores de Pessoas</div>
            <a href="{{ route('colaboradores.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:12px">
            @php
                $itens = [
                    ['label'=>'Ativos','val'=>$totalAtivos,'color'=>'var(--success)','icon'=>'fa-user-check'],
                    ['label'=>'Afastados','val'=>$totalAfastados,'color'=>'var(--warning)','icon'=>'fa-user-clock'],
                    ['label'=>'Demitidos (30d)','val'=>$demissoesRecentes,'color'=>'var(--danger)','icon'=>'fa-user-minus'],
                    ['label'=>'Admissões (30d)','val'=>$admissoesRecentes,'color'=>'var(--brand)','icon'=>'fa-user-plus'],
                ];
            @endphp
            @foreach($itens as $it)
            <div style="display:flex;align-items:center;gap:12px;padding:10px;background:var(--bg-alt);border-radius:var(--r-sm)">
                <div style="width:36px;height:36px;border-radius:8px;background:{{ $it['color'] }}1a;color:{{ $it['color'] }};display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">
                    <i class="fas {{ $it['icon'] }}"></i>
                </div>
                <div style="flex:1">
                    <div style="font-size:11px;color:var(--text-3)">{{ $it['label'] }}</div>
                    <div style="font-size:20px;font-weight:800;color:var(--text-1)">{{ $it['val'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- LINHA 4: RISCOS + CIPA + EPIs ──────────────────────────────────────────── --}}
<div class="dash-grid dash-3 mb-16">
    {{-- Riscos por tipo --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-exclamation-triangle"></i> Riscos (PGR)</div>
            <a href="{{ route('riscos.index') }}" class="btn btn-ghost btn-sm">Ver</a>
        </div>
        <div style="padding:0 16px 16px">
            <div style="font-size:28px;font-weight:800;color:var(--text-1);margin-bottom:12px">
                {{ $totalRiscos }} <span style="font-size:13px;font-weight:400;color:var(--text-3)">riscos identificados</span>
            </div>
            @php
                $tiposRisco = [
                    'fisico'    => ['label'=>'Físico',     'icon'=>'fa-bolt'],
                    'quimico'   => ['label'=>'Químico',    'icon'=>'fa-flask'],
                    'biologico' => ['label'=>'Biológico',  'icon'=>'fa-biohazard'],
                    'ergonomico'=> ['label'=>'Ergonômico', 'icon'=>'fa-chair'],
                    'acidente'  => ['label'=>'Acidente',   'icon'=>'fa-hard-hat'],
                ];
            @endphp
            @foreach($tiposRisco as $key => $info)
            <div class="risco-chip {{ $key }}">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas {{ $info['icon'] }}"></i>
                    <span>{{ $info['label'] }}</span>
                </div>
                <span class="risco-chip-num">{{ $riscosPorTipo[$key]->total ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CIPA + Obrigações --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-users-cog"></i> Obrigações Legais</div>
            <a href="{{ route('cipa.index') }}" class="btn btn-ghost btn-sm">CIPA</a>
        </div>
        <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:14px">
            {{-- CIPA --}}
            <div style="padding:12px;background:var(--bg-alt);border-radius:var(--r-sm)">
                <div style="font-size:11px;color:var(--text-3);margin-bottom:6px;font-weight:700;text-transform:uppercase;letter-spacing:.07em">CIPA</div>
                @if($cipaAtiva > 0)
                <span class="status-badge-big ativa"><i class="fas fa-check-circle"></i> Ativa — {{ $cipaAtiva }} membro(s)</span>
                @elseif($cipaMandatoVencido > 0)
                <span class="status-badge-big vencida"><i class="fas fa-times-circle"></i> Mandato Vencido</span>
                @else
                <span class="status-badge-big sem-dados"><i class="fas fa-minus-circle"></i> Não cadastrada</span>
                @endif
            </div>
            {{-- Extintores --}}
            <div style="padding:12px;background:var(--bg-alt);border-radius:var(--r-sm)">
                <div style="font-size:11px;color:var(--text-3);margin-bottom:6px;font-weight:700;text-transform:uppercase;letter-spacing:.07em">Extintores</div>
                <div style="display:flex;align-items:center;gap:12px">
                    <span style="font-size:24px;font-weight:800;color:{{ $extintVencidos > 0 ? 'var(--danger)' : 'var(--success)' }}">{{ $extintVencidos }}</span>
                    <span style="font-size:12px;color:var(--text-3)">vencido(s) de {{ $extintTotal }} total</span>
                </div>
                @if($extintTotal > 0)
                <div class="prog-bar" style="margin-top:6px">
                    <div class="prog-fill" style="width:{{ $extintTotal > 0 ? round((($extintTotal - $extintVencidos) / $extintTotal) * 100) : 0 }}%;background:{{ $extintVencidos > 0 ? 'var(--danger)' : 'var(--success)' }}"></div>
                </div>
                @endif
            </div>
            {{-- Brigada --}}
            <div style="padding:12px;background:var(--bg-alt);border-radius:var(--r-sm)">
                <div style="font-size:11px;color:var(--text-3);margin-bottom:6px;font-weight:700;text-transform:uppercase;letter-spacing:.07em">Brigada de Emergência</div>
                <a href="{{ route('brigada.index') }}" class="btn btn-secondary btn-sm btn-full"><i class="fas fa-user-shield"></i> Verificar membros</a>
            </div>
        </div>
    </div>

    {{-- EPIs --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-hard-hat"></i> EPIs</div>
            <a href="{{ route('epis.dashboard') }}" class="btn btn-ghost btn-sm">Painel</a>
        </div>
        <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:10px">
            @php
                $epiItens = [
                    ['label'=>'Entregas Registradas','val'=>$epiEntregues,'color'=>'var(--brand)'],
                    ['label'=>'Prazo Vencido','val'=>$epiVencidos,'color'=>'var(--danger)'],
                    ['label'=>'Vencendo (30d)','val'=>$epiAVencer,'color'=>'var(--warning)'],
                    ['label'=>'Estoque Zerado','val'=>$estoquesBaixos,'color'=>'var(--text-3)'],
                ];
            @endphp
            @foreach($epiItens as $e)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--bg-alt);border-radius:var(--r-sm)">
                <span style="font-size:12px;color:var(--text-2)">{{ $e['label'] }}</span>
                <span style="font-size:18px;font-weight:800;color:{{ $e['color'] }}">{{ $e['val'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- LINHA 5: SETORES COM MAIOR RISCO + ADMISSÕES RECENTES ─────────────────── --}}
<div class="dash-grid dash-2 mb-16">
    {{-- Setores com maior exposição a risco --}}
    @if($setoresRisco->count() > 0)
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-map-marked-alt"></i> Setores com Maior Exposição</div>
            <a href="{{ route('gro.matriz') }}" class="btn btn-ghost btn-sm">Matriz GRO</a>
        </div>
        <div style="padding:0 16px 16px">
            @foreach($setoresRisco as $i => $s)
            @php
                $cores = ['var(--danger)','var(--warning)','var(--brand)','var(--success)','var(--text-3)'];
                $max = $setoresRisco->first()->total_riscos ?: 1;
                $pct = round(($s->total_riscos / $max) * 100);
            @endphp
            <div class="setor-row">
                <div style="width:24px;height:24px;border-radius:50%;background:{{ $cores[$i] ?? 'var(--text-3)' }}1a;color:{{ $cores[$i] ?? 'var(--text-3)' }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0">{{ $i+1 }}</div>
                <div style="flex:1;min-width:0">
                    <div class="setor-nome">{{ $s->nome }}</div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%;background:{{ $cores[$i] ?? 'var(--text-3)' }}"></div></div>
                </div>
                <div style="font-size:13px;font-weight:700;color:{{ $cores[$i] ?? 'var(--text-3)' }};flex-shrink:0">{{ $s->total_riscos }} riscos</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Admissões recentes --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-user-plus"></i> Admissões Recentes (60d)</div>
            <a href="{{ route('colaboradores.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div style="padding:0 16px 8px">
            @forelse($admissoesLista as $col)
            <div class="tl-item">
                <div class="tl-dot" style="background:var(--success)"></div>
                <div class="tl-content">
                    <div class="tl-name">{{ $col->nome }}</div>
                    <div class="tl-date">
                        {{ $col->funcao?->nome ?? 'Sem função' }} · {{ $col->setor?->nome ?? 'Sem setor' }}
                        · <strong>{{ $col->data_admissao?->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>
            @empty
            <p style="color:var(--text-3);font-size:13px;padding:12px 0">Nenhuma admissão nos últimos 60 dias.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- PRÓXIMOS ASOs ──────────────────────────────────────────────────────────── --}}
<div class="card mb-16">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-clock"></i> ASOs — Vencidos e Próximos ao Vencimento</div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('asos.vencidos') }}" class="btn btn-secondary btn-sm">Vencidos</a>
            <a href="{{ route('asos.a-vencer') }}" class="btn btn-secondary btn-sm">A Vencer</a>
        </div>
    </div>
    @if($proximosAsos->isEmpty())
    <div class="empty-state py-32">
        <i class="fas fa-check-circle" style="font-size:36px;color:var(--success)"></i>
        <p style="margin-top:8px;color:var(--text-3)">Nenhum ASO vencendo nos próximos 30 dias 🎉</p>
    </div>
    @else
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>COLABORADOR</th><th>FUNÇÃO / SETOR</th><th>VENCIMENTO</th><th>SITUAÇÃO</th><th></th></tr></thead>
            <tbody>
            @foreach($proximosAsos as $a)
            @php $dias = $a->dias_restantes; @endphp
            <tr class="{{ $dias !== null && $dias < 0 ? 'tr-danger' : ($dias !== null && $dias <= 15 ? 'tr-warning' : '') }}">
                <td>
                    <div class="font-bold text-13">{{ $a->colaborador?->nome ?? '—' }}</div>
                    <div class="text-11 text-muted">{{ $a->colaborador?->empresa?->nome_display ?? '—' }}</div>
                </td>
                <td class="text-12">
                    {{ $a->colaborador?->funcao?->nome ?? '—' }}<br>
                    <span class="text-muted">{{ $a->colaborador?->setor?->nome ?? '—' }}</span>
                </td>
                <td class="font-mono text-12">{{ $a->data_vencimento?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    @if($dias !== null && $dias < 0)
                        <span class="badge badge-danger">Vencido há {{ abs($dias) }}d</span>
                    @elseif($dias !== null && $dias <= 15)
                        <span class="badge badge-warning">Urgente — {{ $dias }}d</span>
                    @elseif($dias !== null)
                        <span class="badge badge-info">{{ $dias }} dias</span>
                    @else
                        <span class="badge badge-secondary">Sem data</span>
                    @endif
                </td>
                <td><a href="{{ route('asos.edit', $a->id) }}" class="btn btn-secondary btn-sm">Atualizar</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- GRÁFICO DE RISCOS ──────────────────────────────────────────────────────── --}}
<div class="dash-grid dash-2 mb-16">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-chart-bar"></i> Distribuição de Riscos por Tipo</div>
        </div>
        <canvas id="chartRiscos" height="200" style="padding:0 16px 16px"></canvas>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-chart-bar"></i> ASOs — Válidos x Vencidos x A Vencer</div>
        </div>
        <canvas id="chartAsos" height="200" style="padding:0 16px 16px"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const COLORS  = ['#2563eb','#16a34a','#d97706','#dc2626','#0891b2','#7c3aed','#db2777','#ea580c'];
const gOpts   = {color:'rgba(255,255,255,.05)'};
const tOpts   = {color:'#94a3b8',font:{size:11}};
const ttBg    = 'rgba(15,23,42,.95)';
const charts  = {};

function mkChart(id, type, data, opts={}) {
    if (charts[id]) charts[id].destroy();
    const c = document.getElementById(id); if (!c) return;
    charts[id] = new Chart(c, {
        type, data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#94a3b8', font: { size: 11 } } },
                tooltip: { backgroundColor: ttBg, titleColor: '#f1f5f9', bodyColor: '#94a3b8' },
                ...(opts.plugins || {})
            },
            ...opts
        }
    });
}

// Gráfico tendência
const tendencia = @json($tendencia);
mkChart('chartTrend', 'line', {
    labels: tendencia.map(x => x.mes),
    datasets: [
        { label: 'ASOs criados', data: tendencia.map(x => x.asos),
          borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.12)', fill: true, tension: .4, pointRadius: 4 },
        { label: 'Admissões', data: tendencia.map(x => x.admissoes),
          borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.12)', fill: true, tension: .4, pointRadius: 4 },
    ]
}, { scales: { x: { ticks: tOpts, grid: gOpts }, y: { ticks: tOpts, grid: gOpts, beginAtZero: true } }, plugins: { legend: { position: 'top' } } });

// Gráfico por setor
const porSetor = @json($porSetor);
if (porSetor.length) {
    mkChart('chartSetor', 'doughnut', {
        labels: porSetor.map(x => x.nome),
        datasets: [{ data: porSetor.map(x => x.total), backgroundColor: COLORS, borderWidth: 2, borderColor: 'rgba(0,0,0,.2)' }]
    }, { cutout: '55%' });
}

// Gráfico riscos
const riscos = @json($riscosPorTipo);
const tiposLabels = { fisico: 'Físico', quimico: 'Químico', biologico: 'Biológico', ergonomico: 'Ergonômico', acidente: 'Acidente' };
const tiposCores  = { fisico: '#0891b2', quimico: '#7c3aed', biologico: '#16a34a', ergonomico: '#d97706', acidente: '#dc2626' };
const riscosLabels = Object.keys(tiposLabels);
mkChart('chartRiscos', 'bar', {
    labels: riscosLabels.map(k => tiposLabels[k]),
    datasets: [{ label: 'Qtd', data: riscosLabels.map(k => riscos[k]?.total || 0),
        backgroundColor: riscosLabels.map(k => tiposCores[k] + '99'), borderColor: riscosLabels.map(k => tiposCores[k]),
        borderWidth: 2, borderRadius: 6 }]
}, { scales: { x: { ticks: tOpts, grid: gOpts }, y: { ticks: tOpts, grid: gOpts, beginAtZero: true } }, plugins: { legend: { display: false } } });

// Gráfico ASOs
mkChart('chartAsos', 'bar', {
    labels: ['Válidos', 'Vencidos', 'A Vencer (30d)', 'Sem ASO'],
    datasets: [{ data: [{{ $asoValidos }}, {{ $asoVencidos }}, {{ $asoAVencer }}, {{ $semAso }}],
        backgroundColor: ['rgba(22,163,74,.7)','rgba(220,38,38,.7)','rgba(217,119,6,.7)','rgba(100,116,139,.5)'],
        borderColor: ['#16a34a','#dc2626','#d97706','#64748b'],
        borderWidth: 2, borderRadius: 6 }]
}, { scales: { x: { ticks: tOpts, grid: gOpts }, y: { ticks: tOpts, grid: gOpts, beginAtZero: true } }, plugins: { legend: { display: false } } });
</script>
@endpush
