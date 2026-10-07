@php
    $a = $societe['accent_color'] ?? '#00b5cc';
    $t = $societe['text_color'] ?? '#111111';
@endphp
<style>
    body { margin: 0; padding: 12px; background: #fff; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: {{ $t }}; }
    .bc-doc { max-width: 210mm; margin: 0 auto; padding: 0 8px 24px; }
    .bc-title {
        color: {{ $a }};
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 8px 0;
        padding-bottom: 6px;
        border-bottom: 3px solid {{ $a }};
    }
    .bc-co-name { font-weight: 700; font-size: 12px; margin-bottom: 4px; }
    .bc-co-lines { font-style: italic; color: #444; margin-bottom: 16px; line-height: 1.4; }
    .bc-row { width: 100%; margin-bottom: 14px; }
    .bc-row:after { content: ''; display: table; clear: both; }
    .bc-box {
        float: left;
        width: 48%;
        background: #f3f4f6;
        padding: 10px 12px;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .bc-box-right { float: right; width: 48%; }
    .bc-box table { width: 100%; border-collapse: collapse; }
    .bc-box td { padding: 3px 0; vertical-align: top; }
    .bc-box td:first-child { font-weight: 600; width: 42%; color: #333; }
    .bc-dest-h { font-weight: 700; margin-bottom: 6px; font-size: 11px; }
    .bc-dest-name { font-weight: 700; font-size: 11px; margin-bottom: 4px; }
    .bc-dest-lines { font-style: italic; color: #444; line-height: 1.45; }
    .bc-add-h { font-weight: 700; margin: 14px 0 6px; font-size: 11px; }
    .bc-add-txt { border: 1px solid #e5e7eb; padding: 8px; min-height: 36px; border-radius: 4px; white-space: pre-wrap; }
    .bc-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
    .bc-table thead th {
        border-bottom: 2px solid {{ $a }};
        text-align: left;
        padding: 8px 6px;
        font-weight: 700;
        font-size: 9px;
        text-transform: uppercase;
    }
    .bc-table tbody td {
        border-bottom: 1px solid #e5e7eb;
        padding: 8px 6px;
        vertical-align: top;
    }
    .bc-table .num { text-align: right; }
    .bc-total-row { text-align: right; font-weight: 700; padding: 10px 6px; font-size: 11px; }
    .bc-sign-row { width: 100%; margin-top: 28px; }
    .bc-sign-col { float: left; width: 48%; }
    .bc-sign-col-r { float: right; width: 48%; }
    .bc-sign-h { font-weight: 700; margin-bottom: 12px; color: {{ $a }}; font-size: 11px; }
    .bc-sign-line { margin-top: 28px; border-top: 1px solid #ccc; padding-top: 6px; color: #666; }
    .bc-footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 8px; color: #555; }
    .bc-footer-col { float: left; width: 31%; margin-right: 2%; vertical-align: top; }
    .bc-footer-col:last-child { margin-right: 0; }
    .bc-footer h4 { margin: 0 0 4px; font-size: 8px; color: {{ $a }}; text-transform: uppercase; }
    .bc-bar { height: 6px; background: {{ $a }}; margin-top: 16px; }
    .bc-logo { max-height: 56px; margin-bottom: 8px; }
</style>
