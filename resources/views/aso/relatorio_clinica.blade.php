<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Agendamento — Clínica</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:20px}
.header{border-bottom:3px solid #1d4ed8;padding-bottom:14px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:flex-start}
.logo-area h1{font-size:20px;color:#1d4ed8;font-weight:800}
.logo-area p{font-size:11px;color:#64748b;margin-top:2px}
.doc-info{text-align:right;font-size:11px;color:#64748b}
.doc-info strong{display:block;font-size:13px;color:#1a1a1a}
.empresa-box{background:#f1f5f9;border-left:4px solid #1d4ed8;padding:10px 14px;border-radius:4px;margin-bottom:16px}
.empresa-box h2{font-size:14px;color:#1d4ed8;font-weight:700;margin-bottom:4px}
.empresa-box p{font-size:11px;color:#475569}
.section-title{font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;margin:16px 0 8px}
table{width:100%;border-collapse:collapse;font-size:11px}
thead tr{background:#1d4ed8;color:#fff}
thead th{padding:7px 8px;text-align:left;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em}
tbody tr{border-bottom:1px solid #e2e8f0}
tbody tr:nth-child(even){background:#f8fafc}
tbody td{padding:7px 8px;vertical-align:top}
.badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700}
.badge-blue{background:#dbeafe;color:#1d4ed8}
.badge-green{background:#dcfce7;color:#16a34a}
.footer{margin-top:24px;padding-top:12px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;font-size:10px;color:#94a3b8}
.assinatura{margin-top:40px;display:flex;gap:60px}
.ass-line{border-top:1px solid #64748b;padding-top:6px;font-size:10px;color:#475569;min-width:200px;text-align:center}
.exames-cell{color:#475569;font-style:italic}
.no-print{display:flex;gap:8px;margin-bottom:16px}
@media print{
    .no-print{display:none!important}
    body{padding:10px}
}
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" style="padding:8px 20px;background:#1d4ed8;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600">
        🖨️ Imprimir / Salvar PDF
    </button>
    <button onclick="window.close()" style="padding:8px 20px;background:#e2e8f0;color:#1a1a1a;border:none;border-radius:6px;cursor:pointer">
        Fechar
    </button>
</div>

{{-- Cabeçalho --}}
<div class="header">
    <div class="logo-area">
        <h1>SST Manager</h1>
        <p>Relatório de Agendamento de Exames Ocupacionais</p>
    </div>
    <div class="doc-info">
        <strong>Data de Emissão</strong>
        {{ now()->format('d/m/Y H:i') }}<br>
        Emitido por: {{ auth()->user()->name ?? '—' }}
    </div>
</div>

{{-- Empresa --}}
@if($empresa)
<div class="empresa-box">
    <h2>{{ $empresa->razao_social ?? $empresa->nome_display }}</h2>
    <p>
        @if($empresa->cnpj) CNPJ: {{ $empresa->cnpj }} &nbsp;|&nbsp; @endif
        @if(isset($empresa->endereco) && $empresa->endereco) {{ $empresa->endereco }} @endif
    </p>
</div>
@endif

<div class="section-title">Colaboradores para Agendamento — {{ $asos->count() }} registro(s)</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Colaborador</th>
            <th>CPF</th>
            <th>Nascimento</th>
            <th>Setor</th>
            <th>Função</th>
            <th>Tipo de Exame</th>
            <th>Data Agendada</th>
            <th>Horário</th>
            <th>Exames Complementares</th>
        </tr>
    </thead>
    <tbody>
    @foreach($asos as $i => $a)
    @php
        $colab = $a->colaborador;
        $tipos = ['admissional'=>'Admissional','periodico'=>'Periódico','demissional'=>'Demissional','retorno'=>'Retorno','mudanca_funcao'=>'Mudança de Função'];
    @endphp
    <tr>
        <td>{{ $i + 1 }}</td>
        <td><strong>{{ $colab?->nome ?? '—' }}</strong></td>
        <td>{{ $colab?->cpf ?? '—' }}</td>
        <td>{{ $colab?->data_nascimento?->format('d/m/Y') ?? '—' }}</td>
        <td>{{ $colab?->setor?->nome ?? '—' }}</td>
        <td>{{ $colab?->funcao?->nome ?? '—' }}</td>
        <td><span class="badge badge-blue">{{ $tipos[$a->tipo] ?? ucfirst($a->tipo) }}</span></td>
        <td>
            @if($a->data_agendada)
                <span class="badge badge-green">{{ $a->data_agendada->format('d/m/Y') }}</span>
            @else
                —
            @endif
        </td>
        <td>{{ $a->horario_agendado ? substr($a->horario_agendado,0,5) : '—' }}</td>
        <td class="exames-cell">{{ $a->exames_complementares ?? '—' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

{{-- Assinaturas --}}
<div class="assinatura">
    <div class="ass-line">Responsável SST / Empresa</div>
    <div class="ass-line">Responsável Clínica</div>
    <div class="ass-line">Data: ___/___/______</div>
</div>

<div class="footer">
    <span>SST Manager — Sistema de Gestão de Saúde e Segurança do Trabalho</span>
    <span>Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</span>
</div>

</body>
</html>
