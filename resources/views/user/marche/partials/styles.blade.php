{{-- Styles partagés Marchés (thème Prowise) --}}
<style>
    :root {
        --prowise-blue: #17324D;
        --prowise-blue-dark: #0f2438;
        --prowise-gold: #198754;
        --prowise-gold-dark: #146c43;
        --marche-gradient: linear-gradient(135deg, #17324D 0%, #0f2438 55%, #198754 140%);
        --marche-shadow: 0 10px 40px rgba(30, 64, 175, 0.08);
        --marche-shadow-hover: 0 16px 48px rgba(30, 64, 175, 0.12);
    }
    .marche-hero {
        background: var(--marche-gradient);
        border-radius: 20px;
        padding: 1.75rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .marche-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -8%;
        width: 260px;
        height: 260px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .marche-hero-content { position: relative; z-index: 2; }
    .marche-hero h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 0.35rem; }
    .marche-hero p { margin: 0; opacity: 0.92; font-size: 0.95rem; max-width: 560px; }
    .btn-marche {
        border-radius: 12px;
        padding: 0.55rem 1.15rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-marche-light {
        background: #fff;
        color: #0f2438;
        border: none;
    }
    .btn-marche-light:hover { background: #f8fafc; color: #17324D; transform: translateY(-1px); }
    .btn-marche-outline {
        background: transparent;
        color: #fff;
        border: 2px solid rgba(255,255,255,0.75);
    }
    .btn-marche-outline:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .btn-marche-primary {
        background: linear-gradient(135deg, #17324D, #0f2438);
        color: #fff;
        border: none;
    }
    .btn-marche-primary:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(30,64,175,0.3); }
    .card-marche {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: var(--marche-shadow);
        overflow: hidden;
    }
    .card-marche-body { padding: 1.5rem; }
    .card-marche-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--prowise-blue-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .card-marche-title i { color: var(--prowise-gold-dark); }
    .table-marche { margin: 0; }
    .table-marche thead {
        background: linear-gradient(135deg, #17324D 0%, #0f2438 100%);
        color: #fff;
    }
    .table-marche thead th {
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.9rem 1rem;
        white-space: nowrap;
    }
    .table-marche tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-color: #eef1f7;
    }
    .table-marche tbody tr:hover { background: #f8faff; }
    .marche-input, .marche-select, .marche-textarea {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.55rem 0.85rem;
    }
    .marche-input:focus, .marche-select:focus, .marche-textarea:focus {
        border-color: #17324D;
        box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.12);
        outline: none;
    }
    .marche-import-card {
        background: linear-gradient(135deg, rgba(30,64,175,0.06), rgba(212,175,55,0.1));
        border: 1px solid rgba(30, 64, 175, 0.15);
        border-radius: 16px;
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.25rem;
    }
    .marche-fieldset {
        border: 1px solid #e8ecf4 !important;
        border-radius: 14px !important;
        background: #fafbff !important;
        padding: 1.25rem !important;
    }
    .marche-fieldset legend {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: var(--prowise-blue-dark) !important;
        float: none;
        width: auto;
        padding: 0 0.5rem;
    }
    .marche-intitule-cell {
        display: inline-block;
        max-width: min(380px, 32vw);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    .marche-statut-badge {
        font-weight: 600;
        font-size: 0.75rem;
        padding: 0.3em 0.7em;
        border-radius: 9999px;
    }
    .marche-statut--en-cours {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        color: #fff !important;
        border: none !important;
    }
    .marche-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
    .marche-empty i { font-size: 2.5rem; color: #cbd5e1; display: block; margin-bottom: 0.75rem; }
</style>
