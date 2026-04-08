@extends('layouts.app')
@section('title','Importação de Colaboradores')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Importação de Colaboradores</h1><p class="page-sub">CSV ou XLSX em lote</p></div>
    <a href="{{ route('importacao.modelo','colaboradores') }}" class="btn btn-secondary"><i class="fas fa-download"></i> Baixar Modelo CSV</a>
</div>

@if(session('importResult'))
@php $res = session('importResult'); $temErros = count($res['erros']) > 0; $temOk = count($res['ok'] ?? []) > 0; @endphp
<div class="card mb-20">
    <div class="card-header">
        <div class="card-title"><i class="fas fa-chart-bar"></i> Resultado da Importação</div>
        <a href="{{ route('importacao.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i> Nova Importação</a>
    </div>
    {{-- KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
        <div style="text-align:center;padding:16px;background:rgba(63,185,80,.08);border-radius:var(--r-sm);border:1px solid rgba(63,185,80,.2)">
            <div style="font-size:32px;font-weight:800;color:var(--success)">{{ $res['sucesso'] }}</div>
            <div style="font-size:11px;text-transform:uppercase;color:var(--text-3);margin-top:4px">Importados com sucesso</div>
        </div>
        <div style="text-align:center;padding:16px;background:{{ $temErros ? 'rgba(248,81,73,.08)' : 'var(--bg-secondary)' }};border-radius:var(--r-sm);border:1px solid {{ $temErros ? 'rgba(248,81,73,.2)' : 'var(--border)' }}">
            <div style="font-size:32px;font-weight:800;color:{{ $temErros ? 'var(--danger)' : 'var(--text-3)' }}">{{ count($res['erros']) }}</div>
            <div style="font-size:11px;text-transform:uppercase;color:var(--text-3);margin-top:4px">Erros</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-secondary);border-radius:var(--r-sm);border:1px solid var(--border)">
            <div style="font-size:32px;font-weight:800">{{ $res['total'] }}</div>
            <div style="font-size:11px;text-transform:uppercase;color:var(--text-3);margin-top:4px">Total de linhas</div>
        </div>
    </div>

    {{-- Erros --}}
    @if($temErros)
    <div style="margin-bottom:16px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
            <i class="fas fa-times-circle" style="color:var(--danger)"></i>
            <strong style="color:var(--danger)">{{ count($res['erros']) }} erro(s) — essas linhas NÃO foram importadas:</strong>
        </div>
        <div style="border:1px solid rgba(248,81,73,.3);border-radius:var(--r-sm);overflow:hidden">
            @foreach($res['erros'] as $idx => $err)
            <div style="padding:8px 12px;background:{{ $idx%2==0 ? 'rgba(248,81,73,.04)' : 'transparent' }};border-bottom:1px solid rgba(248,81,73,.1);font-size:12px;font-family:monospace;color:var(--danger)">
                <i class="fas fa-exclamation-circle" style="margin-right:6px;opacity:.7"></i>{{ $err }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Sucesso --}}
    @if($temOk)
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;cursor:pointer" onclick="toggleOk()" id="okToggle">
            <i class="fas fa-check-circle" style="color:var(--success)"></i>
            <strong style="color:var(--success)">{{ $res['sucesso'] }} importado(s) com sucesso</strong>
            <i class="fas fa-chevron-down" style="font-size:10px;color:var(--text-3);margin-left:4px" id="okChevron"></i>
        </div>
        <div id="okList" style="display:none;border:1px solid rgba(63,185,80,.3);border-radius:var(--r-sm);overflow:hidden;max-height:300px;overflow-y:auto">
            @foreach($res['ok'] as $idx => $msg)
            <div style="padding:7px 12px;background:{{ $idx%2==0 ? 'rgba(63,185,80,.04)' : 'transparent' }};border-bottom:1px solid rgba(63,185,80,.1);font-size:12px;color:var(--success)">
                <i class="fas fa-check" style="margin-right:6px;opacity:.7"></i>{{ $msg }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@else
{{-- Formulário de upload --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="fas fa-file-import"></i> Importar Arquivo</div></div>
        <form method="POST" action="{{ route('importacao.colaboradores') }}" enctype="multipart/form-data">@csrf
            <div class="dropzone" id="dropzone">
                <input type="file" name="arquivo" id="fileInput" accept=".csv,.txt,.xlsx,.xls" required>
                <div class="drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <h3>Arraste o arquivo aqui</h3>
                <p>ou clique para selecionar</p>
                <p style="margin-top:6px;font-size:11px;color:var(--text-3)">CSV (separador ;) ou XLSX · máx. 10MB</p>
                <div class="drop-filename" id="dropFilename" style="margin-top:10px;font-size:13px;color:var(--brand);font-weight:600"></div>
            </div>
            <button type="submit" class="btn btn-primary btn-full mt-16"><i class="fas fa-upload"></i> Iniciar Importação</button>
        </form>
    </div>
    <div class="card">
        <div class="card-title mb-14"><i class="fas fa-info-circle"></i> Instruções & Campos</div>
        <div class="flex flex-col gap-10 text-13 text-muted mb-16">
            @foreach(['Baixe o modelo CSV clicando no botão acima','O separador deve ser ponto-e-vírgula (;)','O CNPJ da empresa deve existir no sistema','Setor e Função são criados automaticamente se não existirem','CPFs duplicados são ignorados com aviso de erro','Datas aceitas: AAAA-MM-DD ou DD/MM/AAAA'] as $idx=>$txt)
            <div class="flex gap-10 align-center"><span style="width:22px;height:22px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">{{ $idx+1 }}</span><span>{{ $txt }}</span></div>
            @endforeach
        </div>
        <div style="background:var(--bg-secondary);border-radius:var(--r-sm);padding:12px;font-size:11px;font-family:monospace;color:var(--text-2)">
            <div style="font-weight:700;margin-bottom:6px;color:var(--text-1)">Colunas obrigatórias (*):</div>
            <div>nome * | cpf * | cnpj_empresa *</div>
            <div>nome_setor * | nome_funcao *</div>
            <div>data_nascimento * | sexo (M/F) *</div>
            <div>data_admissao *</div>
            <div style="margin-top:8px;font-weight:700;color:var(--text-1)">Opcionais:</div>
            <div>status | matricula | cbo | escolaridade</div>
            <div>pis | telefone | email</div>
        </div>
    </div>
</div>
@endif
@endsection
@push('scripts')
<script>
function toggleOk() {
    const list = document.getElementById('okList');
    const chev = document.getElementById('okChevron');
    if (list.style.display === 'none') {
        list.style.display = 'block';
        chev.style.transform = 'rotate(180deg)';
    } else {
        list.style.display = 'none';
        chev.style.transform = '';
    }
}
const fi = document.getElementById('fileInput');
if (fi) fi.addEventListener('change', function() {
    const el = document.getElementById('dropFilename');
    if (el && this.files[0]) el.textContent = this.files[0].name;
});
</script>
@endpush
