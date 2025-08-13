<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use App\Services\MesaAvailabilityService;
use Illuminate\Http\JsonResponse;

class ReservaController extends Controller
{
    public function store(StoreReservaRequest $request, MesaAvailabilityService $service): JsonResponse
    {
        $data = $request->validated();

        if (!$service->isAvailable($data['mesa_id'], $data['inicio'], $data['fin'])) {
            return response()->json([
                'errors' => [
                    'mesa_id' => ['La mesa no está disponible en el rango de tiempo indicado.'],
                ],
            ], 422);
        }

        $reserva = Reserva::create($data);

        return response()->json(['data' => new ReservaResource($reserva)], 201);
    }

    public function update(UpdateReservaRequest $request, Reserva $reserva, MesaAvailabilityService $service): JsonResponse
    {
        $data = $request->validated();

        if (!$service->isAvailable($data['mesa_id'], $data['inicio'], $data['fin'], $reserva->id)) {
            return response()->json([
                'errors' => [
                    'mesa_id' => ['La mesa no está disponible en el rango de tiempo indicado.'],
                ],
            ], 422);
        }

        $reserva->update($data);

        return response()->json(['data' => new ReservaResource($reserva)]);
    }

    public function confirm(Reserva $reserva, MesaAvailabilityService $service): JsonResponse
    {
        if (!$service->isAvailable($reserva->mesa_id, $reserva->inicio, $reserva->fin, $reserva->id)) {
            return response()->json([
                'errors' => [
                    'mesa_id' => ['La mesa no está disponible en el rango de tiempo indicado.'],
                ],
            ], 422);
        }

        $reserva->estado = 'confirmada';
        $reserva->save();

        return response()->json(['data' => new ReservaResource($reserva)]);
    }
}
