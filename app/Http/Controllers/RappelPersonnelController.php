<?php

namespace App\Http\Controllers;

use App\Models\RappelPersonnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RappelPersonnelController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'date_rappel' => 'required|date',
            'heure_rappel' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        RappelPersonnel::create([
            'user_id' => $user->id,
            'titre' => $request->titre,
            'notes' => $request->notes,
            'date_rappel' => $request->date_rappel,
            'heure_rappel' => $request->heure_rappel ?: null,
        ]);

        return redirect()->back()->with('success', 'Rappel enregistré.');
    }
}
