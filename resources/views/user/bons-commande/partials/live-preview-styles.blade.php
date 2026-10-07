<style>
    .simulateur-wrapper-bc { position: sticky; top: 1rem; }
    .simulateur-bc {
        background: linear-gradient(180deg, #ffffff 0%, #faf5ff 100%);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(124, 58, 237, 0.12), 0 0 0 1px rgba(124, 58, 237, 0.08);
        overflow: hidden;
        border: 1px solid #ede9fe;
    }
    .simulateur-bc-header {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        color: #fff;
        padding: 1rem 1.25rem;
        position: relative;
    }
    .simulateur-bc-header .live-badge {
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
    .simulateur-bc-header .num-bc {
        font-weight: 800;
        font-size: 1.05rem;
        margin-top: 0.35rem;
        letter-spacing: 0.02em;
    }
    .simulateur-bc-body {
        padding: 1rem 1.1rem;
        font-size: 0.8rem;
        max-height: min(70vh, 520px);
        overflow-y: auto;
    }
    .simulateur-bc .label-sim {
        color: #64748b;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.15rem;
    }
    .simulateur-bc .value-sim {
        font-weight: 500;
        color: #0f172a;
        word-break: break-word;
    }
    .simulateur-bc .simulateur-placeholder { color: #94a3b8; font-style: italic; }
    .simulateur-bc-kv {
        background: #f5f3ff;
        border-radius: 10px;
        padding: 0.6rem 0.75rem;
        font-size: 0.72rem;
        line-height: 1.45;
    }
    .simulateur-bc-kv div { margin-bottom: 0.2rem; }
    .simulateur-bc-kv div:last-child { margin-bottom: 0; }
    .simulateur-bc-kv strong { color: #5b21b6; min-width: 7rem; display: inline-block; }
    .simulateur-bc-table {
        font-size: 0.7rem;
        width: 100%;
        margin: 0.5rem 0 0;
    }
    .simulateur-bc-table th {
        background: #ede9fe;
        color: #5b21b6;
        font-weight: 700;
        padding: 0.35rem 0.3rem;
        border: 1px solid #ddd6fe;
    }
    .simulateur-bc-table td {
        padding: 0.35rem 0.3rem;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }
    .simulateur-bc-table .q, .simulateur-bc-table .p { text-align: right; white-space: nowrap; }
    .simulateur-bc-infos {
        margin-top: 0.65rem;
        padding: 0.5rem;
        background: #faf5ff;
        border-radius: 8px;
        border: 1px dashed #c4b5fd;
        font-size: 0.72rem;
        max-height: 4.5rem;
        overflow: hidden;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .simulateur-bc .badge-statut { font-size: 0.65rem; }
    .simulateur-bc-total {
        margin-top: 0.5rem;
        text-align: right;
        font-weight: 700;
        font-size: 0.75rem;
        color: #5b21b6;
    }
</style>
