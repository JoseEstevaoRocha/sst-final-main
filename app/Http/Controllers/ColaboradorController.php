<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Colaborador, Empresa, Setor, Funcao};
use Illuminate\Support\Facades\DB;

class ColaboradorController extends Controller {
    public function index(Request $r) {
        $user = auth()->user();
        $q = Colaborador::with(['empresa','setor','funcao']);

        if (!$user->isSuperAdmin()) $q->where('empresa_id', $user->empresa_id);
        if ($r->empresa_id)  $q->where('empresa_id',  $r->empresa_id);
        if ($r->setor_id)    $q->where('setor_id',    $r->setor_id);
        if ($r->funcao_id)   $q->where('funcao_id',   $r->funcao_id);
        if ($r->status)      $q->where('status',       $r->status);
        if ($r->sexo)        $q->where('sexo',         $r->sexo);
        if ($r->search)      $q->where(fn($sq) => $sq
            ->where('nome',       'ilike', "%{$r->search}%")
            ->orWhere('cpf',      'ilike', "%{$r->search}%")
            ->orWhere('matricula','ilike', "%{$r->search}%")
            ->orWhere('pis',      'ilike', "%{$r->search}%")
        );
        if ($r->admissao_de)  $q->whereDate('data_admissao', '>=', $r->admissao_de);
        if ($r->admissao_ate) $q->whereDate('data_admissao', '<=', $r->admissao_ate);
        if ($r->jovem_aprendiz) $q->where('jovem_aprendiz', true);

        $cols     = $q->orderBy('nome')->paginate(25)->withQueryString();
        $setores  = Setor::orderBy('nome')->get();
        $funcoes  = Funcao::orderBy('nome')->get();
        $empresas = $user->isSuperAdmin() ? Empresa::ativas()->orderBy('razao_social')->get() : collect();
        return view('colaboradores.index', compact('cols','setores','funcoes','empresas'));
    }
    public function create() {
        $user = auth()->user();
        $empresas = $user->isSuperAdmin() ? Empresa::ativas()->get() : collect([$user->empresa]);
        $setores  = Setor::orderBy('nome')->get();
        return view('colaboradores.form',['colaborador'=>new Colaborador(),'empresas'=>$empresas,'setores'=>$setores,'funcoes'=>collect()]);
    }
    public function store(Request $r) {
        $r->validate(['nome'=>'required|min:3','cpf'=>'required|size:11|unique:colaboradores,cpf','empresa_id'=>'required|exists:empresas,id','setor_id'=>'required|exists:setores,id','funcao_id'=>'required|exists:funcoes,id','data_nascimento'=>'required|date','sexo'=>'required|in:M,F','data_admissao'=>'required|date','status'=>'required']);
        $r->merge(['cpf'=>preg_replace('/\D/','',$r->cpf),'pis'=>preg_replace('/\D/','',$r->pis??'')]);
        $colaborador = Colaborador::create($r->only(['empresa_id','setor_id','funcao_id','nome','cpf','rg','pis','matricula','matricula_esocial','cbo','data_nascimento','sexo','data_admissao','data_demissao','status','jovem_aprendiz','escolaridade','telefone','email','observacoes']));
        if ($r->has('agendar')) {
            return redirect()->route('asos.create', ['colaborador_id' => $colaborador->id])
                ->with('info', "Colaborador {$colaborador->nome} cadastrado! Agende o ASO Admissional abaixo.");
        }
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
