<style>
    .transaction-index {
        --transaction-ink: #153f3a;
        --transaction-muted: #68817c;
        --transaction-line: rgba(18, 63, 55, 0.1);
        color: var(--transaction-ink);
    }

    .transaction-index > .row {
        margin-right: 0;
        margin-left: 0;
    }

    .transaction-index > .row > .col-12 {
        padding-right: 0;
        padding-left: 0;
    }

    .transaction-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .transaction-heading h1 {
        margin: 0;
        color: var(--transaction-ink);
        font-size: 1.5rem;
        font-weight: 700;
    }

    .transaction-heading p {
        margin: 0.25rem 0 0;
        color: var(--transaction-muted);
        font-size: 0.88rem;
    }

    .transaction-heading .btn-success {
        flex-shrink: 0;
        border-color: #137d52;
        background: #137d52;
    }

    .transaction-flash {
        border-radius: 6px;
    }

    .transaction-filter {
        margin-bottom: 1rem;
        padding: 0.9rem;
        border: 1px solid var(--transaction-line);
        border-radius: 8px;
        background: #fff;
    }

    .transaction-filter .form-label {
        margin-bottom: 0.3rem;
        color: var(--transaction-muted);
        font-size: 0.75rem;
        font-weight: 650;
    }

    .transaction-filter .form-control,
    .transaction-filter .form-select {
        min-height: 38px;
        border-color: var(--transaction-line);
        border-radius: 6px;
        font-size: 0.84rem;
    }

    .transaction-table-panel {
        overflow: hidden;
        border: 1px solid var(--transaction-line);
        border-radius: 8px;
        background: #fff;
    }

    .transaction-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--transaction-line);
    }

    .transaction-table-header h2 {
        margin: 0;
        color: var(--transaction-ink);
        font-size: 0.95rem;
        font-weight: 700;
    }

    .transaction-table-header span {
        color: var(--transaction-muted);
        font-size: 0.78rem;
        white-space: nowrap;
    }

    .transaction-table {
        min-width: 760px;
        margin: 0;
        color: var(--transaction-ink);
        vertical-align: middle;
    }

    .transaction-table th {
        padding: 0.7rem 0.85rem;
        border-bottom: 1px solid var(--transaction-line);
        background: #f6faf8;
        color: var(--transaction-muted);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .transaction-table td {
        padding: 0.75rem 0.85rem;
        border-bottom-color: var(--transaction-line);
        font-size: 0.83rem;
    }

    .transaction-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .transaction-primary {
        color: var(--transaction-ink);
        font-weight: 650;
    }

    .transaction-secondary {
        display: block;
        margin-top: 0.15rem;
        color: var(--transaction-muted);
        font-size: 0.73rem;
    }

    .transaction-status {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 650;
        white-space: nowrap;
    }

    .transaction-status-pending,
    .transaction-status-available { background: #fff3dc; color: #805300; }
    .transaction-status-shipped,
    .transaction-status-claimed { background: #e8f1fb; color: #245988; }
    .transaction-status-delivered,
    .transaction-status-confirmed,
    .transaction-status-completed { background: #e6f5ed; color: #176b43; }
    .transaction-status-cancelled { background: #fde9e7; color: #a02d25; }

    .transaction-sentiment-positive { background: #e6f5ed; color: #176b43; }
    .transaction-sentiment-neutral { background: #eef2f1; color: #526762; }
    .transaction-sentiment-negative { background: #fde9e7; color: #a02d25; }

    .transaction-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .transaction-action {
        display: inline-flex;
        width: 2rem;
        height: 2rem;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--transaction-line);
        border-radius: 6px;
        background: #fff;
        color: var(--transaction-ink);
        text-decoration: none;
    }

    .transaction-action:hover,
    .transaction-action:focus-visible {
        border-color: #137d52;
        background: #f2faf5;
        color: #0b6843;
    }

    .transaction-action-danger:hover,
    .transaction-action-danger:focus-visible {
        border-color: #bd4b43;
        background: #fff5f4;
        color: #a02d25;
    }

    .transaction-empty {
        padding: 2.5rem 1rem !important;
        color: var(--transaction-muted);
        text-align: center;
    }

    .transaction-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 0.85rem;
    }

    .transaction-pagination-summary {
        color: var(--transaction-muted);
        font-size: 0.76rem;
    }

    .transaction-pagination .pagination {
        gap: 0.25rem;
        margin: 0;
    }

    .transaction-pagination .page-link {
        min-width: 2.1rem;
        padding: 0.35rem 0.55rem;
        border-color: var(--transaction-line);
        border-radius: 6px !important;
        color: #137d52;
        font-size: 0.78rem;
        text-align: center;
    }

    .transaction-pagination .page-item.active .page-link {
        border-color: #137d52;
        background: #137d52;
        color: #fff;
    }

    .transaction-pagination .page-item.disabled .page-link {
        color: #97a6a2;
    }

    @media (max-width: 768px) {
        .transaction-heading {
            align-items: flex-start;
        }

        .transaction-heading h1 {
            font-size: 1.25rem;
        }

        .transaction-heading .btn-success {
            padding: 0.4rem 0.55rem;
            font-size: 0.78rem;
        }

        .transaction-pagination {
            align-items: flex-start;
            flex-direction: column;
        }

        .transaction-pagination .pagination {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 480px) {
        .transaction-heading {
            flex-direction: column;
        }

        .transaction-heading .btn-success {
            width: 100%;
        }
    }
</style>