@extends('layouts.admin')

@section('title', 'Bulletin — ' . $salaire->reference . ' | PHOVE')

@section('page_title', 'Bulletin de salaire')
@section('page_icon', 'file-invoice-dollar')
@section('page_subtitle', 'Référence : ' . $salaire->reference . ' — ' . $salaire->mois)

@php
    $breadcrumb = [
        ['label' => 'Salaires', 'url' => route('admin.salaires.index')],
        $salaire->reference
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; background: #FFFFFF; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <div style="width: 60px; height: 60px; border-radius: 14px; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; font-size: 1.6rem; flex-shrink: 0;">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div style="flex: 1;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #1E293B; margin: 0 0 4px 0;">
                {{ $salaire->nomMembre() }}
            </h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <span class="projet-badge" style="background: #E8F0F8; color: #1A5B9C;">
                    <i class="fas fa-barcode"></i> {{ $salaire->reference }}
                </span>
                <span class="projet-badge" style="background: #F1F5F9; color: #334155;">
                    <i class="fas fa-calendar-alt"></i> {{ $salaire->mois }}
                </span>
                @if($salaire->statut === 'brouillon')
                    <span class="projet-publication-badge" style="background: #F1F5F9; color: #64748B;">
                        <i class="fas fa-file"></i> Brouillon
                    </span>
                @elseif($salaire->statut === 'valide')
                    <span class="projet-publication-badge" style="background: #DBEAFE; color: #1E40AF;">
                        <i class="fas fa-check-circle"></i> Validé
                    </span>
                @elseif($salaire->statut === 'paye')
                    <span class="projet-publication-badge projet-publie">
                        <i class="fas fa-money-check"></i> Payé
                    </span>
                @else
                    <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                        <i class="fas fa-times-circle"></i> Annulé
                    </span>
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            @if($salaire->estBrouillon())
            <form action="{{ route('admin.salaires.valider', $salaire) }}" method="POST" style="display: inline;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-primary btn-sm" style="background: #3B82F6; border-color: #3B82F6;">
                    <i class="fas fa-check"></i> Valider
                </button>
            </form>
            @endif
            @if($salaire->estValide())
            <button type="button" class="btn btn-primary btn-sm" style="background: #10B981; border-color: #10B981;"
                    onclick="document.getElementById('payerFormShow').style.display='flex';">
                <i class="fas fa-money-check"></i> Marquer payé
            </button>
            @endif
            @if(!$salaire->estPaye())
            <a href="{{ route('admin.salaires.edit', $salaire) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endif
            <a href="{{ route('admin.salaires.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- Formulaire caché pour marquer payé --}}
    @if($salaire->estValide())
    <form action="{{ route('admin.salaires.payer', $salaire) }}" method="POST" id="payerFormShow" 
          style="display: none; align-items: center; gap: 8px; margin-bottom: 24px; background: #D1FAE5; border-radius: 12px; padding: 14px 18px;">
        @csrf
        @method('PUT')
        <label style="font-size: 13px; font-weight: 600; color: #065F46;">Date de paiement :</label>
        <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required 
               style="padding: 8px 12px; border: 1px solid #10B981; border-radius: 8px; font-size: 13px;">
        <button type="submit" class="btn btn-primary btn-sm" style="background: #10B981; border-color: #10B981;">
            <i class="fas fa-check"></i> Confirmer le paiement
        </button>
    </form>
    @endif

    {{-- ============================================ --}}
    {{-- BULLETIN DE SALAIRE --}}
    {{-- ============================================ --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

        {{-- COLONNE GAUCHE : DÉTAILS --}}
        <div>
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-info-circle"></i> Informations</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Membre</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 600; color: #1E293B;">{{ $salaire->nomMembre() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Poste</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $salaire->nomPoste() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Mois</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $salaire->mois }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Mode de paiement</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">
                            <i class="fas fa-{{ $salaire->mode_paiement === 'especes' ? 'money-bill' : ($salaire->mode_paiement === 'virement' ? 'university' : ($salaire->mode_paiement === 'mobile_money' ? 'mobile-alt' : 'check')) }}"></i>
                            {{ $salaire->modePaiementLibelle() }}
                        </span>
                    </div>
                    @if($salaire->date_paiement)
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Date de paiement</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 600; color: #10B981;">{{ $salaire->date_paiement->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($salaire->validePar)
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Validé par</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $salaire->validePar->nom ?? '—' }}</span>
                    </div>
                    @endif
                    @if($salaire->payePar)
                    <div style="display: flex; padding: 10px 0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Payé par</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $salaire->payePar->nom ?? '—' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($salaire->notes)
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-sticky-note"></i> Notes</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <p style="font-size: 13px; color: #334155; line-height: 1.8; white-space: pre-line;">{{ $salaire->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : MONTANTS --}}
        <div>
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-calculator"></i> Détail des montants</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="font-size: 13px; color: #334155;"><i class="fas fa-file-invoice" style="color: #64748B;"></i> Salaire de base</span>
                        <span style="font-size: 14px; font-weight: 600; color: #1E293B;">{{ number_format($salaire->salaire_base, 2, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="font-size: 13px; color: #334155;"><i class="fas fa-plus-circle" style="color: #10B981;"></i> Primes et indemnités</span>
                        <span style="font-size: 14px; font-weight: 600; color: #10B981;">+ {{ number_format($salaire->primes, 2, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="font-size: 13px; color: #334155;"><i class="fas fa-minus-circle" style="color: #EF4444;"></i> Retenues</span>
                        <span style="font-size: 14px; font-weight: 600; color: #EF4444;">- {{ number_format($salaire->retenues, 2, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; background: #F8FAFC; border-radius: 10px; margin-top: 8px;">
                        <span style="font-size: 14px; font-weight: 700; color: #1E293B;">
                            <i class="fas fa-equals" style="color: #1A5B9C;"></i> SALAIRE NET
                        </span>
                        <span style="font-size: 20px; font-weight: 800; color: #1A5B9C;">
                            {{ number_format($salaire->salaire_net, 2, ',', ' ') }} $
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-tasks"></i> Actions rapides</h2>
                </div>
                <div style="padding: 16px 24px; display: flex; flex-direction: column; gap: 10px;">
                    @if($salaire->estBrouillon())
                    <form action="{{ route('admin.salaires.valider', $salaire) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; background: #3B82F6; border-color: #3B82F6;">
                            <i class="fas fa-check"></i> Valider ce salaire
                        </button>
                    </form>
                    @endif
                    @if($salaire->estValide())
                    <form action="{{ route('admin.salaires.payer', $salaire) }}" method="POST" style="display: flex; gap: 8px;">
                        @csrf
                        @method('PUT')
                        <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required 
                               style="flex: 1; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 13px;">
                        <button type="submit" class="btn btn-primary btn-sm" style="background: #10B981; border-color: #10B981;">
                            <i class="fas fa-money-check"></i> Payer
                        </button>
                    </form>
                    @endif
                    @if($salaire->estBrouillon() || $salaire->estValide())
                    <form action="{{ route('admin.salaires.annuler', $salaire) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; color: #EF4444; border-color: #EF4444;"
                                onclick="return confirm('Annuler ce salaire ?');">
                            <i class="fas fa-times-circle"></i> Annuler ce salaire
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection