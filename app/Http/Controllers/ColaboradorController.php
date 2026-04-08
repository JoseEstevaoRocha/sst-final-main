<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Colaborador, Empresa, Setor, Funcao};

class ColaboradorController extends Controller {
    public function index(Request $r) {
        $q = Colaborador::with(['empresa','setor','funcao']);
        if ($r->search)   $q->where(fn($sq)=>$sq->where('nome','ilike',"%{$r->search}%")->orWhere('cpf','ilike',"%{$r->search}%")->orWhere('matricula','ilike',"%{$r->search}%"));
        if ($r->setor_id) $q->where('setor_id',$r->setor_id);
        if ($r->status)   $q->where('status',$r->status);
        $cols = $q->orderBy('nome')->paginate(20)->withQueryString();
        $setores = Setor::orderBy('nome')->get();
        return view('colaboradores.index', compact('cols','setores'));
    }
    public function create() {
        $user = auth()->user();
        $empresas = $user->isSuperAdmin() ? Empresa::ativas()->get() : collect([$user->empresa]);
        $setores  = Setor::orderBy('nome')->get();
        return view('colaboradores.form',['colaborador'=>null,'empresas'=>$empresas,'setores'=>$setores,'funcoes'=>collect()]);
    }
    public function store(Request $r) {
        $r->validate(['nome'=>'required|min:3','cpf'=>'required|size:11|unique:colaboradores,cpf','empresa_id'=>'required|exists:empresas,id','setor_id'=>'required|exists:setores,id','funcao_id'=>'required|exists:funcoes,id','data_nascimento'=>'required|date','sexo'=>'required|in:M,F','data_admissao'=>'required|date','status'=>'required']);
        $r->merge(['cpf'=>preg_replace('/\D/','',$r->cpf),'pis'=>preg_replace('/\D/','',$r->pis??'')]);
        Colaborador::create($r->only(['empresa_id','setor_id','funcao_id','nome','cpf','rg','pis','matricula','matricula_esocial','cbo','data_nascimento','sexo','data_admissao','data_demissao','status','jovem_aprendiz','escolaridade','telefone','email','observacoes']));
        return redirect()->route('colaboradores.index')->with('success','Colaborador cadastrado!');
    }
    public function show(Colaborador $colaborador) { return redirect()->route('ficha.show',$colaborador); }
    public function edit(Colaborador $colaborador) {
        $user = auth()->user();
        $empresas = $user->isSuperAdmin() ? Empresa::ativas()->get() : collect([$user->empresa]);
        $setores  = Setor::where('empresa_id',$colaborador->empresa_id)->get();
        $funcoes  = Funcao::where('setor_id',$colaborador->setor_id)->get();
        return view('colaboradores.form', compact('colaborador','empresas','setores','funcoes'));
    }
    public function update(Request $r, Colaborador $colaborador) {
        $r->validate(['nome'=>'required|min:3',"cpf"=>"required|size:11|unique:colaboradores,cpf,{$colaborador->id}",'empresa_id'=>'required','setor_id'=>'required','funcao_id'=>'required','data_nascimento'=>'required|date','sexo'=>'required|in:M,F','data_admissao'=>'required|date','status'=>'required']);
        $r->merge(['cpf'=>preg_replace('/\D/','',$r->cpf)]);
        $colaborador->update($r->only(['empresa_id','setor_id','funcao_id','nome','cpf','rg','pis','matricula','matricula_esocial','cbo','data_nascimento','sexo','data_admissao','data_demissao','status','jovem_aprendiz','escolaridade','telefone','email','observacoes']));
        return redirect()->route('colaboradores.index')->with('success','Colaborador atualizado!');
    }
    public function destroy(Colaborador $colaborador) { $colaborador->delete(); return redirect()->route('colaboradores.index')->with('success','Colaborador excluído!'); }
    public function bulkDestroy(Request $r) {
        $ids = $r->validate(['ids'=>'required|array'])['ids'];
        $c = Colaborador::whereIn('id',$ids)->delete();
        return redirect()->route('colaboradores.index')->with('success',"{$c} colaborador(es) excluído(s)!");
    }
    public function historico(Colaborador $colaborador) {
        $asos = $colaborador->asos()->orderByDesc('data_exame')->get();
        $epis = $colaborador->entregasEpi()->with('epi')->orderByDesc('data_entrega')->get();
        $unis = $colaborador->entregasUniforme()->with(['uniforme','tamanho'])->orderByDesc('data_entrega')->get();
        return view('colaboradores.historico',compact('colaborador','asos','epis','unis'));
    }
}
