<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>@yield('titre')</title>
<style>
    @page { margin: 26px 28px 44px 28px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #0F172A; }
    .stripe { width: 100%; border-collapse: collapse; }
    .stripe td { height: 4px; padding: 0; }
    .head { width: 100%; border-collapse: collapse; margin: 10px 0 14px; }
    .head td { vertical-align: middle; padding: 0; }
    .logo { width: 44px; height: 44px; }
    .org { font-size: 11px; font-weight: bold; color: #0B1E3D; }
    .org-sub { font-size: 9px; color: #64748B; }
    h1 { font-size: 17px; margin: 0; color: #0B1E3D; }
    .sub { font-size: 10px; color: #64748B; margin-top: 3px; }
    h2 { font-size: 12px; color: #0B1E3D; margin: 16px 0 6px; border-bottom: 1px solid #CBD5E1; padding-bottom: 3px; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background: #0B1E3D; color: #fff; text-align: left; padding: 6px 7px; font-size: 9px; }
    table.data td { padding: 5px 7px; border-bottom: 1px solid #E2E8F0; vertical-align: top; }
    table.data tr { page-break-inside: avoid; }
    table.data tbody tr:nth-child(even) td { background: #F8FAFC; }
    .num { text-align: right; }
    .kpis { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px; }
    .kpis td { width: 25%; border: 1px solid #E2E8F0; border-left: 4px solid #1DB87A; padding: 8px 10px; }
    .kpi-l { font-size: 8px; color: #64748B; text-transform: uppercase; }
    .kpi-v { font-size: 18px; font-weight: bold; margin-top: 3px; }
    .kpi-h { font-size: 8px; color: #64748B; margin-top: 2px; }
    .muted { color: #64748B; }
    .empty { text-align: center; color: #64748B; padding: 24px 0; }
    .foot { position: fixed; bottom: -28px; left: 0; right: 0; font-size: 8px; color: #94A3B8; text-align: center; }
</style>
</head>
<body>

<table class="stripe"><tr>
    <td style="background:#009E60;width:33.3%"></td>
    <td style="background:#FBDE4A;width:33.3%"></td>
    <td style="background:#CE1126;width:33.4%"></td>
</tr></table>

<table class="head"><tr>
    @if($logo)<td style="width:54px"><img class="logo" src="{{ $logo }}" alt=""></td>@endif
    <td>
        <div class="org">{{ $service->nom }}</div>
        <div class="org-sub">CongoAssist — Plateforme de gestion des urgences</div>
    </td>
    <td style="text-align:right" class="org-sub">Généré le {{ $genereLe }}</td>
</tr></table>

@yield('corps')

<div class="foot">{{ $service->nom }} — document généré par CongoAssist le {{ $genereLe }}</div>

</body>
</html>
