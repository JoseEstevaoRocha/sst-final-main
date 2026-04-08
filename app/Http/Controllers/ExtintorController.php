<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Extintor, InspecaoExtintor, Setor};

class ExtintorController extends Controller {
    public function index(Request $r) {
        $q = Extintor::with(['setor']);
        if ($r->status) $q->where('status',$r->status);
        if ($r->tipo)   $q->where('tipo',$r->tipo);
        $extintores = $q->orderBy('status')->orderBy('proxima_recarga')->paginate(20)->withQueryString();
        $stats = ['total'=>Extintor::count(),'vencidos'=>Extintor::where('proxima_recarga','<',today())->count(),'regulares'=>Extintor::where('proxima_recarga','>=',today())->count()];
        $setores = Setor::orderBy('nome')->get();
        return view('emergencia.extintores',compact('extintores','stats','setores'));
    }
    public function store(Request $r) {
        $r->validate(['tipo'=>'required']);
        $data = $r->only(['numero_serie','tipo','capacidade','localizacao','setor_id','ultima_recarga','proxima_recarga','ultimo_teste_hidrostatico','proximo_teste_hidrostatico','status']);
        $data['empresa_id'] = auth()->user()->empresa_id ?? $r->empresa_id;
        $data['status'] = $data['proxima_recarga'] && $data['proxima_recarga'] < today()->format('Y-m-d') ? 'vencido' : ($data['status']??'regular');
        Extintor::create($data);
        return back()->with('success','Extintor cadastrado!');
    }
    public function update(Request $r, Extintor $extintor) {
        $extintor->update($r->only(['numero_serie','tipo','capacidade','localizacao','setor_id','ultima_recarga','proxima_recarga','ultimo_teste_hidrostatico','proximo_teste_hidrostatico','status']));
        return back()->with('success','Extintor atualizado!');
    }
    public function destroy(Extintor $extintor) { $extintor->delete(); return back()->with('success','Extintor excluído!'); }
    public function show(Extintor $e) { return back(); }
    public function create() { return redirect()->route('extintores.index'); }
    public function edit(Extintor $e) { return redirect()->route('extintores.index'); }
    public function inspecao(Request $r, Extintor $extintor) {
        $r->validate(['data_inspecao'=>'required|date','resultado'=>'required|in:conforme,nao_conforme']);
        InspecaoExtintor::create(['extintor_id'=>$extintor->id,'empresa_id'=>$extintor->empresa_id,'data_inspecao'=>$r->data_inspecao,'responsavel'=>$r->responsavel??auth()->user()->name,'resultado'=>$r->resultado,'observacoes'=>$r->observacoes]);
        if ($r->resultado==='nao_conforme') $extintor->update(['status'=>'manutencao']);
        return back()->with('success','Inspeção registrada!');
    }
}
