@extends('layouts.admin')

@section('title', 'Dossier — ' . $dossier->nomMembre() . ' | PHOVE')

@section('page_title', 'Dossier personnel')
@section('page_icon', 'folder-open')
@section('page_subtitle', 'Fiche détaillée du dossier : ' . $dossier->nomMembre())

@php
    $breadcrumb = [
        ['label' => 'Dossiers', 'url' => route('admin.dossiers.index')],
        $dossier->nomMembre()
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE DU DOSSIER --}}
    {{-- ============================================ --}}
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; background: #FFFFFF; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; font-size: 1.3rem; font-weight: 700; flex-shrink: 0;">
            {{ $dossier->membre ? $dossier->membre->initiales() : '?' }}
        </div>
        <div style="flex: 1;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #1E293B; margin: 0 0 4px 0;">
                {{ $dossier->nomMembre() }}
            </h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                @if($dossier->membre)
                <span class="projet-badge" style="background: #E8F0F8; color: #1A5B9C;">
                    <i class="fas fa-user-tag"></i> {{ $dossier->membre->categorieLibelle() }}
                </span>
                @endif
                <span class="projet-publication-badge {{ $dossier->statut === 'complet' ? 'projet-publie' : '' }}" 
                      style="{{ $dossier->statut === 'incomplet' ? 'background: #FEF3C7; color: #92400E;' : ($dossier->statut === 'en_attente' ? 'background: #DBEAFE; color: #1E40AF;' : '') }}">
                    <i class="fas fa-{{ $dossier->statut === 'complet' ? 'check-circle' : ($dossier->statut === 'incomplet' ? 'exclamation-triangle' : 'clock') }}"></i>
                    {{ $dossier->statutLibelle() }}
                </span>
                <span style="font-size: 12px; color: #64748B;">
                    <i class="fas fa-calendar-alt"></i> Créé le {{ $dossier->date_creation->format('d/m/Y') }}
                </span>
                @if($dossier->date_validation)
                <span style="font-size: 12px; color: #10B981;">
                    <i class="fas fa-check-circle"></i> Validé le {{ $dossier->date_validation->format('d/m/Y') }}
                </span>
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <a href="{{ route('admin.dossiers.edit', $dossier) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-edit"></i> Modifier le dossier
            </a>
            <a href="{{ route('admin.dossiers.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PROGRESSION GLOBALE --}}
    {{-- ============================================ --}}
    <div style="background: #FFFFFF; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <h3 style="font-size: 14px; font-weight: 700; color: #1E293B; margin: 0;">
                <i class="fas fa-chart-line"></i> Progression du dossier
            </h3>
            <span style="font-size: 14px; font-weight: 700; color: {{ $dossier->pourcentageCompletion() >= 100 ? '#10B981' : ($dossier->pourcentageCompletion() >= 50 ? '#F59E0B' : '#EF4444') }};">
                {{ $dossier->pourcentageCompletion() }}%
            </span>
        </div>
        <div style="height: 10px; background: #E2E8F0; border-radius: 5px; overflow: hidden;">
            <div style="height: 100%; width: {{ $dossier->pourcentageCompletion() }}%; background: {{ $dossier->pourcentageCompletion() >= 100 ? '#10B981' : ($dossier->pourcentageCompletion() >= 50 ? '#F59E0B' : '#EF4444') }}; border-radius: 5px; transition: width 0.3s ease;"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 12px; color: #64748B;">
            <span><i class="fas fa-check-circle" style="color: #10B981;"></i> {{ $dossier->nombreDocumentsFournis() }} pièce(s) fournie(s)</span>
            <span><i class="fas fa-times-circle" style="color: #EF4444;"></i> {{ $dossier->nombreDocumentsManquants() }} pièce(s) manquante(s)</span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LES 12 PIÈCES — GESTION COMPLÈTE --}}
    {{-- ============================================ --}}
    <div style="background: #FFFFFF; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <h3 style="font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 16px;">
            <i class="fas fa-list-check"></i> Les 12 pièces exigées (Article 65)
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            @foreach($typesDocuments as $type => $label)
                @php
                    $document = $documentsParType->get($type);
                    $estFourni = $document && $document->estFourni();
                @endphp
                <div style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 10px; border: 1px solid {{ $estFourni ? '#D1FAE5' : '#FEE2E2' }}; background: {{ $estFourni ? '#F0FDF4' : '#FEF2F2' }};">
                    {{-- Icône statut --}}
                    <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: {{ $estFourni ? '#D1FAE5' : '#FEE2E2' }}; color: {{ $estFourni ? '#10B981' : '#EF4444' }};">
                        <i class="fas fa-{{ $estFourni ? 'check-circle' : 'times-circle' }}"></i>
                    </div>

                    {{-- Infos --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 13px; font-weight: 600; color: #1E293B;">{{ $label }}</div>
                        @if($estFourni)
                            <div style="font-size: 11px; color: #10B981;">
                                <i class="fas fa-check"></i> Fourni
                                @if($document->date_upload)
                                    — {{ $document->date_upload->format('d/m/Y') }}
                                @endif
                                @if($document->extension())
                                    — {{ $document->extension() }}
                                @endif
                            </div>
                        @else
                            <div style="font-size: 11px; color: #EF4444;">
                                <i class="fas fa-times"></i> Manquant
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div style="display: flex; gap: 6px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end;">
                        @if($estFourni)
                            {{-- VOIR LE DOCUMENT — VIA ROUTE LARAVEL (fonctionne avec php artisan serve) --}}
                            <a href="{{ route('admin.dossiers.documents.voir', $document) }}" target="_blank" 
                               class="projet-action-btn view" title="Voir le document" style="background: #DBEAFE; color: #1E40AF;">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- REMPLACER LE DOCUMENT --}}
                            <form action="{{ route('admin.dossiers.documents.upload', [$dossier, $document]) }}" method="POST" enctype="multipart/form-data" style="display: inline;">
                                @csrf
                                <input type="file" name="fichier" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" 
                                       style="display: none;" id="replaceFile{{ $document->id }}"
                                       onchange="this.form.submit()">
                                <button type="button" class="projet-action-btn edit" title="Remplacer" style="background: #FEF3C7; color: #92400E;"
                                        onclick="document.getElementById('replaceFile{{ $document->id }}').click();">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>

                            {{-- SUPPRIMER LE DOCUMENT --}}
                            <form action="{{ route('admin.dossiers.documents.destroy', $document) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="projet-action-btn delete" 
                                        onclick="return confirm('Supprimer ce document ? La pièce sera marquée comme manquante.');" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @else
                            {{-- UPLOADER LE DOCUMENT MANQUANT — AVEC BOUTON ENREGISTRER VISIBLE --}}
                            <form action="{{ route('admin.dossiers.documents.upload', [$dossier, $document]) }}" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 8px;">
                                @csrf
                                <input type="file" name="fichier" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" 
                                       style="font-size: 11px; max-width: 140px;" required
                                       title="Sélectionner un fichier">
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 8px 12px; font-size: 11px; background: #10B981; border-color: #10B981; white-space: nowrap;">
                                    <i class="fas fa-upload"></i> Enregistrer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- NOTES INTERNES --}}
    {{-- ============================================ --}}
    @if($dossier->notes)
    <div style="background: #FFFFFF; border-radius: 16px; padding: 20px 24px; margin-top: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <h3 style="font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">
            <i class="fas fa-sticky-note"></i> Notes internes
        </h3>
        <p style="font-size: 13px; color: #334155; line-height: 1.8; white-space: pre-line;">{{ $dossier->notes }}</p>
    </div>
    @endif

    {{-- ============================================ --}}
    {{-- ACTIONS FINALES --}}
    {{-- ============================================ --}}
    <div style="display: flex; gap: 10px; margin-top: 24px;">
        <a href="{{ route('admin.dossiers.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <form action="{{ route('admin.dossiers.destroy', $dossier) }}" method="POST" style="display: inline; margin-left: auto;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce dossier et tous ses documents ?');">
                <i class="fas fa-trash-alt"></i> Supprimer le dossier
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection