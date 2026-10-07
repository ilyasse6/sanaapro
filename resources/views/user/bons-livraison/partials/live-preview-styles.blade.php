<style>
    .simulateur-wrapper-bl { position: sticky; top: 1rem; }
    .simulateur-bl {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(8, 145, 178, 0.12), 0 0 0 1px rgba(8, 145, 178, 0.08);
        overflow: hidden;
        border: 1px solid #e0f2fe;
    }
    .simulateur-bl-header {
        background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
        color: #fff;
        padding: 1rem 1.25rem;
        position: relative;
    }
    .simulateur-bl-header .live-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.75rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(255,255,255,0.25);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
    }
    .simulateur-bl-header .num-bl {
        font-weight: 800;
        font-size: 1.05rem;
        margin-top: 0.35rem;
        letter-spacing: 0.02em;
    }
    .simulateur-bl-body {
        padding: 1rem 1.1rem;
        font-size: 0.8rem;
        max-height: min(70vh, 520px);
        overflow-y: auto;
    }
    .simulateur-bl .label-sim {
        color: #64748b;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.15rem;
    }
    .simulateur-bl .value-sim {
        font-weight: 500;
        color: #0f172a;
        word-break: break-word;
    }
    .simulateur-bl .simulateur-placeholder { color: #94a3b8; font-style: italic; }
    .simulateur-bl-kv {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 0.6rem 0.75rem;
        font-size: 0.72rem;
        line-height: 1.45;
    }
    .simulateur-bl-kv div { margin-bottom: 0.2rem; }
    .simulateur-bl-kv div:last-child { margin-bottom: 0; }
    .simulateur-bl-kv strong { color: #0e7490; min-width: 7rem; display: inline-block; }
    .simulateur-bl-table {
        font-size: 0.7rem;
        width: 100%;
        margin: 0.5rem 0 0;
    }
    .simulateur-bl-table th {
        background: #ecfeff;
        color: #0e7490;
        font-weight: 700;
        padding: 0.35rem 0.3rem;
        border: 1px solid #cffafe;
    }
    .simulateur-bl-table td {
        padding: 0.35rem 0.3rem;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }
    .simulateur-bl-table .q { text-align: right; white-space: nowrap; }
    .simulateur-bl-infos {
        margin-top: 0.65rem;
        padding: 0.5rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
        font-size: 0.72rem;
        max-height: 4.5rem;
        overflow: hidden;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .simulateur-bl .badge-statut { font-size: 0.65rem; }
</style>
