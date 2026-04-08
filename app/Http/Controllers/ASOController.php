<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{ASO, Colaborador, Clinica, Empresa, WhatsappMensagem};
use Carbon\Carbon;

class ASOController extends Controller {

    public function index(Request $r) {
        $hoje = today(); $em30 = today()->addDays(30);
        $user = auth()->user();
        $cW   = $user->isSuperAdmin() ? [] : ['empresa_id' => $user->empresa_id];

        $q = ASO::with(['colaborador.funcao','colaborador.setor','empresa','clinica'])->where($cW);
        if ($r->search)    $q->whereHas('colaborador',fn($sq)=>$sq->where('nome','ilike',"%{$r->search}%"));
        if ($r->tipo)      $q->where('tipo',$r->tipo);
        if ($r->status)    $q->where('status_logistico',$r->status);
        if ($r->resultado) $q->where('resultado',$r->resultado);
        $asos  = $q->orderBy('data_vencimento')->paginate(20)->withQueryString();
        $stats = [
            'total'     => ASO::where($cW)->count(),
            'vencidos'  => ASO::where($cW)->where('data_vencimento','<',$hoje)->count(),
            'a_vencer'  => ASO::where($cW)->whereBetween('data_vencimento',[$hoje,$em30])->count(),
            'agendados' => ASO::where($cW)->where('status_logistico','agendado')->count(),
            'em_dia'    => ASO::where($cW)->where('data_vencimento','>=',$hoje)->count(),
        ];
        $empresas = $user->isSuperAdmin() ? Empresa::ativas()->get() : collect();
        return view('aso.index',compact('asos','stats','empresas'));
    }

    public function create() {
        $clinicas = Clinica::ativas()->orderBy('nome')->get();
        $empresas = auth()->user()->isSuperAdmin() ? Empresa::ativas()->get() : collect([auth()->user()->empresa]);
        return view('aso.form',['aso'=>null,'clinicas'=>$clinicas,'empresas'=>$empresas]);
    }

    public function store(Request $r) {
        $r->validate(['colaborador_id'=>'required|exists:colaboradores,id','tipo'=>'required']);
        ASO::create(array_merge(
            $r->only(['empresa_id','colaborador_id','clinica_id','tipo','data_exame','data_vencimento','resultado','clinica_nome','medico_responsavel','status_logistico','observacoes','exames_complementares']),
            ['resultado'=>$r->resultado??'pendente','status_logistico'=>$r->status_logistico??'pendente','empresa_id'=>$r->empresa_id??auth()->user()->empresa_id]
        ));
        return redirect()->route('asos.index')->with('success','ASO cadastrado!');
    }

    public function edit(ASO $aso) {
        $clinicas = Clinica::ativas()->get();
        $empresas = auth()->user()->isSuperAdmin() ? Empresa::ativas()->get() : collect([auth()->user()->empresa]);
        return view('aso.form',compact('aso','clinicas','empresas'));
    }

    public function update(Request $r, ASO $aso) {
        $r->validate(['colaborador_id'=>'required','tipo'=>'required']);
        $aso->update($r->only(['empresa_id','colaborador_id','clinica_id','tipo','data_exame','data_vencimento','data_agendada','horario_agendado','exames_complementares','resultado','clinica_nome','medico_responsavel','status_logistico','observacoes']));
        return redirect()->route('asos.index')->with('success','ASO atualizado!');
    }

    public function destroy(ASO $aso) {
        $aso->delete();
        return redirect()->route('asos.index')->with('success','ASO excluído!');
    }

    public function show(ASO $aso) { return redirect()->route('asos.index'); }

    public function vencidos(Request $r) {
        $user = auth()->user();
        $cW   = $user->isSuperAdmin() ? [] : ['empresa_id' => $user->empresa_id];

        $q = ASO::with(['colaborador.funcao','colaborador.setor','colaborador.empresa','empresa','clinica'])
            ->where($cW)
            ->where('data_vencimento','<',today());

        if ($r->mes) {
            $q->whereMonth('data_vencimento', $r->mes)
              ->whereYear('data_vencimento', $r->ano ?? today()->year);
        }
        if ($r->search) {
            $q->whereHas('colaborador',fn($sq)=>$sq->where('nome','ilike',"%{$r->search}%"));
        }

        $asos     = $q->orderBy('data_vencimento')->paginate(50)->withQueryString();
        $clinicas = Clinica::ativas()->orderBy('nome')->get();
        $empresas = $user->isSuperAdmin() ? Empresa::ativas()->get() : collect();
        return view('aso.vencidos',compact('asos','clinicas','empresas'));
    }

    public function aVencer() {
        $user = auth()->user();
        $cW   = $user->isSuperAdmin() ? [] : ['empresa_id' => $user->empresa_id];
        $asos = ASO::with(['colaborador.funcao','empresa'])
            ->where($cW)
            ->whereBetween('data_vencimento',[today(),today()->addDays(30)])
            ->orderBy('data_vencimento')->paginate(25);
        return view('aso.a_vencer',compact('asos'));
    }

    public function historico() {
        $user = auth()->user();
        $cW   = $user->isSuperAdmin() ? [] : ['empresa_id' => $user->empresa_id];
        $asos = ASO::with(['colaborador','empresa'])->where($cW)->orderByDesc('created_at')->paginate(25);
        return view('aso.historico',compact('asos'));
    }

    public function agendamento() {
        $user     = auth()->user();
        $cW       = $user->isSuperAdmin() ? [] : ['empresa_id' => $user->empresa_id];
        $pendentes = ASO::with(['colaborador','empresa'])->where($cW)->where('status_logistico','pendente')->whereNotNull('data_vencimento')->orderBy('data_vencimento')->paginate(20);
        $clinicas  = Clinica::ativas()->get();
        return view('aso.agendamento',compact('pendentes','clinicas'));
    }

    public function updateLogistica(Request $r, ASO $aso) {
        $r->validate(['status_logistico'=>'required']);
        $aso->update(['status_logistico'=>$r->status_logistico]);
        if (request()->expectsJson()) return response()->json(['ok'=>true]);
        return back()->with('success','Status atualizado!');
    }

    // ── AGENDAMENTO INDIVIDUAL ────────────────────────────────────────────
    public function agendar(Request $r, ASO $aso) {
        $r->validate([
            'data_agendada'        => 'required|date',
            'horario_agendado'     => 'nullable|date_format:H:i',
            'exames_complementares'=> 'nullable|string',
            'clinica_id'           => 'nullable|exists:clinicas,id',
        ]);

        $aso->update([
            'data_agendada'         => $r->data_agendada,
            'horario_agendado'      => $r->horario_agendado,
            'exames_complementares' => $r->exames_complementares,
            'status_logistico'      => 'agendado',
            'clinica_id'            => $r->clinica_id ?? $aso->clinica_id,
        ]);

        // Criar mensagem WhatsApp se solicitado
        if ($r->enviar_whatsapp && $r->clinica_id) {
            $clinica = Clinica::find($r->clinica_id);
            $colab   = $aso->colaborador;
            $texto   = $this->montarMensagemClinica($aso, $colab, $clinica, $r->exames_complementares);

            $msg = WhatsappMensagem::create([
                'empresa_id'     => $aso->empresa_id,
                'colaborador_id' => $aso->colaborador_id,
                'clinica_id'     => $r->clinica_id,
                'aso_id'         => $aso->id,
                'tipo_exame'     => $aso->tipo,
                'mensagem_texto' => $texto,
                'status'         => 'pendente',
                'data_agendada'  => $r->data_agendada,
                'horario_agendado' => $r->horario_agendado,
                'usuario_envio'  => auth()->id(),
            ]);

            return redirect()->route('whatsapp.url', $msg->id)
                ->with('success','ASO agendado! Abrindo WhatsApp...');
        }

        return back()->with('success','ASO agendado para '.Carbon::parse($r->data_agendada)->format('d/m/Y').'!');
    }

    // ── AGENDAMENTO EM LOTE ───────────────────────────────────────────────
    public function agendarLote(Request $r) {
        $r->validate([
            'ids'                  => 'required|array|min:1',
            'ids.*'                => 'exists:asos,id',
            'data_agendada'        => 'required|date',
            'horario_agendado'     => 'nullable|date_format:H:i',
            'exames_complementares'=> 'nullable|string',
        ]);

        $updated = ASO::whereIn('id', $r->ids)->update([
            'data_agendada'         => $r->data_agendada,
            'horario_agendado'      => $r->horario_agendado,
            'exames_complementares' => $r->exames_complementares,
            'status_logistico'      => 'agendado',
        ]);

        if ($r->gerar_relatorio) {
            return redirect()->route('asos.relatorio-clinica', [
                'ids'            => implode(',', $r->ids),
                'data_agendada'  => $r->data_agendada,
            ]);
        }

        return back()->with('success', "$updated ASO(s) agendados para ".Carbon::parse($r->data_agendada)->format('d/m/Y').'!');
    }

    // ── RELATÓRIO PARA CLÍNICA ────────────────────────────────────────────
    public function relatorioClinica(Request $r) {
        $ids = $r->ids ? explode(',', $r->ids) : [];

        $asos = ASO::with(['colaborador.funcao','colaborador.setor','colaborador.empresa','empresa','clinica'])
            ->when($r->ids, fn($q) => $q->whereIn('id', $ids))
            ->when(!$r->ids && $r->empresa_id, fn($q) => $q->where('empresa_id', $r->empresa_id)->where('data_vencimento','<',today()))
            ->orderBy('data_agendada')
            ->orderBy('horario_agendado')
            ->get();

        $empresa = $asos->first()?->empresa;
        return view('aso.relatorio_clinica', compact('asos','empresa'));
    }

    // ── HELPER ────────────────────────────────────────────────────────────
    private function montarMensagemClinica(ASO $aso, $colab, $clinica, ?string $exames): string {
        $tipos = ['admissional'=>'Admissional','periodico'=>'Periódico','demissional'=>'Demissional','retorno'=>'Retorno ao Trabalho','mudanca_funcao'=>'Mudança de Função'];
        $tipo  = $tipos[$aso->tipo] ?? $aso->tipo;
        $data  = $aso->data_agendada?->format('d/m/Y') ?? '—';
        $hora  = $aso->horario_agendado ? substr($aso->horario_agendado, 0, 5) : '';
        $nome  = $colab?->nome ?? '—';
        $cpf   = $colab?->cpf ?? '—';
        $nasc  = $colab?->data_nascimento?->format('d/m/Y') ?? '—';
        $setor = $colab?->setor?->nome ?? '—';
        $funcao= $colab?->funcao?->nome ?? '—';
        $emp   = $aso->empresa?->razao_social ?? $aso->empresa?->nome_display ?? '—';

        $msg = "Olá, *{$clinica->nome}*!\n\n";
        $msg .= "Agendamento de exame ocupacional:\n\n";
        $msg .= "🏢 *Empresa:* {$emp}\n";
        $msg .= "👤 *Colaborador:* {$nome}\n";
        $msg .= "📋 *CPF:* {$cpf}\n";
        $msg .= "🎂 *Nascimento:* {$nasc}\n";
        $msg .= "🏭 *Setor:* {$setor}\n";
        $msg .= "💼 *Função:* {$funcao}\n";
        $msg .= "🔬 *Tipo de Exame:* {$tipo}\n";
        $msg .= "📅 *Data:* {$data}".($hora ? " às {$hora}" : "")."\n";
        if ($exames) $msg .= "🧪 *Exames Complementares:* {$exames}\n";
        $msg .= "\nAguardamos confirmação. Obrigado!";

        return $msg;
    }
}
