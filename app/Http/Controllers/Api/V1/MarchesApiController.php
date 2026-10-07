<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Marche;
use Illuminate\Http\Request;

class MarchesApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $q = Marche::with(['client', 'lots', 'cautions', 'documents'])
            ->where('actif', 1)
            ->orderByDesc('created_at');
        if ($request->filled('statut')) {
            $q->where('statut', $request->statut);
        }
        return $this->paginated($q->paginate((int) $request->get('per_page', 20)), 'Liste marches.');
    }

    public function show(int $id)
    {
        $marche = Marche::with(['client', 'lots', 'cautions', 'documents'])->findOrFail($id);
        return $this->ok($marche, 'Detail marche.');
    }
}
