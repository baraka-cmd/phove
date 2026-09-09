<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Organe;
use App\Models\StructureTerritoriale;

class GouvernanceController extends Controller
{
    /**
     * Affiche la page publique de la gouvernance PHOVE.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Membres actifs du Conseil d'Administration
        $membresCA = Organe::where('organe', 'conseil_administration')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // Membres actifs de la Commission de Contrôle
        $membresCC = Organe::where('organe', 'commission_controle')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // Membres actifs de la Direction Générale
        $membresDG = Organe::where('organe', 'direction_executive')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // Structures territoriales actives
        $structures = StructureTerritoriale::where('statut', 'actif')
            ->orderBy('type')
            ->orderBy('nom')
            ->get();

        return view('public.gouvernance', compact(
            'membresCA',
            'membresCC',
            'membresDG',
            'structures'
        ));
    }
}