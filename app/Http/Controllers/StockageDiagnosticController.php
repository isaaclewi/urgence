<?php

namespace App\Http\Controllers;

use App\Support\ImageUpload;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Page de diagnostic (administrateur connecté) : /admin/stockage-test
 * Teste l'envoi d'un petit fichier vers Supabase Storage et affiche la VRAIE erreur s'il échoue.
 * N'affiche jamais les clés secrètes.
 */
class StockageDiagnosticController extends Controller
{
    public function index()
    {
        $cfg = config('filesystems.disks.supabase');
        $lignes = [];

        $lignes[] = ['SUPABASE_KEY renseignée', ! empty($cfg['key']) ? 'oui' : 'NON  <-- à renseigner'];
        $lignes[] = ['SUPABASE_SECRET renseignée', ! empty($cfg['secret']) ? 'oui' : 'NON  <-- à renseigner'];
        $lignes[] = ['SUPABASE_BUCKET', $cfg['bucket'] ?: 'VIDE  <-- à renseigner'];
        $lignes[] = ['SUPABASE_REGION', $cfg['region']];
        $lignes[] = ['SUPABASE_ENDPOINT', $cfg['endpoint'] ?: 'VIDE  <-- à renseigner (https://xxxx.supabase.co/storage/v1/s3)'];
        $lignes[] = ['SUPABASE_PUBLIC_URL', $cfg['public_url'] ?: 'vide (déduite automatiquement)'];
        $lignes[] = ['URL publique utilisée', ImageUpload::base() ?: 'IMPOSSIBLE À CALCULER'];
        $lignes[] = ['Version PHP / extension GD', PHP_VERSION . ' / ' . (function_exists('imagecreatefromstring') ? 'GD ok' : 'GD absente')];

        $chemin = 'diagnostic/test-' . now()->format('YmdHis') . '.txt';
        $url    = ImageUpload::url($chemin);

        try {
            // Disque temporaire avec "throw" activé pour obtenir le message d'erreur réel.
            $disque = Storage::build(array_merge($cfg, ['throw' => true]));

            $disque->put($chemin, 'test CongoAssist ' . now());
            $lignes[] = ['1) Envoi vers Supabase', 'OK'];

            try {
                $rep = Http::timeout(10)->get($url);
                $lignes[] = ['2) Lecture publique de ' . $url,
                    $rep->successful()
                        ? 'OK (HTTP ' . $rep->status() . ')'
                        : 'ECHEC HTTP ' . $rep->status() . '  <-- le bucket n\'est probablement pas PUBLIC, ou SUPABASE_PUBLIC_URL est faux'];
            } catch (\Throwable $e) {
                $lignes[] = ['2) Lecture publique', 'ECHEC : ' . $e->getMessage()];
            }

            try {
                $disque->delete($chemin);
                $lignes[] = ['3) Suppression du fichier test', 'OK'];
            } catch (\Throwable $e) {
                $lignes[] = ['3) Suppression', 'ECHEC : ' . $e->getMessage()];
            }
        } catch (\Throwable $e) {
            $lignes[] = ['1) Envoi vers Supabase', 'ECHEC : ' . get_class($e) . ' — ' . $e->getMessage()];
        }

        $html = '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
              . '<title>Diagnostic stockage</title>'
              . '<body style="font-family:system-ui,sans-serif;padding:16px;max-width:900px;margin:auto">'
              . '<h2>Diagnostic du stockage des images (Supabase)</h2><table style="border-collapse:collapse;width:100%">';

        foreach ($lignes as [$k, $v]) {
            $mauvais = str_contains($v, 'ECHEC') || str_contains($v, 'NON') || str_contains($v, 'VIDE') || str_contains($v, 'IMPOSSIBLE');
            $html .= '<tr><td style="border:1px solid #ddd;padding:8px;font-weight:600;vertical-align:top">' . e($k) . '</td>'
                   . '<td style="border:1px solid #ddd;padding:8px;word-break:break-word;color:' . ($mauvais ? '#b91c1c' : '#065f46') . '">' . e($v) . '</td></tr>';
        }

        $html .= '</table><p style="color:#666;font-size:13px">Envoyez-moi une capture de cette page si une ligne est en rouge.</p></body>';

        return response($html);
    }
}
