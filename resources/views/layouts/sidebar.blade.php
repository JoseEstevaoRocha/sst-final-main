@php
$currentRoute = Route::currentRouteName() ?? '';
$currentPath  = request()->path();

function sidebarActive(array $patterns): string {
    $route = Route::currentRouteName() ?? '';
    $path  = request()->path();
    foreach ($patterns as $p) {
        if (str_contains($p,'*') ? Str::is($p,$route) || Str::is($p,$path) : ($route===$p || $path===$p)) return 'active';
    }
    return '';
}

$nav = [
    ['id'=>'dashboard','label'=>'Dashboard','icon'=>'fas fa-chart-line','href'=>route('dashboard'),'match'=>['dashboard*'],'sub'=>[
        ['href'=>route('dashboard'),'icon'=>'fas fa-home','label'=>'Visão Geral'],
        ['href'=>route('dashboard.alertas'),'icon'=>'fas fa-bell','label'=>'Alertas'],
    ]],
    ['id'=>'org','label'=>'Organizacional','icon'=>'fas fa-sitemap','match'=>['empresas*','colaboradores*','setores*','funcoes*'],'sub'=>[
        ['href'=>route('empresas.index'),'icon'=>'fas fa-building','label'=>'Empresas'],
        ['href'=>route('setores.index'),'icon'=>'fas fa-layer-group','label'=>'Setores'],
        ['href'=>route('funcoes.index'),'icon'=>'fas fa-briefcase','label'=>'Funções'],
        ['href'=>route('colaboradores.index'),'icon'=>'fas fa-users','label'=>'Colaboradores'],
    ]],
    ['id'=>'ghe','label'=>'GHE & Riscos','icon'=>'fas fa-shield-alt','match'=>['ghes*','riscos*','gro*'],'sub'=>[
        ['href'=>route('ghes.index'),'icon'=>'fas fa-object-group','label'=>'Cadastro de GHE'],
        ['href'=>route('riscos.index'),'icon'=>'fas fa-exclamation-triangle','label'=>'Riscos'],
        ['href'=>route('gro.matriz'),'icon'=>'fas fa-table','label'=>'Matriz de Risco'],
    ]],
    ['id'=>'saude','label'=>'Saúde Ocupacional','icon'=>'fas fa-heartbeat','match'=>['asos*','clinicas*','exames-clinicos*'],'sub'=>[
        ['href'=>route('asos.index'),'icon'=>'fas fa-clipboard-list','label'=>'Gestão de ASO'],
        ['href'=>route('asos.vencidos'),'icon'=>'fas fa-calendar-times','label'=>'ASOs Vencidos'],
        ['href'=>route('asos.a-vencer'),'icon'=>'fas fa-clock','label'=>'A Vencer (30d)'],
        ['href'=>route('asos.agendamento'),'icon'=>'fas fa-calendar-plus','label'=>'Agendamento'],
        ['href'=>route('clinicas.index'),'icon'=>'fas fa-hospital','label'=>'Clínicas'],
        ['href'=>route('exames-clinicos.index'),'icon'=>'fas fa-stethoscope','label'=>'Exames Clínicos'],
    ]],
    ['id'=>'uniformes','label'=>'Uniformes','icon'=>'fas fa-tshirt','match'=>['uniformes*','tamanhos*'],'sub'=>[
        ['href'=>route('uniformes.index'),'icon'=>'fas fa-tshirt','label'=>'Catálogo'],
        ['href'=>route('tamanhos.index'),'icon'=>'fas fa-ruler','label'=>'Tamanhos'],
        ['href'=>route('uniformes.entregas'),'icon'=>'fas fa-box-open','label'=>'Entregas'],
    ]],
    ['id'=>'epis','label'=>'EPIs','icon'=>'fas fa-hard-hat','match'=>['epis*'],'sub'=>[
        ['href'=>route('epis.index'),'icon'=>'fas fa-hard-hat','label'=>'Catálogo de EPIs'],
        ['href'=>route('epis.entregas'),'icon'=>'fas fa-box-open','label'=>'Entregas'],
        ['href'=>route('epis.validade'),'icon'=>'fas fa-calendar-check','label'=>'Controle Validade'],
    ]],
    ['id'=>'maquinas','label'=>'Máquinas NR12','icon'=>'fas fa-cogs','match'=>['maquinas*','manutencoes*'],'sub'=>[
        ['href'=>route('maquinas.index'),'icon'=>'fas fa-industry','label'=>'Inventário'],
        ['href'=>route('manutencoes.index'),'icon'=>'fas fa-wrench','label'=>'Manutenções'],
    ]],
    ['id'=>'emergencia','label'=>'Emergência','icon'=>'fas fa-fire-extinguisher','match'=>['extintores*','brigada*','cipa*'],'sub'=>[
        ['href'=>route('extintores.index'),'icon'=>'fas fa-fire-extinguisher','label'=>'Extintores'],
        ['href'=>route('brigada.index'),'icon'=>'fas fa-user-shield','label'=>'Brigada'],
        ['href'=>route('cipa.index'),'icon'=>'fas fa-users-cog','label'=>'CIPA (NR05)'],
    ]],
    ['id'=>'whatsapp','label'=>'WhatsApp API','icon'=>'fab fa-whatsapp','match'=>['whatsapp*'],'sub'=>[
        ['href'=>route('whatsapp.index'),'icon'=>'fas fa-comment-dots','label'=>'Mensagens'],
        ['href'=>route('whatsapp.config'),'icon'=>'fas fa-cog','label'=>'Configuração'],
    ]],
    ['id'=>'ficha','label'=>'Ficha Funcionário','icon'=>'fas fa-id-card','match'=>['ficha*'],'sub'=>[
        ['href'=>route('ficha.index'),'icon'=>'fas fa-search','label'=>'Buscar Colaborador'],
    ]],
    ['id'=>'importacao','label'=>'Importação','icon'=>'fas fa-file-import','match'=>['importacao*'],'sub'=>[
        ['href'=>route('importacao.index'),'icon'=>'fas fa-users','label'=>'Colaboradores CSV/XLS'],
    ]],
    ['id'=>'relatorios','label'=>'Relatórios','icon'=>'fas fa-chart-bar','match'=>['relatorios*'],'sub'=>[
        ['href'=>route('relatorios.index'),'icon'=>'fas fa-chart-bar','label'=>'Dashboard Relatórios'],
        ['href'=>route('relatorios.asos'),'icon'=>'fas fa-clipboard-list','label'=>'Relatório ASOs'],
        ['href'=>route('relatorios.epis'),'icon'=>'fas fa-hard-hat','label'=>'Relatório EPIs'],
    ]],
    ['id'=>'config','label'=>'Configurações','icon'=>'fas fa-cog','match'=>['config*'],'sub'=>[
        ['href'=>route('config.index'),'icon'=>'fas fa-sliders-h','label'=>'Sistema'],
        ['href'=>route('config.usuarios'),'icon'=>'fas fa-user-cog','label'=>'Usuários'],
    ]],
];
@endphp

<aside class="sidebar" id="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        @php $logo=$sysName=null; try{$logo=\App\Http\Controllers\ConfigController::get('system_logo');$sysName=\App\Http\Controllers\ConfigController::get('system_name','SST Manager');}catch(\Exception $e){$sysName='SST Manager';} @endphp
        <div class="sidebar-logo">
            @if($logo && \Storage::disk('public')->exists($logo))
                <img src="{{ Storage::url($logo) }}" alt="{{ $sysName }}" class="brand-img">
            @else
                <div class="brand-icon-wrap"><i class="fas fa-shield-alt"></i></div>
            @endif
        </div>
        <div class="brand-text">
            <span class="brand-name">{{ $sysName }}</span>
            <span class="brand-version">v2.0</span>
        </div>
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Recolher">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        @foreach($nav as $item)
        @php $isActive = !empty(array_filter($item['match'] ?? [], fn($p) => Str::is($p, $currentRoute) || Str::is($p, $currentPath))); @endphp
        <div class="nav-group {{ $isActive ? 'open' : '' }}" data-id="{{ $item['id'] }}">
            <button class="nav-trigger {{ $isActive ? 'active' : '' }}" onclick="toggleNav('{{ $item['id'] }}')" title="{{ $item['label'] }}">
                <i class="{{ $item['icon'] }} nav-icon"></i>
                <span class="nav-label">{{ $item['label'] }}</span>
                @if(!empty($item['sub']))<i class="fas fa-chevron-right nav-arrow"></i>@endif
            </button>
            @if(!empty($item['sub']))
            <div class="nav-sub" id="sub-{{ $item['id'] }}">
                @foreach($item['sub'] as $s)
                @php $subActive = request()->url() === $s['href'] ? 'active' : ''; @endphp
                <a href="{{ $s['href'] }}" class="nav-sub-item {{ $subActive }}">
                    <i class="{{ $s['icon'] }}"></i>
                    <span>{{ $s['label'] }}</span>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </nav>

    {{-- User bottom --}}
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">{{ auth()->user()->initials }}</div>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
            <span class="sidebar-user-role">{{ auth()->user()->cargo ?? '' }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout" title="Sair"><i class="fas fa-sign-out-alt"></i></button>
        </form>
    </div>
</aside>
