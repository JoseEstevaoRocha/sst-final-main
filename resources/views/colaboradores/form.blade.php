@extends('layouts.app')
@section('title', $colaborador->exists ? 'Editar Colaborador' : 'Novo Colaborador')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $colaborador->exists ? 'Editar Colaborador' : 'Novo Colaborador' }}</h1>
    </div>
    <a href="{{ route('colaboradores.index') }}" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <form method="POST" action="{{ $colaborador->exists ? route('colaboradores.update',$colaborador->id) : route('colaboradores.store') }}">
        @csrf @if($colaborador->exists) @method('PUT') @endif
        <div class="form-grid">

            {{-- DADOS PESSOAIS --}}
            <div class="form-section"><i class="fas fa-user"></i> Dados Pessoais</div>
            <div class="form-group form-full">
                <label class="form-label">Nome Completo <span class="required">*</span></label>
                <input type="text" name="nome" value="{{ old('nome',$colaborador->nome??'') }}" class="form-control @error('nome') is-invalid @enderror" required>
                @error('nome')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">CPF <span class="required">*</span></label>
                <input type="text" name="cpf" value="{{ old('cpf',$colaborador->cpf??'') }}" class="form-control @error('cpf') is-invalid @enderror" maxlength="11" placeholder="Apenas números" required>
                @error('cpf')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">PIS/PASEP</label>
                <input type="text" name="pis" value="{{ old('pis',$colaborador->pis??'') }}" class="form-control" maxlength="11">
            </div>
            <div class="form-group">
                <label class="form-label">RG</label>
                <input type="text" name="rg" value="{{ old('rg',$colaborador->rg??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Data de Nascimento <span class="required">*</span></label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento',$colaborador->data_nascimento?->format('Y-m-d')??'') }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Sexo <span class="required">*</span></label>
                <select name="sexo" class="form-select" required>
                    <option value="M" {{ old('sexo',$colaborador->sexo??'')==='M'?'selected':'' }}>Masculino</option>
                    <option value="F" {{ old('sexo',$colaborador->sexo??'')==='F'?'selected':'' }}>Feminino</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Escolaridade</label>
                <select name="escolaridade" class="form-select">
                    <option value="">Selecione</option>
                    @foreach(['Ensino Fundamental Incompleto','Ensino Fundamental Completo','Ensino Médio Incompleto','Ensino Médio Completo','Superior Incompleto','Superior Completo','Pós-graduação','Mestrado'] as $e)
                    <option value="{{ $e }}" {{ old('escolaridade',$colaborador->escolaridade??'')===$e?'selected':'' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone',$colaborador->telefone??'') }}" class="form-control" placeholder="(11) 99999-0000">
            </div>
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email',$colaborador->email??'') }}" class="form-control">
            </div>

            {{-- VÍNCULO EMPREGATÍCIO --}}
            <div class="form-section"><i class="fas fa-building"></i> Vínculo Empregatício</div>
            <div class="form-group">
                <label class="form-label">Empresa <span class="required">*</span></label>
                <select name="empresa_id" id="empresa_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($empresas as $e)
                    <option value="{{ $e->id }}" {{ old('empresa_id',$colaborador->empresa_id??'')==$e->id?'selected':'' }}>{{ $e->nome_display }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Setor <span class="required">*</span></label>
                <select name="setor_id" id="setor_id" class="form-select" required>
                    <option value="">Selecione a empresa primeiro</option>
                    @foreach($setores as $s)
                    <option value="{{ $s->id }}" {{ old('setor_id',$colaborador->setor_id??'')==$s->id?'selected':'' }}>{{ $s->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Função <span class="required">*</span></label>
                <select name="funcao_id" id="funcao_id" class="form-select" required>
                    <option value="">Selecione o setor primeiro</option>
                    @foreach($funcoes as $f)
                    <option value="{{ $f->id }}" data-cbo="{{ $f->cbo }}" {{ old('funcao_id',$colaborador->funcao_id??'')==$f->id?'selected':'' }}>{{ $f->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- CBO — preenchido automaticamente pela função --}}
            <div class="form-group">
                <label class="form-label">CBO
                    <span style="font-size:10px;color:var(--text-3);font-weight:400;margin-left:4px">(preenchido pela função)</span>
                </label>
                <input type="hidden" name="cbo" id="cbo_input" value="{{ old('cbo', $colaborador->funcao?->cbo ?? $colaborador->cbo ?? '') }}">
                <div id="cbo_display" style="padding:8px 12px;background:var(--bg-alt);border:1px solid var(--border);border-radius:var(--r-sm);font-size:13px;font-family:monospace;color:var(--text-2);min-height:38px;display:flex;align-items:center">
                    {{ $colaborador->funcao?->cbo ?? ($colaborador->cbo ? $colaborador->cbo : '<span style="color:var(--text-3);font-style:italic;font-family:inherit">Selecione a função</span>') }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Matrícula</label>
                <input type="text" name="matricula" value="{{ old('matricula',$colaborador->matricula??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Matrícula eSocial</label>
                <input type="text" name="matricula_esocial" value="{{ old('matricula_esocial',$colaborador->matricula_esocial??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Data de Admissão <span class="required">*</span></label>
                <input type="date" name="data_admissao" value="{{ old('data_admissao',$colaborador->data_admissao?->format('Y-m-d')??'') }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Data de Demissão</label>
                <input type="date" name="data_demissao" value="{{ old('data_demissao',$colaborador->data_demissao?->format('Y-m-d')??'') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['Contratado','Demitido','Afastado','INSS'] as $st)
                    <option value="{{ $st }}" {{ old('status',$colaborador->status??'Contratado')===$st?'selected':'' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label checkbox-wrap">
                    <input type="checkbox" name="jovem_aprendiz" value="1" {{ old('jovem_aprendiz',$colaborador->jovem_aprendiz??false)?'checked':'' }}>
                    <span class="checkbox-box"></span>
                    <span>Jovem Aprendiz</span>
                </label>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes',$colaborador->observacoes??'') }}</textarea>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('colaboradores.index') }}" class="btn btn-ghost">Cancelar</a>
            @if($colaborador->exists)
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar alterações</button>
            @else
                <div style="display:flex;gap:8px;align-items:center">
                    <button type="submit" name="agendar" value="1" class="btn btn-secondary" title="Salva o colaborador e abre o agendamento de ASO Admissional">
                        <i class="fas fa-calendar-plus"></i> Salvar e Agendar ASO
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cadastrar colaborador
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Dados de setor e função atuais (para edição) ────────────────────────────
const currentSetorId  = '{{ old('setor_id',  $colaborador->setor_id  ?? '') }}';
const currentFuncaoId = '{{ old('funcao_id', $colaborador->funcao_id ?? '') }}';

// ── Empresa → Setores ────────────────────────────────────────────────────────
async function loadSetores(empresaId, selectSetorId = '') {
    const sel = document.getElementById('setor_id');
    sel.innerHTML = '<option value="">Carregando...</option>';
    resetFuncao();
    if (!empresaId) { sel.innerHTML = '<option value="">Selecione a empresa primeiro</option>'; return; }
    const res  = await fetch(`/api/setores?empresa_id=${empresaId}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    sel.innerHTML = '<option value="">Selecione o setor</option>';
    data.forEach(s => {
        const o = document.createElement('option');
        o.value = s.id; o.textContent = s.nome;
        if (String(s.id) === String(selectSetorId)) o.selected = true;
        sel.appendChild(o);
    });
    if (selectSetorId) loadFuncoes(selectSetorId, currentFuncaoId);
}

// ── Setor → Funções (com CBO) ────────────────────────────────────────────────
async function loadFuncoes(setorId, selectFuncaoId = '') {
    const sel = document.getElementById('funcao_id');
    sel.innerHTML = '<option value="">Carregando...</option>';
    resetCbo();
    if (!setorId) { sel.innerHTML = '<option value="">Selecione o setor primeiro</option>'; return; }
    const res  = await fetch(`/api/funcoes?setor_id=${setorId}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    sel.innerHTML = '<option value="">Selecione a função</option>';
    data.forEach(f => {
        const o = document.createElement('option');
        o.value = f.id; o.textContent = f.nome; o.dataset.cbo = f.cbo || '';
        if (String(f.id) === String(selectFuncaoId)) o.selected = true;
        sel.appendChild(o);
    });
    if (selectFuncaoId) updateCbo();
}

// ── CBO display ──────────────────────────────────────────────────────────────
function updateCbo() {
    const sel     = document.getElementById('funcao_id');
    const opt     = sel.options[sel.selectedIndex];
    const cbo     = opt && opt.dataset.cbo ? opt.dataset.cbo : '';
    const display = document.getElementById('cbo_display');
    const input   = document.getElementById('cbo_input');
    input.value   = cbo;
    display.innerHTML = cbo
        ? `<span>${cbo}</span>`
        : `<span style="color:var(--text-3);font-style:italic;font-family:inherit">Nenhum CBO cadastrado nesta função</span>`;
}

function resetFuncao() {
    document.getElementById('funcao_id').innerHTML = '<option value="">Selecione o setor primeiro</option>';
    resetCbo();
}

function resetCbo() {
    document.getElementById('cbo_input').value = '';
    document.getElementById('cbo_display').innerHTML = '<span style="color:var(--text-3);font-style:italic;font-family:inherit">Selecione a função</span>';
}

// ── Eventos ──────────────────────────────────────────────────────────────────
document.getElementById('empresa_id')?.addEventListener('change', function() {
    loadSetores(this.value);
});

document.getElementById('setor_id')?.addEventListener('change', function() {
    loadFuncoes(this.value);
});

document.getElementById('funcao_id')?.addEventListener('change', updateCbo);

// ── Inicialização (edição) ───────────────────────────────────────────────────
(function init() {
    const empresaId = document.getElementById('empresa_id')?.value;
    if (empresaId && currentSetorId) {
        // Já tem setor e função selecionados (modo edição): apenas atualiza CBO
        updateCbo();
    }
})();
</script>
@endpush
