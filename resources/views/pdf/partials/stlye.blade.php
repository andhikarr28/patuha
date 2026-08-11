* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #1e293b;
}

h2.section-title {
    font-size: 13px;
    margin: 18px 0 8px;
    padding-bottom: 4px;
    border-bottom: 1px solid #cbd5e1;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

table.data-table th,
table.data-table td {
    border: 1px solid #cbd5e1;
    padding: 6px 8px;
    font-size: 11px;
}

table.data-table th {
    background: #f1f5f9;
    text-align: left;
    text-transform: uppercase;
    font-size: 10px;
    color: #334155;
}

table.data-table td.right, table.data-table th.right { text-align: right; }
table.data-table td.center, table.data-table th.center { text-align: center; }
table.data-table tfoot td { font-weight: bold; background: #f8fafc; }

.summary-box {
    display: table;
    width: 100%;
    margin-bottom: 16px;
    border-collapse: collapse;
}

.summary-cell {
    display: table-cell;
    border: 1px solid #cbd5e1;
    padding: 10px 12px;
}

.summary-label {
    font-size: 9.5px;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.3px;
}

.summary-value {
    font-size: 15px;
    font-weight: bold;
    margin-top: 3px;
    color: #0f172a;
}

.muted { color: #64748b; }

.badge {
    font-size: 9px;
    padding: 1px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    color: #334155;
}

.trx-block { margin-bottom: 10px; border: 1px solid #cbd5e1; padding: 8px; border-radius: 3px; }
.trx-header { font-weight: bold; margin-bottom: 6px; font-size: 11.5px; color: #0f172a; }

.footer-doc {
    margin-top: 24px;
    font-size: 9.5px;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
    padding-top: 8px;
}

.page-break { page-break-before: always; }