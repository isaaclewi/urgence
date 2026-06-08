<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Response;

class CodeQRController extends Controller
{
    public function index()
    {
        if (!session()->has('citoyen_id')) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $citoyen = Citoyens::find(session('citoyen_id'));

        // Texte du QR Code
       $qrText = "Nom(s): {$citoyen->nom}\nPrénom(s): {$citoyen->prenom}\nMatricule: {$citoyen->matricule}\n" .
       "Adresse: {$citoyen->adresse}\n";


        // Génération du QR Code en PNG
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrText)
            ->size(300)
            ->margin(10)
            ->build();

        // Encode en base64 pour affichage direct
        $qrImage = base64_encode($result->getString());

        return view('codeQR', [
            'citoyen' => $citoyen,
            'qrImage' => $qrImage,
        ]);
    }

    // Optionnel: téléchargement direct
    public function download()
    {
        $citoyen = Citoyens::find(session('citoyen_id'));

        $qrText = "Nom: {$citoyen->nom} {$citoyen->prenom}\n";

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrText)
            ->size(300)
            ->build();

        return Response::make($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qrcode.png"'
        ]);
    }
}
