@extends('layouts.app')
@section('title','Entregas de Uniforme')
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Entregas de Uniforme</h1><p class="page-sub">{{ $entregas->total() }} registros</p></div>
    <button class="btn btn-primary" onclick="openModal('modalEntrega')"><i class="fas fa-plus"></i> Nova Entrega</button>
</div>
<div class="card p-0"><div class="table-wrap"><table class="table">
<thead><tr><th>COLABORADOR</th><th>UNIFORME</th><th>TAMANHO</th><th>QTD</th><th>DATA ENTREGA</th><th>MOTIVO</th><th>RESPONSÁVEL</th></tr></thead>
<tbody>
@forelse($entregas as $e)
<tr>
    <td class="font-bold text-13">{{ $e->colaborador->nome??'—' }}</td>
    <td><div class="font-bold text-13">{{ $e->uniforme->nome??'—' }}</div><div class="text-11 text-muted">{{ $e->uniforme->tipo??'' }}</div></td>
    <td><span class="badge badge-info">{{ $e->tamanho->codigo??'—' }}</span></td>
    <td class="font-bold text-16" style="color:var(--brand)">{{ $e->quantidade }}</td>
    <td class="font-mono text-12">{{ $e->data_entrega->format('d/m/Y') }}</td>
    <td class="text-12">{{ ucfirst($e->motivo??'—') }}</td>
    <td class="text-12">{{ $e->responsavel??'—' }}</td>
</tr>
@empty
<tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fas fa-box-open"></i></div><h3>Nenhuma entrega</h3></div></td></tr>
@endforelse
</tbody></table></div></div>

<div class="modal-overlay" id="modalEntrega"><div class="modal modal-lg">
<div class="modal-header"><div class="modal-title"><i class="fas fa-box-open"></i> Nova Entrega de Uniforme</div><button class="modal-close" onclick="closeModal('modalEntrega')"><i class="fas fa-times"></i></button></div>
<div class="modal-body"><form method="POST" action="{{ route('uniformes.entregas.store') }}">@csrf
<div class="form-grid">
    <div class="form-group"><label class="form-label">Empresa *</label><select name="empresa_id" id="empresa_id" class="form-select" required><option value="">Selecione</option>@foreach($empresas as $e)<option value="{{ $e->id }}">{{ $e->nome_display }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Colaborador *</label><select name="colaborador_id" id="colabSel" class="form-select" required><option value="">Selecione a empresa</option></select></div>
    <div class="form-group"><label class="form-label">Uniforme *</label><select name="uniforme_id" class="form-select" required><option value="">Selecione</option>@foreach($uniformes_list as $u)<option value="{{ $u->id }}">{{ $u->nome }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Tamanho *</label><select name="tamanho_id" class="form-select" required><option value="">Selecione</option>@foreach($tamanhos as $t)<option value="{{ $t->id }}">{{ $t->codigo }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label">Quantidade</label><input type="number" name="quantidade" class="form-control" value="1" min="1" required></div>
    <div class="form-group"><label class="form-label">Data de Entrega *</label><input type="date" name="data_entrega" class="form-control" value="{{ date('Y-m-d') }}" required></div>
    <div class="form-group"><label class="form-label">Motivo</label><select name="motivo" class="form-select"><option value="admissao">Admissão</option><option value="substituicao">Substituição</option><option value="perda">Perda</option><option value="dano">Dano</option></select></div>
    <div class="form-group"><label class="form-label">Responsável</label><input type="text" name="responsavel" class="form-control" value="{{ auth()->user()->name }}"></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal('modalEntrega')">Cancelar</button><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button></div>
</form></div></div></div>
@endsection
@push('scripts')
<script>
document.getElementById('empresa_id')?.addEventListener('change',async function(){
    const r=await fetch(`/api/colaboradores?empresa_id=${this.value}`,{headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data=await r.json();const sel=document.getElementById('colabSel');sel.innerHTML='<option value="">Selecione</option>';
    data.forEach(c=>{const o=document.createElement('option');o.value=c.id;o.textContent=c.nome;sel.appendChild(o);});
});
</script>
@endpush
