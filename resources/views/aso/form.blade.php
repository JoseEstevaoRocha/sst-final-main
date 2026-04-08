@extends('layouts.app')
@section('title', $aso ? 'Editar ASO' : 'Novo ASO')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">{{ $aso ? 'Editar ASO' : 'Novo ASO' }}</h1></div>
    <a href="{{ route('asos.index') }}" class="btn btn-secondary">← Voltar</a>
</div>
<div class="card">
    <form method="POST" action="{{ $aso ? route('asos.update',$aso->id) : route('asos.store') }}">
        @csrf @if($aso) @method('PUT') @endif
        <div class="form-grid">
            <div class="form-section"><i class="fas fa-building"></i> Empresa e Colaborador</div>
            <div class="form-group">
                <label class="form-label">Empresa <span class="required">*</span></label>
                <select name="empresa_id" id="empresa_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($empresas as $e)<option value="{{ $e->id }}" {{ old('empresa_id',$aso?->empresa_id??auth()->user()->empresa_id)==$e->id?'selected':'' }}>{{ $e->nome_display }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Colaborador <span class="required">*</span></label>
                <select name="colaborador_id" id="colaboradorSelect" class="form-select" required>
                    <option value="">Selecione a empresa primeiro</option>
                    @if($aso)<option value="{{ $aso?->colaborador_id }}" selected>{{ $aso?->colaborador?->nome }}</option>@endif
                </select>
            </div>
            <div class="form-section"><i class="fas fa-clipboard-list"></i> Dados do ASO</div>
            <div class="form-group">
                <label class="form-label">Tipo <span class="required">*</span></label>
                <select name="tipo" class="form-select" required>
                    @foreach(['admissional'=>'Admissional','periodico'=>'Periódico','demissional'=>'Demissional','retorno'=>'Retorno ao Trabalho','mudanca_funcao'=>'Mudança de Função'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('tipo',$aso?->tipo??'')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Resultado</label>
                <select name="resultado" class="form-select">
                    @foreach(['pendente'=>'Pendente','apto'=>'Apto','apto_restricoes'=>'Apto com Restrições','inapto'=>'Inapto'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('resultado',$aso?->resultado??'pendente')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Data do Exame</label>
                <input type="date" name="data_exame" value="{{ old('data_exame',$aso?->data_exame?->format('Y-m-d')??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Data de Vencimento</label>
                <input type="date" name="data_vencimento" value="{{ old('data_vencimento',$aso?->data_vencimento?->format('Y-m-d')??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Clínica</label>
                <select name="clinica_id" class="form-select">
                    <option value="">Selecione ou digite abaixo</option>
                    @foreach($clinicas as $cl)<option value="{{ $cl->id }}" {{ old('clinica_id',$aso?->clinica_id??'')==$cl->id?'selected':'' }}>{{ $cl->nome }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nome da Clínica (manual)</label>
                <input type="text" name="clinica_nome" value="{{ old('clinica_nome',$aso?->clinica_nome??'') }}" class="form-control" placeholder="Ou digite o nome da clínica">
            </div>
            <div class="form-group">
                <label class="form-label">Médico Responsável</label>
                <input type="text" name="medico_responsavel" value="{{ old('medico_responsavel',$aso?->medico_responsavel??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Status Logístico</label>
                <select name="status_logistico" class="form-select">
                    @foreach(['pendente'=>'Pendente','agendado'=>'Agendado','em_atendimento'=>'Em Atendimento','finalizado'=>'Finalizado','em_transito'=>'Em Trânsito','recebido_empresa'=>'Recebido Empresa','entregue_colaborador'=>'Entregue Colaborador'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('status_logistico',$aso?->status_logistico??'pendente')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes',$aso?->observacoes??'') }}</textarea>
            </div>
        </div>
        <div class="form-footer">
            <a href="{{ route('asos.index') }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('empresa_id')?.addEventListener('change', async function() {
    const r = await fetch(`/api/colaboradores?empresa_id=${this.value}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await r.json();
    const sel = document.getElementById('colaboradorSelect');
    sel.innerHTML = '<option value="">Selecione</option>';
    data.forEach(c => { const o=document.createElement('option'); o.value=c.id; o.textContent=c.nome; sel.appendChild(o); });
});
</script>
@endpush
