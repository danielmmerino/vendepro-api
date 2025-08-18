<?php

namespace App\Http\Controllers\Sri;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sri\NextSecuenciaRequest;
use App\Http\Resources\Sri\SecuenciaResource;
use App\Services\Sri\SecuenciaService;

class SecuenciaController extends Controller
{
    public function __construct(private SecuenciaService $service)
    {
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
