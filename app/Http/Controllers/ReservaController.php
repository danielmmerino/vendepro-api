<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReservaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_desde' => ['nullable','date'],
            'fecha_hasta' => ['nullable','date'],
            'mesa_id' => ['nullable','uuid'],
            'estado' => ['nullable','in:pendiente,confirmada,cancelada,no_show'],
            'canal' => ['nullable','in:telefono,web,app,walk_in,tercero'],
            'q' => ['nullable','string'],
            'sort' => ['nullable','string'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:100'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation',
                'fields' => $validator->errors()->toArray(),
            ], 422);
        }
        $page = max((int)$request->query('page',1),1);
        $per = min((int)$request->query('per_page',20),100);
        $off = ($page-1)*$per;
        $mesa_id = $request->query('mesa_id');
        $estado = $request->query('estado');
        $canal = $request->query('canal');
        $fini = $request->query('fecha_desde');
        $ffin = $request->query('fecha_hasta');
        $q = $request->query('q');
        $sort = $request->query('sort','inicio');
        $allowedSorts = ['inicio','fin','estado','canal','cliente_nombre'];
        $order = [];
        foreach (explode(',', $sort) as $s) {
            $s = trim($s);
            if ($s==='') continue;
            $dir = 'asc';
            if (str_starts_with($s,'-')) { $dir='desc'; $s=substr($s,1); }
            if (!in_array($s,$allowedSorts)) continue;
            $order[] = "$s $dir";
        }
        if (!$order) { $order[] = 'inicio asc'; }
        $orderSql = implode(', ',$order);
        $params = [
            'mesa_id'=>$mesa_id,
            'estado'=>$estado,
            'canal'=>$canal,
            'fini'=>$fini,
            'ffin'=>$ffin,
            'q'=>$q,
            'limit'=>$per,
            'offset'=>$off,
        ];
        $sql = "SELECT r.*
FROM reservas r
WHERE (:mesa_id IS NULL OR r.mesa_id = :mesa_id)
  AND (:estado IS NULL OR r.estado = :estado)
  AND (:canal IS NULL OR r.canal = :canal)
  AND (:fini IS NULL OR r.inicio >= :fini)
  AND (:ffin IS NULL OR r.inicio < DATE_ADD(:ffin, INTERVAL 1 DAY))
  AND (:q IS NULL OR (r.cliente_nombre LIKE CONCAT('%',:q,'%') OR r.cliente_telefono LIKE CONCAT('%',:q,'%') OR r.cliente_email LIKE CONCAT('%',:q,'%')))
ORDER BY $orderSql
LIMIT :limit OFFSET :offset";
        $rows = DB::select($sql,$params);
        $countSql = "SELECT COUNT(1) AS total
FROM reservas r
WHERE (:mesa_id IS NULL OR r.mesa_id = :mesa_id)
  AND (:estado IS NULL OR r.estado = :estado)
  AND (:canal IS NULL OR r.canal = :canal)
  AND (:fini IS NULL OR r.inicio >= :fini)
  AND (:ffin IS NULL OR r.inicio < DATE_ADD(:ffin, INTERVAL 1 DAY))
  AND (:q IS NULL OR (r.cliente_nombre LIKE CONCAT('%',:q,'%') OR r.cliente_telefono LIKE CONCAT('%',:q,'%') OR r.cliente_email LIKE CONCAT('%',:q,'%')))
";
        $total = DB::selectOne($countSql,$params)->total ?? 0;
        return response()->json([
            'data' => array_map(fn($r) => (array)$r, $rows),
            'pagination' => [
                'page' => $page,
                'per_page' => $per,
                'total' => (int)$total,
            ],
        ]);
    }

    public function store(StoreReservaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $idem = $request->header('Idempotency-Key');
        if ($idem) {
            $existing = DB::table('reservas')->where('idempotency_key',$idem)->first();
            if ($existing) {
                $model = Reserva::find($existing->id);
                return response()->json(['data' => new ReservaResource($model)], 200);
            }
            $data['idempotency_key'] = $idem;
        }
        $mesa = DB::selectOne("SELECT capacidad, estado FROM mesas WHERE id = :id", ['id'=>$data['mesa_id']]);
        if (!$mesa || $mesa->estado !== 'activa') {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['mesa_id'=>['invalida']],
            ], 422);
        }
        if ($data['comensales'] <=0 || $data['comensales'] > $mesa->capacidad) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['comensales'=>['fuera_de_capacidad']],
            ], 422);
        }
        if (strtotime($data['inicio']) >= strtotime($data['fin'])) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['inicio'=>['rango_invalido']],
            ], 422);
        }
        $conf = DB::selectOne(
            "SELECT COUNT(1) AS conflictos
FROM reservas
WHERE mesa_id = :mesa_id
  AND estado IN ('pendiente','confirmada')
  AND id <> COALESCE(:reserva_id,'00000000-0000-0000-0000-000000000000')
  AND (inicio < :fin AND fin > :inicio)",
            ['mesa_id'=>$data['mesa_id'],'reserva_id'=>null,'inicio'=>$data['inicio'],'fin'=>$data['fin']]
        );
        if ($conf->conflictos > 0) {
            return response()->json([
                'error' => 'Validation',
                'fields' => ['inicio'=>['conflicto']]
            ], 422);
        }
        $data['estado'] = $data['estado'] ?? 'pendiente';
        $data['canal'] = $data['canal'] ?? 'telefono';
        return DB::transaction(function () use ($data) {
            $data['id'] = (string) Str::uuid();
            $reserva = Reserva::create($data);
            return response()->json(['data' => new ReservaResource($reserva)], 201);
        });
    }

    public function show(Reserva $reserva): JsonResponse
    {
        return response()->json(['data' => new ReservaResource($reserva)]);
    }

    public function update(UpdateReservaRequest $request, Reserva $reserva): JsonResponse
    {
        if (!in_array($reserva->estado, ['pendiente','confirmada'])) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Solo se puede modificar si esta pendiente o confirmada',
            ], 409);
        }
        $data = $request->validated();
        $mesaId = $data['mesa_id'] ?? $reserva->mesa_id;
        $inicio = $data['inicio'] ?? $reserva->inicio;
        $fin = $data['fin'] ?? $reserva->fin;
        $comensales = $data['comensales'] ?? $reserva->comensales;
        if ($mesaId != $reserva->mesa_id || $inicio != $reserva->inicio || $fin != $reserva->fin || $comensales != $reserva->comensales) {
            $mesa = DB::selectOne("SELECT capacidad, estado FROM mesas WHERE id = :id", ['id'=>$mesaId]);
            if (!$mesa || $mesa->estado !== 'activa') {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['mesa_id'=>['invalida']],
                ], 422);
            }
            if ($comensales <=0 || $comensales > $mesa->capacidad) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['comensales'=>['fuera_de_capacidad']],
                ], 422);
            }
            if (strtotime($inicio) >= strtotime($fin)) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['inicio'=>['rango_invalido']],
                ], 422);
            }
            $conf = DB::selectOne(
                "SELECT COUNT(1) AS conflictos
FROM reservas
WHERE mesa_id = :mesa_id
  AND estado IN ('pendiente','confirmada')
  AND id <> :reserva_id
  AND (inicio < :fin AND fin > :inicio)",
                ['mesa_id'=>$mesaId,'reserva_id'=>$reserva->id,'inicio'=>$inicio,'fin'=>$fin]
            );
            if ($conf->conflictos > 0) {
                return response()->json([
                    'error' => 'Validation',
                    'fields' => ['inicio'=>['conflicto']]
                ], 422);
            }
        }
        $data['mesa_id'] = $mesaId;
        $data['inicio'] = $inicio;
        $data['fin'] = $fin;
        $data['comensales'] = $comensales;
        return DB::transaction(function () use ($reserva, $data) {
            $reserva->update($data);
            return response()->json(['data' => new ReservaResource($reserva)]);
        });
    }

    public function destroy(Reserva $reserva): JsonResponse
    {
        if (!in_array($reserva->estado, ['pendiente','confirmada'])) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'Solo se puede eliminar si esta pendiente o confirmada',
            ], 409);
        }
        return DB::transaction(function () use ($reserva) {
            $reserva->estado = 'cancelada';
            $reserva->save();
            $reserva->delete();
            return response()->json(['data' => true]);
        });
    }
}
