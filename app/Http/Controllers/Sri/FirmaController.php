<?php

namespace App\Http\Controllers\Sri;

use App\Http\Controllers\Controller;
use App\Models\Sri\Certificado;
use Illuminate\Http\Request;

class FirmaController extends Controller
{
    public function configurar(Request $request)
    {
        $data = $request->validate([
            'p12_base64'=>'required|string',
            'password'=>'required|string',
            'empresa_id'=>'required|string'
        ]);
        Certificado::updateOrCreate(
            ['emisor_id'=>$data['empresa_id']],
            [
                'alias'=>'principal',
                'p12_base64'=>$data['p12_base64'],
                'p12_password'=>$data['password'],
                'activo'=>true
            ]
        );
        return response()->json(['data'=>true],201);
    }

    public function estado(Request $request)
    {
        $empresaId = $request->query('empresa_id');
        $cert = Certificado::where('emisor_id',$empresaId)->first();
        return ['data'=>[
            'valido'=> (bool)$cert,
            'vence'=> $cert? $cert->valido_hasta : null,
            'issuer'=> $cert? $cert->alias : null
        ]];
    }
}
