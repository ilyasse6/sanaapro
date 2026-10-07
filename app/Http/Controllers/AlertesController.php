<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Article;
use App\Models\Caution;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Support\AppHeaderBranding;
use App\Support\SocieteBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlertesController extends Controller
{
    private function p(string $a): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Alertes & Paramètres', $a)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    private function ensureParametresWritable(): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if ($u->isSuperAdmin()) {
            return null;
        }
        if ($u->isDirecteur()) {
            return null;
        }

        return redirect()->back()->with('error', 'Seul le directeur de l\'entreprise (ou un super administrateur) peut modifier ces paramètres.');
    }

    public function index()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }

        $joursCaution = (int) AppSetting::get('alerte_caution_jours', 30);
        $joursFacture = (int) AppSetting::get('alerte_facture_impayee_jours', 7);
        $stockActif = AppSetting::get('alerte_stock_actif', '1') === '1';

        $cautions = Caution::with('marche')
            ->where('statut', 'active')
            ->whereNotNull('date_fin')
            ->where('date_fin', '<=', now()->addDays($joursCaution))
            ->where('date_fin', '>=', now()->subDay())
            ->orderBy('date_fin')
            ->get();

        $factures = Facture::with('client')
            ->where('actif', 1)
            ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle'])
            ->whereNotNull('date_echeance')
            ->where('date_echeance', '<=', now()->addDays($joursFacture))
            ->orderBy('date_echeance')
            ->get();

        $articlesBas = collect();
        if ($stockActif) {
            $articlesBas = Article::with('fournisseur')
                ->where('actif', 1)
                ->whereColumn('stock_quantity', '<=', 'seuil_alerte')
                ->orderBy('nom')
                ->limit(50)
                ->get();
        }

        return view('user.alertes.index', compact('cautions', 'factures', 'articlesBas', 'joursCaution', 'joursFacture', 'stockActif'));
    }

    public function parametres(Request $request)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }

        $user = Auth::user();
        if ($user && $user->isSuperAdmin() && $request->filled('entreprise_parametres_id')) {
            session(['parametres_entreprise_id' => (int) $request->entreprise_parametres_id]);

            return redirect()->route('parametres');
        }

        $eid = AppSetting::resolvedEntrepriseId();
        $entreprisesListe = ($user && $user->isSuperAdmin())
            ? Entreprise::query()->orderBy('abbreviation')->get()
            : collect();

        return view('user.alertes.parametres', [
            'entreprisesListe' => $entreprisesListe,
            'entrepriseParametresId' => $eid,
            'joursCaution' => (int) AppSetting::get('alerte_caution_jours', 30),
            'joursFacture' => (int) AppSetting::get('alerte_facture_impayee_jours', 7),
            'stockActif' => AppSetting::get('alerte_stock_actif', '1'),
            'branding' => SocieteBranding::all($eid),
            'header_logo_url' => AppHeaderBranding::logoUrl($eid),
        ]);
    }

    public function saveParametres(Request $request)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        if ($r = $this->ensureParametresWritable()) {
            return $r;
        }
        $request->validate([
            'alerte_caution_jours' => 'required|integer|min:1|max:365',
            'alerte_facture_impayee_jours' => 'required|integer|min:1|max:365',
        ]);

        AppSetting::set('alerte_caution_jours', (string) $request->alerte_caution_jours);
        AppSetting::set('alerte_facture_impayee_jours', (string) $request->alerte_facture_impayee_jours);
        AppSetting::set('alerte_stock_actif', $request->has('alerte_stock_actif') ? '1' : '0');

        return redirect()->route('parametres')->with('success', 'Paramètres enregistrés.');
    }

    public function saveBranding(Request $request)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        if ($r = $this->ensureParametresWritable()) {
            return $r;
        }

        $request->validate([
            'societe_nom_commercial' => 'nullable|string|max:255',
            'societe_adresse' => 'nullable|string|max:2000',
            'societe_telephone' => 'nullable|string|max:80',
            'societe_email' => 'nullable|email|max:255',
            'devis_mention_tva' => 'nullable|string|max:500',
            'devis_entete_titre' => 'nullable|string|max:255',
            'devis_entete_activite' => 'nullable|string|max:500',
            'devis_pied_page' => 'nullable|string|max:1000',
            'document_accent_color' => ['nullable', 'string', 'max:7'],
            'document_text_color' => ['nullable', 'string', 'max:7'],
            'logo' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
            'header_logo' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
        ]);

        if ($request->boolean('remove_logo')) {
            $old = AppSetting::get('societe_logo');
            if (is_string($old) && $old !== '' && $old !== 'societe/sanaapro-logo.png') {
                Storage::disk('public')->delete($old);
            }
            AppSetting::set('societe_logo', '');
            AppSetting::set('app_header_logo', '');
        }

        if ($request->hasFile('logo')) {
            $old = AppSetting::get('societe_logo');
            if (is_string($old) && $old !== '' && $old !== 'societe/sanaapro-logo.png') {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('logo')->store('societe', 'public');
            AppSetting::set('societe_logo', $path);
            AppSetting::set('app_header_logo', $path);
        }

        AppSetting::set('societe_nom_commercial', (string) $request->input('societe_nom_commercial', ''));
        AppSetting::set('societe_adresse', (string) $request->input('societe_adresse', ''));
        AppSetting::set('societe_telephone', (string) $request->input('societe_telephone', ''));
        AppSetting::set('societe_email', (string) $request->input('societe_email', ''));
        $mention = trim((string) $request->input('devis_mention_tva', ''));
        AppSetting::set('devis_mention_tva', $mention !== '' ? $mention : SocieteBranding::DEFAULT_MENTION_TVA);
        foreach ([
            'devis_entete_titre' => SocieteBranding::DEFAULT_DEVIS_ENTETE_TITRE,
            'devis_entete_activite' => SocieteBranding::DEFAULT_DEVIS_ENTETE_ACTIVITE,
            'devis_pied_page' => SocieteBranding::DEFAULT_DEVIS_PIED_PAGE,
        ] as $cle => $defaut) {
            $valeur = trim((string) $request->input($cle, ''));
            AppSetting::set($cle, $valeur !== '' ? $valeur : $defaut);
        }

        $accent = trim((string) $request->input('document_accent_color', ''));
        if ($accent !== '' && preg_match('/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/', $accent)) {
            if (strlen($accent) === 4) {
                $accent = '#'.$accent[1].$accent[1].$accent[2].$accent[2].$accent[3].$accent[3];
            }
            AppSetting::set('document_accent_color', $accent);
        } else {
            AppSetting::set('document_accent_color', SocieteBranding::DEFAULT_ACCENT_COLOR);
        }

        $textC = trim((string) $request->input('document_text_color', ''));
        if ($textC !== '' && preg_match('/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/', $textC)) {
            if (strlen($textC) === 4) {
                $textC = '#'.$textC[1].$textC[1].$textC[2].$textC[2].$textC[3].$textC[3];
            }
            AppSetting::set('document_text_color', $textC);
        } else {
            AppSetting::set('document_text_color', SocieteBranding::DEFAULT_TEXT_COLOR);
        }

        return redirect()->route('parametres')->with('success', 'Logos et identité enregistrés.');
    }
}
