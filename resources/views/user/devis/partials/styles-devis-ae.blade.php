<style>
    {{-- Couleur : variable CSS sur chaque .devis-ae-doc (instantané devis/facture), pas les paramètres globaux --}}
    .devis-actions { margin-bottom: 1.5rem; }
    .devis-ae-doc {
        background: #fff;
        max-width: 210mm;
        margin: 0 auto;
        padding: 1.25rem 1.5rem 2rem;
        font-family: Arial, Helvetica, "Segoe UI", Roboto, sans-serif;
        color: var(--devis-ae-text, #111111);
        font-size: 11px;
        line-height: 1.45;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        border: var(--devis-ae-border);
    }
    .devis-ae-doc .text-muted { color: inherit !important; opacity: 0.68; }
    .devis-ae-top { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; }
    .devis-ae-co-left { flex: 1 1 260px; min-width: 200px; }
    .devis-ae-logo-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
    .devis-ae-logo { max-height: 64px; max-width: 200px; object-fit: contain; }
    .devis-ae-logo-ph {
        min-height: 56px; min-width: 120px; border: 1px dashed #94a3b8; display: flex; align-items: center; justify-content: center;
        color: var(--devis-ae-text, #111111); opacity: 0.55; font-size: 10px; text-align: center; padding: 0.35rem;
    }
    .devis-ae-ph-hint { font-size: 9px; display: block; margin-top: 0.15rem; }
    .devis-ae-co-name { font-weight: 700; color: var(--devis-ae-blue); font-size: 13px; margin-bottom: 0.35rem; }
    .devis-ae-co-lines { white-space: pre-line; }
    .devis-ae-boxes { flex: 1 1 240px; display: flex; flex-direction: column; gap: 0.5rem; max-width: 320px; }
    .devis-ae-box { border: var(--devis-ae-border); }
    .devis-ae-box-h {
        background: var(--devis-ae-blue); color: #fff; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.02em;
        padding: 0.35rem 0.5rem;
    }
    .devis-ae-box-b { padding: 0.5rem 0.6rem; background: #fff; }
    .devis-ae-box-b div { margin-bottom: 0.2rem; }
    .devis-ae-box-b strong { color: var(--devis-ae-blue); }
    .devis-ae-title {
        text-align: center; color: var(--devis-ae-blue); font-size: 20px; font-weight: 700; margin: 1rem 0 0.35rem;
        letter-spacing: 0.02em;
    }
    .devis-ae-sub { text-align: center; font-style: italic; color: var(--devis-ae-text, #111111); opacity: 0.62; margin-bottom: 1rem; font-size: 11px; }
    .devis-ae-objet-titre { font-style: normal !important; color: var(--devis-ae-text, #111111) !important; opacity: 1 !important; font-weight: 600 !important; }
    .devis-ae-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    .devis-ae-table th {
        background: var(--devis-ae-blue); color: #fff; font-weight: 700; text-align: left; padding: 0.45rem 0.5rem; font-size: 10px;
        border: var(--devis-ae-border);
    }
    .devis-ae-table th.num, .devis-ae-table td.num { text-align: right; }
    .devis-ae-table td {
        border: var(--devis-ae-border); padding: 0.45rem 0.5rem; vertical-align: top;
    }
    .devis-ae-table td.desc { font-size: 10.5px; }
    .devis-ae-totaux { margin-left: auto; max-width: 280px; margin-bottom: 0.75rem; }
    .devis-ae-totaux table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .devis-ae-totaux td { padding: 0.25rem 0.4rem; border: none; }
    .devis-ae-totaux td:first-child { text-align: left; }
    .devis-ae-totaux td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
    .devis-ae-totaux-tr-payer td {
        background: var(--devis-ae-blue); color: #fff; font-weight: 700; font-size: 13px; padding: 0.5rem 0.5rem;
    }
    .devis-ae-mention-tva { font-size: 9px; color: var(--devis-ae-text, #111111); opacity: 0.88; margin: 0 0 1rem; font-style: italic; }
    .devis-ae-comments {
        border: var(--devis-ae-border); min-height: 4.5rem; padding: 0.5rem 0.6rem; margin-bottom: 1rem;
    }
    .devis-ae-comments h3 { font-size: 10px; color: var(--devis-ae-blue); font-weight: 700; margin: 0 0 0.35rem; text-transform: uppercase; }
    .devis-ae-sign {
        border: var(--devis-ae-border); padding: 0.6rem; max-width: 340px; margin-left: auto; font-size: 10px;
    }
    .devis-ae-sign p { margin: 0 0 0.5rem; font-style: italic; }
    .devis-ae-sign .row-sig { display: flex; justify-content: space-between; gap: 1rem; margin-top: 1.25rem; }
    .devis-ae-statut-screen { margin-bottom: 1rem; }
    @@media print {
        .devis-actions, .facture-actions, .devis-ae-statut-screen, .facture-ae-statut-screen, .devis-lot-actions, .facture-lot-actions, .sidebar, .header, #header, nav, .btn, footer { display: none !important; }
        .devis-ae-doc { box-shadow: none; border: none; max-width: none; padding: 0; }
        body { background: #fff !important; }
        #main { margin: 0 !important; padding: 0 !important; }
    }
</style>
