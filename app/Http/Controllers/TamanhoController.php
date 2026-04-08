<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Tamanho;

class TamanhoController extends Controller {
    public function index() { $tamanhos=Tamanho::orderBy('ordem')->get(); return view('tamanhos.index',compact('tamanhos')); }
    public function store(Request $r) { $r->validate(['codigo'=>'required|unique:tamanhos,codigo']); Tamanho::create(['codigo'=>strtoupper(trim($r->codigo)),'descricao'=>$r->descricao,'ordem'=>$r->ordem??99]); return back()->with('success','Tamanho criado!'); }
    public function update(Request $r, Tamanho $tamanho) { $tamanho->update(['codigo'=>strtoupper(trim($r->codigo)),'descricao'=>$r->descricao,'ordem'=>$r->ordem??99]); return back()->with('success','Atualizado!'); }
    public function destroy(Tamanho $tamanho) { $tamanho->delete(); return back()->with('success','Excluído!'); }
    public function seed() {
        $seeds=[['codigo'=>'PP','descricao'=>'Extra Pequeno','ordem'=>1],['codigo'=>'P','descricao'=>'Pequeno','ordem'=>2],['codigo'=>'M','descricao'=>'Médio','ordem'=>3],['codigo'=>'G','descricao'=>'Grande','ordem'=>4],['codigo'=>'GG','descricao'=>'Extra Grande','ordem'=>5],['codigo'=>'XG','descricao'=>'Extra Extra Grande','ordem'=>6],['codigo'=>'36','descricao'=>'Nº 36','ordem'=>10],['codigo'=>'38','descricao'=>'Nº 38','ordem'=>11],['codigo'=>'40','descricao'=>'Nº 40','ordem'=>12],['codigo'=>'42','descricao'=>'Nº 42','ordem'=>13],['codigo'=>'44','descricao'=>'Nº 44','ordem'=>14]];
        $c=0; foreach($seeds as $s){if(!Tamanho::where('codigo',$s['codigo'])->exists()){Tamanho::create($s);$c++;}}
        return back()->with('success',"$c tamanho(s) criado(s)!");
    }
    public function create() { return redirect()->route('tamanhos.index'); }
    public function show(Tamanho $t) { return redirect()->route('tamanhos.index'); }
    public function edit(Tamanho $t) { return redirect()->route('tamanhos.index'); }
}
