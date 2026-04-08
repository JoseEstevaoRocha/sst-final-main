@extends('layouts.app')
@section('title','Config WhatsApp')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Configuração WhatsApp</h1><p class="page-sub">Modelo de mensagem para solicitação de agendamento</p></div>
    <a href="{{ route('whatsapp.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start">

<div class="card">
    <form method="POST" action="{{ route('whatsapp.config.save') }}">@csrf
    <div class="flex flex-col gap-18">
        <div class="form-group">
            <label class="form-label">Modelo da Mensagem</label>
            <p class="text-11 text-muted mb-8">Use as variáveis da lista ao lado. Cada variável será substituída pelos dados reais do colaborador no momento do envio.</p>
            <textarea name="modelo_mensagem" class="form-control" rows="12" required style="font-family:monospace;font-size:13px">{{ old('modelo_mensagem', $config->modelo_mensagem ?? "*SOLICITAÇÃO DE AGENDAMENTO*\nEmpresa: {empresa}\nColaborador: {nome}\nCPF: {cpf}\nRG: {rg}\nNasc: {nasc}\nExame: {tipo}\nSetor Atual: {setor}\nFunção Atual: {funcao}\nData: {data} às {horario}") }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Telefone para Retorno</label>
            <input type="text" name="telefone_retorno" value="{{ old('telefone_retorno', $config->telefone_retorno ?? '') }}" class="form-control" placeholder="(11) 99999-0000">
        </div>
    </div>
    <div class="form-footer">
        <a href="{{ route('whatsapp.index') }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    </div>
    </form>
</div>

{{-- PAINEL DE VARIÁVEIS --}}
<div style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="fab fa-whatsapp" style="color:#25d366"></i> Variáveis Disponíveis</div></div>
        <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:6px">
            @php
            $vars = [
                ['{nome}',       'Nome completo do colaborador (maiúsculas)'],
                ['{empresa}',    'Razão social da empresa (maiúsculas)'],
                ['{cpf}',        'CPF formatado (xxx.xxx.xxx-xx)'],
                ['{rg}',         'RG do colaborador'],
                ['{nasc}',       'Data de nascimento (dd/mm/aaaa)'],
                ['{tipo}',       'Tipo do exame (ADMISSIONAL, DEMISSIONAL...)'],
                ['{setor}',      'Setor atual do colaborador'],
                ['{funcao}',     'Função + CBO do colaborador'],
                ['{data}',       'Data agendada (informada na solicitação)'],
                ['{horario}',    'Horário agendado (informado na solicitação)'],
                ['{clinica}',    'Nome da clínica destinatária'],
            ];
            @endphp
            @foreach($vars as [$var, $desc])
            <div style="display:flex;align-items:flex-start;gap:10px;padding:7px 10px;background:var(--bg-alt);border-radius:6px;cursor:pointer" onclick="inserirVar('{{ $var }}')">
                <code style="font-size:12px;font-weight:700;color:var(--brand);background:rgba(37,99,235,.1);padding:2px 7px;border-radius:4px;white-space:nowrap">{{ $var }}</code>
                <span style="font-size:11px;color:var(--text-3);margin-top:2px">{{ $desc }}</span>
            </div>
            @endforeach
            <p class="text-11 text-muted" style="margin-top:6px"><i class="fas fa-info-circle"></i> Clique na variável para inserir no modelo</p>
        </div>
    </div>

    {{-- Preview --}}
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="fas fa-eye"></i> Preview</div></div>
        <div style="padding:0 16px 16px">
            <div id="preview" style="background:var(--bg-alt);border-radius:8px;padding:14px;font-size:12px;white-space:pre-wrap;line-height:1.6;color:var(--text-2);min-height:80px;font-family:monospace"></div>
        </div>
    </div>
</div>

</div>
@endsection
@push('scripts')
<script>
const textarea = document.querySelector('textarea[name=modelo_mensagem]');
function inserirVar(v) {
    const start = textarea.selectionStart;
    const end   = textarea.selectionEnd;
    textarea.value = textarea.value.substring(0, start) + v + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + v.length, start + v.length);
    atualizarPreview();
}
function atualizarPreview() {
    const exemplo = {
        '{nome}':'WILLIANS COSTA MACHADO', '{empresa}':'CAMARO DA SERRA INDUSTRIA E COMERCIO LTDA',
        '{cpf}':'077.299.907-41', '{rg}':'111990669 - IFP - RJ', '{nasc}':'15/10/1977',
        '{tipo}':'DEMISSIONAL', '{setor}':'ESCRITÓRIO',
        '{funcao}':'Técnico de Operação de Transporte 3421-10',
        '{data}':'07/04/2026', '{horario}':'08H00', '{clinica}':'CLÍNICA SST'
    };
    let txt = textarea.value;
    Object.entries(exemplo).forEach(([k,v]) => { txt = txt.replaceAll(k, v); });
    document.getElementById('preview').textContent = txt;
}
textarea.addEventListener('input', atualizarPreview);
atualizarPreview();
</script>
@endpush
