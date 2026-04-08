<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Brigadista, Colaborador};

class BrigadaController extends Controller {
    public function index() {
        $brigadistas = Brigadista::with('colaborador')->where('ativo',true)->paginate(20);
        $colaboradores = Colaborador::ativos()->orderBy('nome')->get();
        return view('emergencia.brigada',compact('brigadistas','colaboradores'));
    }
    public function store(Request $r) {
        $r->validate(['colaborador_id'=>'required','funcao_brigada'=>'required']);
        Brigadista::updateOrCreate(['colaborador_id'=>$r->colaborador_id,'empresa_id'=>auth()->user()->empresa_id],['funcao_brigada'=>$r->funcao_brigada,'data_inicio'=>$r->data_inicio,'data_validade_cert'=>$r->data_validade_cert,'ativo'=>true]);
        return back()->with('success','Brigadista cadastrado!');
    }
    public function destroy(int $id) { Brigadista::find($id)?->update(['ativo'=>false]); return back()->with('success','Brigadista removido!'); }
}
