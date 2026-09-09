<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Organe;
use App\Models\StructureTerritoriale;

class OrganigrammeController extends Controller
{
    /**
     * Affiche la page publique de l'organigramme PHOVE.
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

        // Directeur Général (Direction Exécutive)
        $directeurGeneral = Organe::where('organe', 'direction_executive')
            ->where('poste', 'like', '%Directeur Général%')
            ->where('actif', true)
            ->with('membre')
            ->first();

        // Chefs de Missions
        $chefsMissions = Organe::where('organe', 'chef_mission')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // Chargés des Programmes
        $chargesProgrammes = Organe::where('organe', 'charge_programme')
            ->where('actif', true)
            ->with('membre')
            ->orderBy('ordre')
            ->get();

        // Structures territoriales actives
        $structures = StructureTerritoriale::where('statut', 'actif')
            ->orderBy('type')
            ->orderBy('nom')
            ->get();

        return view('public.organigramme', compact(
            'membresCA',
            'membresCC',
            'directeurGeneral',
            'chefsMissions',
            'chargesProgrammes',
            'structures'
        ));
    }
}