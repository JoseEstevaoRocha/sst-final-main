<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{EPI, EPIEstoque, EntregaEPI, EpiMovimentacao, Colaborador, Empresa};
use Carbon\Carbon;

class EPIController extends Controller {
    public function index(Request $r) {
        $q = EPI::query();
        if ($r->search) $q->where(fn($sq)=>$sq->where('nome','ilike',"%{$r->search}%")->orWhere('numero_ca','ilike',"%{$r->search}%"));
        if ($r->tipo)   $q->where('tipo',$r->tipo);
        if ($r->status) $q->where('status',$r->status);
        $epis    = $q->withCount('entregas')->orderBy('nome')->paginate(20)->withQueryString();
        $empresas = auth()->user()->isSuperAdmin() ? Empresa::ativas()->get() : collect([auth()->user()->empresa]);
        $dash    = $this->calcDash();
        return view('epi.index',compact('epis','dash','empresas'));
    }
    public function create() { return view('epi.form',['epi'=>null]); }
    public function store(Request $r) {
        $r->validate(['nome'=>'required','tipo'=>'required']);
        EPI::create($r->only(['nome','descricao','tipo','numero_ca','validade_ca','fornecedor','fabricante','vida_util_dias','estoque_minimo','unidade','custo_unitario','status']));
        return redirect()->route('epis.index')->with('success','EPI cadastrado!');
    }
    public function edit(EPI $epi) { return view('epi.form',compact('epi')); }
    public function update(Request $r, EPI $epi) {
        $r->validate(['nome'=>'required','tipo'=>'required']);
        $epi->update($r->only(['nome','descricao','tipo','numero_ca','validade_ca','fornecedor','fabricante','vida_util_dias','estoque_minimo','unidade','custo_unitario','status']));
        return redirect()->route('epis.index')->with('success','EPI atualizado!');
    }
    public function destroy(EPI $epi) { $epi->update(['status'=>'Inativo']); return redirect()->route('epis.index')->with('success','EPI inativado!'); }
    public function show(EPI $epi) { return redirect()->route('epis.index'); }
    public function dashboard() {
        return view('epi.dashboard',['dash'=>$this->calcDash(),'estoquesBaixos'=>EPIEstoque::with(['epi','empresa'])->whereColumn('quantidade','<=',\DB::raw('(SELECT estoque_minimo FROM epis WHERE epis.id=epi_estoques.epi_id)'))->get()]);
    }
    public function movimentar(Request $r, EPI $epi) {
        $r->validate(['empresa_id'=>'required','tipo'=>'required|in:entrada,saida,ajuste','quantidade'=>'required|integer|min:1']);
        $eid = (int)$r->empresa_id; $qty = (int)$r->quantidade;
        $est = EPIEstoque::firstOrCreate(['epi_id'=>$epi->id,'empresa_id'=>$eid],['quantidade'=>0]);
        $novo = $r->tipo==='entrada' ? $est->quantidade+$qty : max(0,$est->quantidade-$qty);
        $est->update(['quantidade'=>$novo]);
        \App\Models\EpiMovimentacao::create(['epi_id'=>$epi->id,'empresa_id'=>$eid,'tipo'=>$r->tipo,'quantidade'=>$qty,'motivo'=>$r->motivo,'usuario'=>auth()->user()->name]);
        return back()->with('success',"Estoque atualizado! Saldo: {$novo}");
    }
    public function entregas(Request $r) {
        $q = EntregaEPI::with(['colaborador','epi','empresa']);
        if ($r->empresa_id) $q->where('empresa_id',$r->empresa_id);
        if ($r->epi_id)     $q->where('epi_id',$r->epi_id);
        $entregas = $q->orderByDesc('data_entrega')->paginate(20)->withQueryString();
        $epis_list = EPI::ativos()->orderBy('nome')->get();
        $empresas  = auth()->user()->isSuperAdmin() ? Empresa::ativas()->get() : collect([auth()->user()->empresa]);
        return view('epi.entregas',compact('entregas','epis_list','empresas'));
    }
    public function storeEntrega(Request $r) {
        $r->validate(['colaborador_id'=>'required|exists:colaboradores,id','epi_id'=>'required|exists:epis,id','quantidade'=>'required|integer|min:1','data_entrega'=>'required|date']);
        $epi = EPI::find($r->epi_id);
        $troca = $r->data_prevista_troca ?: ($epi->vida_util_dias ? Carbon::parse($r->data_entrega)->addDays($epi->vida_util_dias)->format('Y-m-d') : null);
        $eid = $r->empresa_id ?? auth()->user()->empresa_id;
        EntregaEPI::create(['empresa_id'=>$eid,'colaborador_id'=>$r->colaborador_id,'epi_id'=>$r->epi_id,'quantidade'=>$r->quantidade,'tamanho'=>$r->tamanho,'data_entrega'=>$r->data_entrega,'data_prevista_troca'=>$troca,'responsavel'=>$r->responsavel??auth()->user()->name,'observacoes'=>$r->observacoes,'status'=>'Ativo']);
        // Decrementar estoque
        $est = EPIEstoque::firstOrCreate(['epi_id'=>$r->epi_id,'empresa_id'=>$eid],['quantidade'=>0]);
        $est->decrement('quantidade',$r->quantidade);
        return back()->with('success','Entrega registrada e estoque atualizado!');
    }
    public function validade() {
        $vencidos  = EntregaEPI::with(['colaborador','epi'])->where('status','Ativo')->where('data_prevista_troca','<',today())->orderBy('data_prevista_troca')->paginate(20);
        $aVencer   = EntregaEPI::with(['colaborador','epi'])->where('status','Ativo')->whereBetween('data_prevista_troca',[today(),today()->addDays(60)])->orderBy('data_prevista_troca')->paginate(20);
        return view('epi.validade',compact('vencidos','aVencer'));
    }
    public function ficha(Colaborador $colaborador) {
        $entregas = EntregaEPI::with('epi')->where('colaborador_id',$colaborador->id)->orderByDesc('data_entrega')->get();
        return view('epi.ficha',compact('colaborador','entregas'));
    }
    private function calcDash(): array {
        $hoje = today(); $em60 = today()->addDays(60);
        return ['total_ativos'=>EPI::where('status','Ativo')->count(),'vencidos'=>EntregaEPI::where('status','Ativo')->where('data_prevista_troca','<',$hoje)->count(),'a_vencer_60'=>EntregaEPI::where('status','Ativo')->whereBetween('data_prevista_troca',[$hoje,$em60])->count(),'entregas_mes'=>EntregaEPI::where('created_at','>=',now()->startOfMonth())->count(),'estoque_baixo'=>EPIEstoque::whereColumn('quantidade','<=',\DB::raw('(SELECT estoque_minimo FROM epis WHERE epis.id=epi_estoques.epi_id)'))->count()];
    }
}
