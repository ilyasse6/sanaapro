@php
    $a = $societe['accent_color'] ?? '#00b5cc';
    $t = $societe['text_color'] ?? '#111111';
@endphp
<style>
    body { margin: 0; padding: 12px; background: #fff; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: {{ $t }}; }
    .bl-doc { max-width: 210mm; margin: 0 auto; padding: 0 8px 24px; }
    .bl-title {
        color: {{ $a }};
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 8px 0;
        padding-bottom: 6px;
        border-bottom: 3px solid {{ $a }};
    }
    .bl-co-name { font-weight: 700; font-size: 12px; margin-bottom: 4px; }
    .bl-co-lines { font-style: italic; color: #444; margin-bottom: 16px; line-height: 1.4; }
    .bl-row { width: 100%; margin-bottom: 14px; }
    .bl-row:after { content: ''; display: table; clear: both; }
    .bl-box {
        float: left;
        width: 48%;
        background: #f3f4f6;
        padding: 10px 12px;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .bl-box-right { float: right; width: 48%; }
    .bl-box table { width: 100%; border-collapse: collapse; }
    .bl-box td { padding: 3px 0; vertical-align: top; }
    .bl-box td:first-child { font-weight: 600; width: 42%; color: #333; }
    .bl-dest-h { font-weight: 700; margin-bottom: 6px; font-size: 11px; }
    .bl-dest-name { font-weight: 700; font-size: 11px; margin-bottom: 4px; }
    .bl-dest-lines { font-style: italic; color: #444; line-height: 1.45; }
    .bl-add-h { font-weight: 700; margin: 14px 0 6px; font-size: 11px; }
    .bl-add-txt { border: 1px solid #e5e7eb; padding: 8px; min-height: 36px; border-radius: 4px; white-space: pre-wrap; }
    .bl-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
    .bl-table thead th {
        border-bottom: 2px solid {{ $a }};
        text-align: left;
        padding: 8px 6px;
        font-weight: 700;
        font-size: 9px;
        text-transform: uppercase;
    }
    .bl-table tbody td {
        border-bottom: 1px solid #e5e7eb;
        padding: 8px 6px;
        vertical-align: top;
    }
    .bl-table .num { text-align: right; }
    .bl-sign-row { width: 100%; margin-top: 28px; }
    .bl-sign-col { float: left; width: 48%; }
    .bl-sign-col-r { float: right; width: 48%; }
    .bl-sign-h { font-weight: 700; margin-bottom: 12px; color: {{ $a }}; font-size: 11px; }
    .bl-sign-line { margin-top: 28px; border-top: 1px solid #ccc; padding-top: 6px; color: #666; }
    .bl-footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 8px; color: #555; }
    .bl-footer-col { float: left; width: 31%; margin-right: 2%; vertical-align: top; }
    .bl-footer-col:last-child { margin-right: 0; }
    .bl-footer h4 { margin: 0 0 4px; font-size: 8px; color: {{ $a }}; text-transform: uppercase; }
    .bl-bar { height: 6px; background: {{ $a }}; margin-top: 16px; }
    .bl-logo { max-height: 56px; margin-bottom: 8px; }
</style>
