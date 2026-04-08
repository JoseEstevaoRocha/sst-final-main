<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Maquina, Setor, Empresa};

class MaquinaController extends Controller {
    public function index(Request $r) {
        $q = Maquina::with(['setor','empresa']);
        if ($r->search) $q->where(fn($sq)=>$sq->where('nome','ilike',"%{$r->search}%")->orWhere('numero_serie','ilike',"%{$r->search}%"));
        if ($r->status) $q->where('status',$r->status);
        $maquinas = $q->orderBy('nome')->paginate(20)->withQueryString();
        $setores  = Setor::orderBy('nome')->get();
        $stats = ['total'=>Maquina::count(),'operacionais'=>Maquina::where('status','operacional')->count(),'inativas'=>Maquina::where('status','inativo')->count()];
        return view('maquinas.index',compact('maquinas','setores','stats'));
    }
    public function create() {
        $setores  = Setor::orderBy('nome')->get();
        $empresas = auth()->user()->hasRole('super-admin') ? Empresa::ativas()->get() : collect();
        return view('maquinas.form',['maquina'=>null,'setores'=>$setores,'empresas'=>$empresas]);
    }
    public function store(Request $r) {
        $r->validate(['nome'=>'required','empresa_id'=>auth()->user()->hasRole('super-admin')?'required':'nullable']);
        $data = $r->only(['nome','marca','modelo','numero_serie','ano_fabricacao','setor_id','status','observacoes']);
        $data['empresa_id'] = auth()->user()->empresa_id ?: $r->empresa_id;
        Maquina::create($data);
        return redirect()->route('maquinas.index')->with('success','Máquina cadastrada!');
    }
    public function edit(Maquina $maquina) {
        $setores  = Setor::orderBy('nome')->get();
        $empresas = auth()->user()->hasRole('super-admin') ? Empresa::ativas()->get() : collect();
        return view('maquinas.form',compact('maquina','setores','empresas'));
    }
    public function update(Request $r, Maquina $maquina) {
        $r->validate(['nome'=>'required']);
        $maquina->update($r->only(['nome','marca','modelo','numero_serie','ano_fabricacao','setor_id','status','observacoes']));
        return redirect()->route('maquinas.index')->with('success','Máquina atualizada!');
    }
    public function destroy(Maquina $maquina) { $maquina->delete(); return redirect()->route('maquinas.index')->with('success','Máquina excluída!'); }
    public function show(Maquina $m) { return redirect()->route('maquinas.index'); }
    public function checklist(Maquina $maquina) { return view('maquinas.checklist',compact('maquina')); }
}
