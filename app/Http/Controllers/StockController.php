<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CatalogueService;
use App\Models\Fournisseur;
use App\Models\StockMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    private function p(string $a): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Gestion du Stock', $a)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function indexArticles()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }
        $articles = Article::with(['fournisseur', 'catalogueService'])->where('actif', 1)->orderBy('nom')->paginate(20);

        return view('user.stock.articles', compact('articles'));
    }

    public function createArticle()
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->get();
        $services = CatalogueService::where('actif', 1)->orderBy('nom')->get();

        return view('user.stock.article-create', compact('fournisseurs', 'services'));
    }

    public function storeArticle(Request $request)
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'reference' => 'required|string|max:80|unique:articles,reference',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unite' => 'nullable|string|max:30',
            'prix_achat_ht' => 'nullable|numeric|min:0',
            'prix_vente_ht' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'seuil_alerte' => 'nullable|numeric|min:0',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'catalogue_service_id' => 'nullable|exists:catalogue_services,id',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }
        Article::create([...$v->validated(), 'actif' => 1, 'user_insert' => Auth::id()]);

        return redirect()->route('stock.articles')->with('success', 'Article créé.');
    }

    public function editArticle(Article $article)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->get();
        $services = CatalogueService::where('actif', 1)->orderBy('nom')->get();

        return view('user.stock.article-edit', compact('article', 'fournisseurs', 'services'));
    }

    public function updateArticle(Request $request, Article $article)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'reference' => 'required|string|max:80|unique:articles,reference,' . $article->id,
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unite' => 'nullable|string|max:30',
            'prix_achat_ht' => 'nullable|numeric|min:0',
            'prix_vente_ht' => 'nullable|numeric|min:0',
            'seuil_alerte' => 'nullable|numeric|min:0',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'catalogue_service_id' => 'nullable|exists:catalogue_services,id',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }
        $article->update([...$v->validated(), 'user_update' => Auth::id()]);

        return redirect()->route('stock.articles')->with('success', 'Article mis à jour.');
    }

    public function indexMouvements()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }
        $mouvements = StockMouvement::with(['article', 'user'])->orderByDesc('created_at')->paginate(25);
        $articles = Article::where('actif', 1)->orderBy('nom')->get();

        return view('user.stock.mouvements', compact('mouvements', 'articles'));
    }

    public function storeMouvement(Request $request)
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'type' => 'required|in:entree,sortie,ajustement',
            'quantite' => 'required|numeric|min:0.001',
            'motif' => 'nullable|string|max:255',
            'reference_piece' => 'nullable|string|max:120',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $article = Article::lockForUpdate()->findOrFail($request->article_id);
                $q = (float) $request->quantite;
                if ($request->type === 'entree') {
                    $article->stock_quantity = (float) $article->stock_quantity + $q;
                } elseif ($request->type === 'sortie') {
                    $article->stock_quantity = max(0, (float) $article->stock_quantity - $q);
                } else {
                    $article->stock_quantity = $q;
                }
                $article->save();

                StockMouvement::create([
                    'article_id' => $article->id,
                    'type' => $request->type,
                    'quantite' => $q,
                    'motif' => $request->motif,
                    'reference_piece' => $request->reference_piece,
                    'user_id' => Auth::id(),
                ]);
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Mouvement impossible.')->withInput();
        }

        return redirect()->route('stock.mouvements')->with('success', 'Mouvement enregistré.');
    }

    public function desactivateArticle(Article $article)
    {
        if ($r = $this->p('desactiver')) {
            return $r;
        }
        $article->update(['actif' => 0, 'user_update' => Auth::id()]);

        return redirect()->route('stock.articles')->with('success', 'Article désactivé.');
    }
}
