    /* Étiquettes type Rappels iOS (agenda dashboard + page RDV) */
    .ios-agenda-wrap { margin-bottom: 1.5rem; }
    .ios-reminders-card {
        background: #f2f2f7;
        border-radius: 16px;
        padding: 14px 14px 16px;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .ios-reminders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding: 0 2px;
    }
    .ios-reminders-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f2438;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ios-reminders-hint {
        font-size: 0.72rem;
        font-weight: 600;
        color: #8e8e93;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0;
        padding: 0 2px;
    }
    .ios-reminders-seeall {
        font-size: 0.8rem;
        color: #198754;
        text-decoration: none;
        font-weight: 600;
    }
    .ios-reminders-seeall:hover { text-decoration: underline; color: #146c43; }
    .ios-reminders-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .ios-reminder-chip {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border-radius: 12px;
        padding: 10px 12px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .ios-reminder-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(30, 64, 175, 0.12);
    }
    .ios-reminder-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .ios-reminder-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .ios-reminder-line1 {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1c1c1e;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ios-reminder-line2 {
        font-size: 0.78rem;
        color: #8e8e93;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ios-reminder-chev {
        color: #c7c7cc;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .ios-reminders-empty {
        text-align: center;
        color: #8e8e93;
        font-size: 0.88rem;
        padding: 1.25rem 0.5rem;
    }
    .ios-reminders-empty i {
        font-size: 1.25rem;
        display: block;
        margin-bottom: 0.35rem;
        opacity: 0.65;
    }
    .home-mt-modal {
        border-radius: 16px;
        overflow: hidden;
    }
    .home-mt-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    @media (max-width: 576px) {
        .home-mt-grid { grid-template-columns: 1fr; }
    }
    .home-mt-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 6px;
        padding: 14px 10px;
        border: none;
        border-radius: 14px;
        background: #f2f2f7;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }
    .home-mt-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        background: #e8e8ed;
    }
    .home-mt-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .home-mt-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1c1c1e;
    }
    .home-mt-hint {
        font-size: 0.7rem;
        color: #8e8e93;
        line-height: 1.2;
    }
