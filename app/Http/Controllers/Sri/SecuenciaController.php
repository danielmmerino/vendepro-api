<?php

namespace App\Http\Controllers\Sri;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sri\NextSecuenciaRequest;
use App\Http\Resources\Sri\SecuenciaResource;
use App\Services\Sri\SecuenciaService;
use Illuminate\Http\Request;

class SecuenciaController extends Controller
{
    public function __construct(private SecuenciaService $service)
    {
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'emisor_id'=>'nullable|string',
            'establecimiento'=>'required|string',
            'punto'=>'required|string',
            'tipo'=>'required|string'
        ]);
        $numero = $this->service->current(
            $data['emisor_id'],
            $data['establecimiento'],
            $data['punto'],
            $data['tipo']
        );
        return ['data'=>$numero];
    }

    public function next(NextSecuenciaRequest $request)
    {
        $data = $request->validated();
        $numero = $this->service->next(
            $data['emisor_id'],
            $data['establecimiento'],
            $data['punto'],
            $data['tipo']
        );

        return new SecuenciaResource($numero);
    }
}
