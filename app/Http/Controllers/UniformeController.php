<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Uniforme, UniformeEstoque, EntregaUniforme, Tamanho, Colaborador, Empresa};
use Carbon\Carbon;

class UniformeController extends Controller {
    public function index(Request $r) {
        $q = Uniforme::query();
        if ($r->search) $q->where('nome','ilike',"%{$r->search}%");
        if ($r->tipo)   $q->where('tipo',$r->tipo);
        $uniformes = $q->orderBy('nome')->paginate(20)->withQueryString();
        $tamanhos  = Tamanho::orderBy('ordem')->get();
        $estoques  = UniformeEstoque::with('tamanho')->get()->groupBy('uniforme_id');
        return view('uniformes.index',compact('uniformes','tamanhos','estoques'));
    }
    public function create() { return view('uniformes.form',['uniforme'=>null]); }
    public function store(Request $r) {
        $r->validate(['nome'=>'required','tipo'=>'required']);
        Uniforme::create($r->only(['nome','tipo','descricao','fornecedor','custo_unitario','status']));
        return redirect()->route('uniformes.index')->with('success','Uniforme cadastrado!');
    }
    public function edit(Uniforme $uniforme) { return view('uniformes.form',compact('uniforme')); }
    public function update(Request $r, Uniforme $uniforme) {
        $r->validate(['nome'=>'required','tipo'=>'required']);
        $uniforme->update($r->only(['nome','tipo','descricao','fornecedor','custo_unitario','status']));
        return redirect()->route('uniformes.index')->with('success','Uniforme atualizado!');
    }
    public function destroy(Uniforme $uniforme) { $uniforme->delete(); return redirect()->route('uniformes.index')->with('success','Uniforme excluído!'); }
    public function show(Uniforme $uniforme) { return redirect()->route('uniformes.index'); }
    public function updateEstoque(Request $r, Uniforme $uniforme) {
        $r->validate(['tamanho_id'=>'required','quantidade'=>'required|integer|min:0']);
        UniformeEstoque::updateOrCreate(['uniforme_id'=>$uniforme->id,'tamanho_id'=>$r->tamanho_id],['quantidade'=>$r->quantidade,'minimo'=>$r->minimo??0]);
        return back()->with('success','Estoque atualizado!');
    }
    public function entregas(Request $r) {
        $q = EntregaUniforme::with(['colaborador','uniforme','tamanho','empresa']);
        if ($r->empresa_id) $q->where('empresa_id',$r->empresa_id);
        $entregas  = $q->orderByDesc('data_entrega')->paginate(20)->withQueryString();
        $uniformes_list = Uniforme::ativos()->orderBy('nome')->get();
        $tamanhos  = Tamanho::orderBy('ordem')->get();
        $empresas  = auth()->user()->isSuperAdmin() ? Empresa::ativas()->get() : collect([auth()->user()->empresa]);
        return view('uniformes.entregas',compact('entregas','uniformes_list','tamanhos','empresas'));
    }
    public function storeEntrega(Request $r) {
        $r->validate(['colaborador_id'=>'required','uniforme_id'=>'required','tamanho_id'=>'required','quantidade'=>'required|integer|min:1','data_entrega'=>'required|date']);
        $eid = $r->empresa_id ?? auth()->user()->empresa_id;
        EntregaUniforme::create(['empresa_id'=>$eid,'colaborador_id'=>$r->colaborador_id,'uniforme_id'=>$r->uniforme_id,'tamanho_id'=>$r->tamanho_id,'quantidade'=>$r->quantidade,'data_entrega'=>$r->data_entrega,'data_prevista_troca'=>$r->data_prevista_troca,'motivo'=>$r->motivo??'admissao','responsavel'=>$r->responsavel??auth()->user()->name,'observacoes'=>$r->observacoes]);
        $est = UniformeEstoque::where(['uniforme_id'=>$r->uniforme_id,'tamanho_id'=>$r->tamanho_id])->first();
        if ($est) $est->decrement('quantidade',$r->quantidade);
        return back()->with('success','Entrega registrada!');
    }
    public function ficha(Colaborador $colaborador) {
        $entregas = EntregaUniforme::with(['uniforme','tamanho'])->where('colaborador_id',$colaborador->id)->orderByDesc('data_entrega')->get();
        return view('uniformes.ficha',compact('colaborador','entregas'));
    }
}
