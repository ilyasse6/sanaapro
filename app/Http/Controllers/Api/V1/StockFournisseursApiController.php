<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Article;
use App\Models\Fournisseur;
use App\Models\StockMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockFournisseursApiController extends BaseApiController
{
    public function fournisseursIndex(Request $request)
    {
        $q = Fournisseur::query()->where('actif', 1)->orderByDesc('id');
        return $this->paginated($q->paginate((int) $request->get('per_page', 20)), 'Liste fournisseurs.');
    }

    public function fournisseursShow(int $id)
    {
        return $this->ok(Fournisseur::with('articles')->findOrFail($id), 'Detail fournisseur.');
    }

    public function articlesIndex(Request $request)
    {
        $q = Article::with(['fournisseur'])->where('actif', 1)->orderByDesc('id');
        if ($request->filled('low_stock') && (int) $request->low_stock === 1) {
            $q->whereColumn('stock_quantity', '<=', 'seuil_alerte');
        }
        return $this->paginated($q->paginate((int) $request->get('per_page', 20)), 'Liste articles.');
    }

    public function articleMouvements(int $articleId)
    {
        $article = Article::with('mouvements.user')->findOrFail($articleId);
        return $this->ok($article, 'Mouvements stock article.');
    }

    public function mouvementsStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'type' => 'required|in:entree,sortie,ajustement',
            'quantite' => 'required|numeric|min:0.001',
            'motif' => 'nullable|string|max:255',
            'reference_piece' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation error', $validator->errors()->toArray(), 422);
        }

        $mvt = StockMouvement::create([
            ...$validator->validated(),
            'user_id' => $request->user()->id,
        ]);
        $article = Article::findOrFail($request->article_id);
        $qte = (float) $article->stock_quantity;
        $delta = (float) $request->quantite;
        if ($request->type === 'entree') {
            $qte += $delta;
        } elseif ($request->type === 'sortie') {
            $qte -= $delta;
        } else {
            $qte = $delta;
        }
        $article->update(['stock_quantity' => max(0, $qte)]);

        return $this->ok($mvt->fresh(), 'Mouvement enregistre.', 201);
    }
}
