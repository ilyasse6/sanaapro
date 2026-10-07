<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Devis;
use App\Models\Facture;
use App\Support\SocieteBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DevisFacturesApiController extends BaseApiController
{
    public function devisIndex(Request $request)
    {
        $q = Devis::with(['client', 'lignes'])->where('actif', 1)->orderByDesc('created_at');
        if ($request->filled('statut')) {
            $q->where('statut', $request->statut);
        }
        return $this->paginated($q->paginate((int) $request->get('per_page', 20)), 'Liste devis.');
    }

    public function devisShow(int $id)
    {
        $devis = Devis::with(['client', 'lignes'])->findOrFail($id);
        return $this->ok($devis, 'Detail devis.');
    }

    public function facturesIndex(Request $request)
    {
        $q = Facture::with(['client', 'devis', 'lignes'])->where('actif', 1)->orderByDesc('created_at');
        if ($request->filled('statut')) {
            $q->where('statut', $request->statut);
        }
        return $this->paginated($q->paginate((int) $request->get('per_page', 20)), 'Liste factures.');
    }

    public function facturesShow(int $id)
    {
        $facture = Facture::with(['client', 'devis', 'lignes'])->findOrFail($id);
        return $this->ok($facture, 'Detail facture.');
    }

    /**
     * PDF pour l’app mobile (Bearer Sanctum), même rendu que la version web.
     */
    public function devisPdf(int $id)
    {
        $user = request()->user();
        if (! $user || ! $user->hasPermission('Gestion des Devis', 'consulter')) {
            return response()->json([
                'success' => false,
                'message' => 'Permission insuffisante.',
                'errors' => [],
            ], 403);
        }

        $devi = Devis::with(['client', 'lignes.catalogueService', 'facture'])->findOrFail($id);
        $societe = $devi->branding_affiche;

        [$logo_src_for_pdf, $logo_img_pdf_dims] = SocieteBranding::logoPourPdfAvecDefaut($societe, 140, 80);

        $pdf = Pdf::loadView('user.devis.pdf', compact('devi', 'societe', 'logo_src_for_pdf', 'logo_img_pdf_dims'))
            ->setPaper('a4');

        $name = 'devis-'.preg_replace('/[^A-Za-z0-9._-]+/', '_', (string) $devi->numero_devis).'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ]);
    }

    public function facturePdf(int $id)
    {
        $user = request()->user();
        if (! $user || ! $user->hasPermission('Gestion des Factures', 'consulter')) {
            return response()->json([
                'success' => false,
                'message' => 'Permission insuffisante.',
                'errors' => [],
            ], 403);
        }

        $facture = Facture::with(['client', 'devis', 'lignes'])->findOrFail($id);
        $societe = $facture->branding_affiche;
        $remiseGlobaleHt = $facture->remise_globale_pct > 0
            ? round($facture->montant_lignes_ht * ($facture->remise_globale_pct / 100), 2)
            : 0;

        $logo_src_for_pdf = SocieteBranding::logoSrcForDomPdf($societe);
        $logo_img_pdf_dims = SocieteBranding::logoDisplayDimensionsForPdf($societe);

        $pdf = Pdf::loadView('user.facture.pdf', compact('facture', 'societe', 'remiseGlobaleHt', 'logo_src_for_pdf', 'logo_img_pdf_dims'));

        $name = 'facture-'.preg_replace('/[^A-Za-z0-9._-]+/', '_', (string) $facture->numero_facture).'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ]);
    }
}
