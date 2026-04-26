@if(isset($featuredPaper) && $featuredPaper)
@php
    $paperUrl = route('papers.show', $featuredPaper->slug);
@endphp
<section style="background:#06111f;border-top:1px solid rgba(56,182,255,.28);border-bottom:1px solid rgba(56,182,255,.2);">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-2">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:42px;height:42px;background:rgba(56,182,255,.16);border:1px solid rgba(56,182,255,.34);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-file-alt" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="text-uppercase fw-bold" style="color:var(--primary-color);font-size:.68rem;letter-spacing:.08em;">Investigación</div>
                        <div class="fw-semibold text-white" style="font-size:.9rem;line-height:1.15;">Paper destacado</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <a href="{{ $paperUrl }}" class="text-decoration-none">
                    <h2 class="fw-bold mb-2" style="color:#fff;font-size:1.12rem;line-height:1.28;">{{ $featuredPaper->title }}</h2>
                </a>
                @if($featuredPaper->excerpt)
                    <p class="mb-0" style="color:#cbd5e1;font-size:.86rem;line-height:1.55;max-width:860px;">{{ Str::limit($featuredPaper->excerpt, 190) }}</p>
                @endif
            </div>

            <div class="col-lg-3">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    @if($featuredPaper->arxiv_category)
                        <span class="badge rounded-pill" style="background:rgba(255,255,255,.08);color:#dbeafe;border:1px solid rgba(255,255,255,.14);font-size:.7rem;">{{ $featuredPaper->arxiv_category }}</span>
                    @endif
                    @if($featuredPaper->arxiv_published_date)
                        <span style="color:#94a3b8;font-size:.74rem;">
                            <i class="far fa-calendar-alt me-1"></i>{{ $featuredPaper->arxiv_published_date->locale('es')->isoFormat('D MMM YYYY') }}
                        </span>
                    @endif
                    <a href="{{ $paperUrl }}"
                       class="btn btn-sm rounded-pill fw-semibold px-3"
                       style="background:var(--primary-color);color:#fff;font-size:.78rem;white-space:nowrap;">
                        Leer investigación <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
