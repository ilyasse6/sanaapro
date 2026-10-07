{{-- Feuille complète pour DomPDF : aucun var(--) (non supporté → couleurs par défaut / incorrectes) --}}
@php
    $a = $societe['accent_color'] ?? \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR;
    $t = $societe['text_color'] ?? \App\Support\SocieteBranding::DEFAULT_TEXT_COLOR;
@endphp
<style>
    body { margin: 0; padding: 10px; background: #fff; }
    .devis-ae-pdf.devis-ae-doc {
        background: #fff;
        max-width: 210mm;
        margin: 0 auto;
        padding: 1.25rem 1.5rem 2rem;
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        color: {{ $t }};
        font-size: 11px;
        line-height: 1.45;
        border: 1px solid {{ $a }};
    }
    .devis-ae-pdf .text-muted { color: {{ $t }}; opacity: 0.68; }
    .devis-ae-pdf .devis-ae-top {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.25rem;
    }
    .devis-ae-pdf .devis-ae-co-left { flex: 1 1 260px; min-width: 200px; }
    .devis-ae-pdf .devis-ae-logo-row {
        margin-bottom: 0.75rem;
        padding-top: 3px;
    }
    .devis-ae-pdf .devis-ae-logo {
        display: block;
        margin: 0;
        vertical-align: top;
    }
    .devis-ae-pdf .devis-ae-logo-ph {
        min-height: 56px;
        min-width: 120px;
        border: 1px dashed #94a3b8;
        color: {{ $t }};
        opacity: 0.55;
        font-size: 10px;
        text-align: center;
        padding: 0.35rem;
    }
    .devis-ae-pdf .devis-ae-ph-hint { font-size: 9px; display: block; margin-top: 0.15rem; }
    .devis-ae-pdf .devis-ae-co-name {
        font-weight: 700;
        color: {{ $a }};
        font-size: 13px;
        margin-bottom: 0.35rem;
    }
    .devis-ae-pdf .devis-ae-co-lines { white-space: pre-line; }
    .devis-ae-pdf .devis-ae-boxes {
        flex: 1 1 240px;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        max-width: 320px;
    }
    .devis-ae-pdf .devis-ae-box { border: 1px solid {{ $a }}; margin-bottom: 0.5rem; }
    .devis-ae-pdf .devis-ae-box-h {
        background: {{ $a }};
        color: #ffffff;
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 0.35rem 0.5rem;
    }
    .devis-ae-pdf .devis-ae-box-b { padding: 0.5rem 0.6rem; background: #fff; }
    .devis-ae-pdf .devis-ae-box-b div { margin-bottom: 0.2rem; }
    .devis-ae-pdf .devis-ae-box-b strong { color: {{ $a }}; }
    .devis-ae-pdf .devis-ae-title {
        text-align: center;
        color: {{ $a }};
        font-size: 20px;
        font-weight: 700;
        margin: 1rem 0 0.35rem;
        letter-spacing: 0.02em;
    }
    .devis-ae-pdf .devis-ae-sub {
        text-align: center;
        font-style: italic;
        color: {{ $t }};
        opacity: 0.62;
        margin-bottom: 1rem;
        font-size: 11px;
    }
    .devis-ae-pdf .devis-ae-objet-titre {
        font-style: normal;
        color: {{ $t }};
        opacity: 1;
        font-weight: 600;
    }
    .devis-ae-pdf .devis-ae-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    .devis-ae-pdf .devis-ae-table th {
        background: {{ $a }};
        color: #ffffff;
        font-weight: 700;
        text-align: left;
        padding: 0.45rem 0.5rem;
        font-size: 10px;
        border: 1px solid {{ $a }};
    }
    .devis-ae-pdf .devis-ae-table th.num,
    .devis-ae-pdf .devis-ae-table td.num { text-align: right; }
    .devis-ae-pdf .devis-ae-table td {
        border: 1px solid {{ $a }};
        padding: 0.45rem 0.5rem;
        vertical-align: top;
        color: {{ $t }};
    }
    .devis-ae-pdf .devis-ae-table td.desc { font-size: 10.5px; }
    .devis-ae-pdf .devis-ae-totaux { margin-left: auto; max-width: 280px; margin-bottom: 0.75rem; }
    .devis-ae-pdf .devis-ae-totaux table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .devis-ae-pdf .devis-ae-totaux td {
        padding: 0.25rem 0.4rem;
        border: none;
        color: {{ $t }};
    }
    .devis-ae-pdf .devis-ae-totaux td:first-child { text-align: left; }
    .devis-ae-pdf .devis-ae-totaux td:last-child { text-align: right; }
    .devis-ae-pdf .devis-ae-totaux-tr-payer td {
        background: {{ $a }};
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        padding: 0.5rem 0.5rem;
    }
    .devis-ae-pdf .devis-ae-mention-tva {
        font-size: 9px;
        color: {{ $t }};
        opacity: 0.88;
        margin: 0 0 1rem;
        font-style: italic;
    }
    .devis-ae-pdf .devis-ae-comments {
        border: 1px solid {{ $a }};
        min-height: 4.5rem;
        padding: 0.5rem 0.6rem;
        margin-bottom: 1rem;
    }
    .devis-ae-pdf .devis-ae-comments h3 {
        font-size: 10px;
        color: {{ $a }};
        font-weight: 700;
        margin: 0 0 0.35rem;
        text-transform: uppercase;
    }
    .devis-ae-pdf .devis-ae-comments .small { color: {{ $t }}; }
    .devis-ae-pdf .devis-ae-sign {
        border: 1px solid {{ $a }};
        padding: 0.6rem;
        max-width: 340px;
        margin-left: auto;
        font-size: 10px;
        color: {{ $t }};
    }
    .devis-ae-pdf .devis-ae-sign p { margin: 0 0 0.5rem; font-style: italic; }
    .devis-ae-pdf .devis-ae-sign .row-sig {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 1.25rem;
    }
</style>
