<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, EmpresaController,
    ColaboradorController, SetorController, FuncaoController,
    ASOController, EPIController, UniformeController, TamanhoController,
    GHEController, RiscoController, MaquinaController, ManutencaoController,
    ExtintorController, BrigadaController, CipaController,
    WhatsAppController, ClinicaController, FichaController,
    ImportacaoController, RelatorioController, ConfigController,
    ApiController, ExameClinicoController
};

// ── AUTENTICAÇÃO ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── ÁREA PROTEGIDA ────────────────────────────────────────────────────────
Route::middleware(['auth', 'tenant'])->group(function () {

    // Dashboard
    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/alertas', [DashboardController::class, 'alertas'])->name('dashboard.alertas');

    // ── ORGANIZACIONAL ────────────────────────────────────────────
    Route::middleware('permission:empresas.view')->group(function () {
        Route::resource('empresas', EmpresaController::class);
    });

    Route::middleware('permission:colaboradores.view')->group(function () {
        Route::resource('colaboradores', ColaboradorController::class)->parameters(['colaboradores' => 'colaborador']);
        Route::delete('/colaboradores/bulk/destroy', [ColaboradorController::class, 'bulkDestroy'])->name('colaboradores.bulk-destroy')->middleware('permission:colaboradores.delete');
        Route::get('/colaboradores/{colaborador}/historico', [ColaboradorController::class, 'historico'])->name('colaboradores.historico');
    });

    Route::resource('setores', SetorController::class)->parameters(['setores' => 'setor']);
    Route::resource('funcoes', FuncaoController::class)->parameters(['funcoes' => 'funcao']);

    // ── EXAMES CLÍNICOS ───────────────────────────────────────────
    Route::resource('exames-clinicos', ExameClinicoController::class)->parameters(['exames-clinicos' => 'exame']);
    Route::post('/exames-clinicos/atribuir-em-lote', [ExameClinicoController::class, 'atribuirEmLote'])->name('exames-clinicos.lote');
    // Exames do Setor
    Route::get('/setores/{setor}/exames',                    [ExameClinicoController::class, 'setorExames'])->name('setores.exames');
    Route::post('/setores/{setor}/exames',                   [ExameClinicoController::class, 'setorAddExame'])->name('setores.exames.add');
    Route::delete('/setores/{setor}/exames/{exame}',         [ExameClinicoController::class, 'setorRemoveExame'])->name('setores.exames.remove');
    // Exames da Função
    Route::get('/funcoes/{funcao}/exames',                   [ExameClinicoController::class, 'funcaoExames'])->name('funcoes.exames');
    Route::post('/funcoes/{funcao}/exames',                  [ExameClinicoController::class, 'funcaoAddExame'])->name('funcoes.exames.add');
    Route::post('/funcoes/{funcao}/exames/importar-setor',   [ExameClinicoController::class, 'funcaoImportarSetor'])->name('funcoes.exames.importar');
    Route::delete('/funcoes/{funcao}/exames/{exame}',        [ExameClinicoController::class, 'funcaoRemoveExame'])->name('funcoes.exames.remove');

    // ── SAÚDE OCUPACIONAL ─────────────────────────────────────────
    Route::middleware('permission:asos.view')->group(function () {
        Route::get('/asos/vencidos',             [ASOController::class, 'vencidos'])->name('asos.vencidos');
        Route::get('/asos/a-vencer',             [ASOController::class, 'aVencer'])->name('asos.a-vencer');
        Route::get('/asos/historico',            [ASOController::class, 'historico'])->name('asos.historico');
        Route::get('/asos/agendamento',          [ASOController::class, 'agendamento'])->name('asos.agendamento');
        Route::get('/asos/relatorio-clinica',    [ASOController::class, 'relatorioClinica'])->name('asos.relatorio-clinica');
        Route::post('/asos/agendar-lote',        [ASOController::class, 'agendarLote'])->name('asos.agendar-lote');
        Route::resource('asos', ASOController::class);
        Route::post('/asos/{aso}/logistica',     [ASOController::class, 'updateLogistica'])->name('asos.logistica');
        Route::post('/asos/{aso}/agendar',       [ASOController::class, 'agendar'])->name('asos.agendar');
    });

    Route::resource('clinicas', ClinicaController::class);

    // WhatsApp
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/',                          [WhatsAppController::class, 'index'])->name('index');
        Route::post('/preparar',                 [WhatsAppController::class, 'preparar'])->name('preparar');
        Route::post('/{msg}/enviar',             [WhatsAppController::class, 'enviar'])->name('enviar');
        Route::get('/{msg}/url',                 [WhatsAppController::class, 'getUrl'])->name('url');
        Route::post('/{msg}/resposta',           [WhatsAppController::class, 'resposta'])->name('resposta');
        Route::post('/{msg}/concluir',           [WhatsAppController::class, 'concluir'])->name('concluir');
        Route::get('/config',                    [WhatsAppController::class, 'config'])->name('config');
        Route::post('/config',                   [WhatsAppController::class, 'saveConfig'])->name('config.save');
    });

    // ── GHE & RISCOS ──────────────────────────────────────────────
    Route::middleware('permission:ghe.view')->group(function () {
        Route::resource('ghes', GHEController::class);
        Route::post('/ghes/{ghe}/riscos',        [GHEController::class, 'addRisco'])->name('ghes.riscos.add');
        Route::delete('/ghes/{ghe}/riscos/{risco}',[GHEController::class, 'removeRisco'])->name('ghes.riscos.remove');
        Route::get('/gro/matriz',                [GHEController::class, 'matriz'])->name('gro.matriz');
    });
    Route::resource('riscos', RiscoController::class);

    // ── EPIs ──────────────────────────────────────────────────────
    Route::middleware('permission:epis.view')->group(function () {
        Route::get('/epis/dashboard',        [EPIController::class, 'dashboard'])->name('epis.dashboard');
        Route::get('/epis/entregas',         [EPIController::class, 'entregas'])->name('epis.entregas');
        Route::post('/epis/entregas',        [EPIController::class, 'storeEntrega'])->name('epis.entregas.store');
        Route::get('/epis/validade',         [EPIController::class, 'validade'])->name('epis.validade');
        Route::get('/epis/ficha/{colaborador}',[EPIController::class,'ficha'])->name('epis.ficha');
        Route::resource('epis', EPIController::class);
        Route::post('/epis/{epi}/movimentar',[EPIController::class, 'movimentar'])->name('epis.movimentar');
    });

    // ── UNIFORMES ─────────────────────────────────────────────────
    Route::middleware('permission:uniformes.view')->group(function () {
        Route::get('/uniformes/entregas',    [UniformeController::class, 'entregas'])->name('uniformes.entregas');
        Route::post('/uniformes/entregas',   [UniformeController::class, 'storeEntrega'])->name('uniformes.entregas.store');
        Route::get('/uniformes/ficha/{colaborador}',[UniformeController::class,'ficha'])->name('uniformes.ficha');
        Route::resource('uniformes', UniformeController::class);
        Route::post('/uniformes/{uniforme}/estoque', [UniformeController::class, 'updateEstoque'])->name('uniformes.estoque');
    });
    Route::resource('tamanhos', TamanhoController::class);
    Route::post('/tamanhos/seed', [TamanhoController::class, 'seed'])->name('tamanhos.seed');

    // ── MÁQUINAS NR12 ─────────────────────────────────────────────
    Route::middleware('permission:maquinas.view')->group(function () {
        Route::get('/manutencoes',                   [ManutencaoController::class, 'geral'])->name('manutencoes.index');
        Route::post('/manutencoes',                  [ManutencaoController::class, 'geralStore'])->name('manutencoes.geral.store');
        Route::get('/manutencoes/modelo-csv',        [ManutencaoController::class, 'modeloCsv'])->name('manutencoes.modelo-csv');
        Route::post('/manutencoes/importar',         [ManutencaoController::class, 'importar'])->name('manutencoes.importar');
        Route::resource('maquinas', MaquinaController::class);
        Route::resource('maquinas.manutencoes', ManutencaoController::class)->shallow();
        Route::get('/maquinas/{maquina}/checklist', [MaquinaController::class, 'checklist'])->name('maquinas.checklist');
    });

    // ── EMERGÊNCIA ────────────────────────────────────────────────
    Route::middleware('permission:emergencia.view')->group(function () {
        Route::resource('extintores', ExtintorController::class);
        Route::post('/extintores/{extintor}/inspecao', [ExtintorController::class, 'inspecao'])->name('extintores.inspecao');
        Route::get('/brigada',           [BrigadaController::class, 'index'])->name('brigada.index');
        Route::post('/brigada',          [BrigadaController::class, 'store'])->name('brigada.store');
        Route::delete('/brigada/{id}',   [BrigadaController::class, 'destroy'])->name('brigada.destroy');
        Route::get('/cipa',              [CipaController::class, 'index'])->name('cipa.index');
        Route::post('/cipa',             [CipaController::class, 'store'])->name('cipa.store');
        Route::delete('/cipa/{id}',      [CipaController::class, 'destroy'])->name('cipa.destroy');
    });

    // ── FICHA DO FUNCIONÁRIO ──────────────────────────────────────
    Route::get('/ficha',                      [FichaController::class, 'index'])->name('ficha.index');
    Route::get('/ficha/{colaborador}',        [FichaController::class, 'show'])->name('ficha.show');
    Route::get('/ficha/{colaborador}/pdf',    [FichaController::class, 'pdf'])->name('ficha.pdf');
    Route::get('/ficha/{colaborador}/epi-pdf',[FichaController::class, 'epiPdf'])->name('ficha.epi-pdf');
    Route::get('/ficha/{colaborador}/uni-pdf',[FichaController::class, 'uniformePdf'])->name('ficha.uni-pdf');

    // ── IMPORTAÇÃO ────────────────────────────────────────────────
    Route::middleware('permission:colaboradores.import')->group(function () {
        Route::get('/importacao',             [ImportacaoController::class, 'index'])->name('importacao.index');
        Route::post('/importacao/colaboradores',[ImportacaoController::class,'importarColaboradores'])->name('importacao.colaboradores');
        Route::post('/importacao/epis',       [ImportacaoController::class, 'importarEpis'])->name('importacao.epis');
        Route::get('/importacao/modelo/{tipo}',[ImportacaoController::class,'modelo'])->name('importacao.modelo');
    });

    // ── RELATÓRIOS ────────────────────────────────────────────────
    Route::middleware('permission:relatorios.view')->group(function () {
        Route::get('/relatorios',              [RelatorioController::class, 'index'])->name('relatorios.index');
        Route::get('/relatorios/asos',         [RelatorioController::class, 'asos'])->name('relatorios.asos');
        Route::get('/relatorios/epis',         [RelatorioController::class, 'epis'])->name('relatorios.epis');
        Route::get('/relatorios/uniformes',    [RelatorioController::class, 'uniformes'])->name('relatorios.uniformes');
        Route::get('/relatorios/extintores',   [RelatorioController::class, 'extintores'])->name('relatorios.extintores');
        Route::get('/relatorios/maquinas',     [RelatorioController::class, 'maquinas'])->name('relatorios.maquinas');
        Route::get('/relatorios/export/{tipo}',[RelatorioController::class, 'export'])->name('relatorios.export')->middleware('permission:relatorios.export');
    });

    // ── CONFIGURAÇÕES ─────────────────────────────────────────────
    Route::middleware('permission:config.view')->group(function () {
        Route::get('/configuracoes',      [ConfigController::class, 'index'])->name('config.index');
        Route::post('/configuracoes',     [ConfigController::class, 'save'])->name('config.save')->middleware('permission:config.edit');
        Route::post('/configuracoes/logo',[ConfigController::class, 'logo'])->name('config.logo')->middleware('permission:config.edit');
        Route::get('/configuracoes/usuarios', [ConfigController::class, 'usuarios'])->name('config.usuarios')->middleware('permission:users.view');
        Route::post('/configuracoes/usuarios',[ConfigController::class, 'storeUsuario'])->name('config.usuarios.store')->middleware('permission:users.create');
        Route::put('/configuracoes/usuarios/{user}',[ConfigController::class, 'updateUsuario'])->name('config.usuarios.update')->middleware('permission:users.edit');
        Route::delete('/configuracoes/usuarios/{user}',[ConfigController::class, 'destroyUsuario'])->name('config.usuarios.destroy')->middleware('permission:users.delete');
    });

    // ── API AJAX ──────────────────────────────────────────────────
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/setores',           [ApiController::class, 'setores'])->name('setores');
        Route::get('/funcoes',           [ApiController::class, 'funcoes'])->name('funcoes');
        Route::get('/colaboradores',     [ApiController::class, 'colaboradores'])->name('colaboradores');
        Route::get('/maquinas',          [ApiController::class, 'maquinas'])->name('maquinas');
        Route::get('/clinicas',          [ApiController::class, 'clinicas'])->name('clinicas');
        Route::get('/search',            [ApiController::class, 'search'])->name('search');
        Route::get('/notificacoes',      [ApiController::class, 'notificacoes'])->name('notificacoes');
        Route::get('/dashboard/stats',   [ApiController::class, 'dashboardStats'])->name('dashboard.stats');
        Route::get('/dashboard/charts',  [ApiController::class, 'dashboardCharts'])->name('dashboard.charts');
    });
});
