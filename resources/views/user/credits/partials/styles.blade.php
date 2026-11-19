<style>
    .cr-hero { background: linear-gradient(135deg, #0f172a 0%, #0f2438 50%, #198754 100%); border-radius: 20px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem; position: relative; overflow: hidden; }
    .cr-hero::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255,255,255,0.08); border-radius: 50%; }
    .cr-hero > * { position: relative; z-index: 1; }
    .cr-hero h1 { font-size: 1.6rem; font-weight: 700; }
    .cr-card { background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(15,23,42,0.08); border: none; }
    .cr-stat { background: #fff; border-radius: 14px; box-shadow: 0 6px 24px rgba(15,23,42,0.07); padding: 1rem 1.15rem; height: 100%; border-left: 4px solid #0f2438; }
    .cr-stat .lbl { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; font-weight: 600; }
    .cr-stat .val { font-size: 1.35rem; font-weight: 700; color: #0f172a; margin-top: 0.2rem; white-space: nowrap; }
    .cr-stat .sub { font-size: 0.8rem; color: #64748b; }
    .cr-stat.danger { border-left-color: #dc2626; } .cr-stat.danger .val { color: #b91c1c; }
    .cr-stat.warning { border-left-color: #f59e0b; }
    .cr-stat.success { border-left-color: #16a34a; } .cr-stat.success .val { color: #15803d; }
    .cr-stat.info { border-left-color: #0ea5e9; }
    .cr-table thead th { background: #0f2438; color: #fff; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.03em; border: none; white-space: nowrap; }
    .cr-table td { vertical-align: middle; white-space: nowrap; }
    .cr-montant { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .cr-solde-pos { color: #b91c1c; font-weight: 700; }
    .cr-solde-neg { color: #15803d; font-weight: 700; }
    .cr-filtres .btn { border-radius: 999px; }
    .cr-section-title { font-weight: 700; color: #0f172a; font-size: 1rem; }
    .cr-cheque-fields { background: #f1f5f9; border-radius: 12px; padding: 0.75rem; }
    @media print {
        .cr-no-print, .sidebar, .header, #header, footer, .alert { display: none !important; }
        #main { margin: 0 !important; padding: 0 !important; }
        .cr-hero { background: none !important; color: #000 !important; padding: 0 0 1rem !important; }
        .cr-card, .cr-stat { box-shadow: none !important; border: 1px solid #ddd !important; }
        .cr-table thead th { background: #eee !important; color: #000 !important; }
        .col-lg-8 { width: 100% !important; }
    }
</style>
