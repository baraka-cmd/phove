<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Organe;
use App\Models\StructureTerritoriale;
use Illuminate\View\View;

class GouvernanceController extends Controller
{
    /**
     * Affiche la page publique de la gouvernance PHOVE.
     *
     * Récupère :
     *   - Les membres fondateurs (2012)
     *   - Le Conseil d'Administration
     *   - La Commission de Contrôle
     *   - La Direction Générale
     *   - Les structures territoriales
     *
     * @return View
     */
    public function index(): View
    {
        // ─────────────────────────────────────────────
        // 1. Les membres fondateurs (le 30 août 2012)
        // ─────────────────────────────────────────────
        $fondateurs = Organe::where('est_fondateur', true)
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->orderBy('date_debut')
            ->get();

        // ─────────────────────────────────────────────
        // 2. Membres actifs du Conseil d'Administration
        // ─────────────────────────────────────────────
        $membresCA = Organe::where('organe', 'conseil_administration')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // ─────────────────────────────────────────────
        // 3. Membres actifs de la Commission de Contrôle
        // ─────────────────────────────────────────────
        $membresCC = Organe::where('organe', 'commission_controle')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // ─────────────────────────────────────────────
        // 4. Membres actifs de la Direction Générale
        // ─────────────────────────────────────────────
        $membresDG = Organe::where('organe', 'direction_executive')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // ─────────────────────────────────────────────
        // 5. Structures territoriales actives
        // ─────────────────────────────────────────────
        $structures = StructureTerritoriale::where('statut', 'actif')
            ->orderBy('type')
            ->orderBy('nom')
            ->get();

        // ─────────────────────────────────────────────
        // 6. Statistiques (pour KPI éventuels)
        // ─────────────────────────────────────────────
        $stats = [
            'fondateurs'  => $fondateurs->count(),
            'ca'          => $membresCA->count(),
            'cc'          => $membresCC->count(),
            'dg'          => $membresDG->count(),
            'structures'  => $structures->count(),
        ];

        return view('public.gouvernance', compact(
            'fondateurs',
            'membresCA',
            'membresCC',
            'membresDG',
            'structures',
            'stats'
        ));
    }
}