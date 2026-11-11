@if($paginator->hasPages())
    <div class="ds-pager">
        @if($paginator->onFirstPage())
            <span class="ds-btn ds-btn-secondary ds-btn-sm" aria-disabled="true">← Précédent</span>
        @else
            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ $paginator->previousPageUrl() }}">← Précédent</a>
        @endif

        <span class="ds-muted">Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if($paginator->hasMorePages())
            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ $paginator->nextPageUrl() }}">Suivant →</a>
        @else
            <span class="ds-btn ds-btn-secondary ds-btn-sm" aria-disabled="true">Suivant →</span>
        @endif
    </div>
@endif
