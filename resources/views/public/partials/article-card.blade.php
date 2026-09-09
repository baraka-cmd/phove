{{-- ============================================ --}}
{{-- COMPOSANT CARTE ARTICLE RÉUTILISABLE — PHOVE --}}
{{-- Utilisé dans : liste actualités, page d'accueil --}}
{{-- ============================================ --}}

<article class="article-card {{ $class ?? '' }}" data-aos="fade-up">
    
    {{-- Image de l'article --}}
    <div class="article-card-image">
        @if($article->image)
            <img src="{{ asset('storage/' . $article->image) }}" 
                 alt="{{ $article->titre }}" 
                 loading="lazy"
                 class="article-card-img">
        @else
            <div class="article-card-placeholder">
                <i class="fas fa-newspaper"></i>
            </div>
        @endif
        
        {{-- Badge catégorie --}}
        <span class="article-card-badge article-badge-{{ $article->categorie }}">
            <i class="fas fa-{{ 
                $article->categorie === 'actualite' ? 'bolt' : 
                ($article->categorie === 'projet' ? 'project-diagram' : 
                ($article->categorie === 'evenement' ? 'calendar-star' : 
                ($article->categorie === 'rapport' ? 'file-alt' : 
                ($article->categorie === 'temoignage' ? 'quote-right' : 'bullhorn'))))
            }}"></i>
            {{ $article->categorieLibelle() }}
        </span>

        {{-- Badge épinglé --}}
        @if($article->epingle)
        <span class="article-card-pin" title="Article épinglé">
            <i class="fas fa-thumbtack"></i>
        </span>
        @endif
    </div>

    {{-- Corps de la carte --}}
    <div class="article-card-body">
        
        {{-- Métadonnées --}}
        <div class="article-card-meta">
            <span class="article-meta-item" title="Date de publication">
                <i class="fas fa-calendar-alt"></i>
                {{ $article->date_publication ? $article->date_publication->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
            </span>
            <span class="article-meta-item" title="Nombre de vues">
                <i class="fas fa-eye"></i>
                {{ $article->vues }}
            </span>
            @if($article->auteur)
            <span class="article-meta-item" title="Auteur">
                <i class="fas fa-user-edit"></i>
                {{ $article->auteur->nom }}
            </span>
            @endif
        </div>

        {{-- Titre --}}
        <h3 class="article-card-title">
            <a href="{{ route('actualites.show', $article->slug) }}" title="Lire l'article : {{ $article->titre }}">
                {{ $article->titre }}
            </a>
        </h3>

        {{-- Extrait --}}
        <p class="article-card-excerpt">
            {{ $article->extraitOuDefaut(150) }}
        </p>

        {{-- Footer --}}
        <div class="article-card-footer">
            <a href="{{ route('actualites.show', $article->slug) }}" class="article-card-link">
                <span>Lire la suite</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            
            {{-- Temps de lecture estimé --}}
            <span class="article-reading-time" title="Temps de lecture estimé">
                <i class="fas fa-clock"></i>
                {{ max(1, ceil(str_word_count(strip_tags($article->contenu)) / 200)) }} min
            </span>
        </div>
    </div>
</article>