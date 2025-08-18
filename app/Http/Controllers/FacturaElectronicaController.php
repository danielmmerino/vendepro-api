<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Services\Sri\FacturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaElectronicaController extends Controller
{
    public function __construct(private FacturaService $service)
    {
    }

    public function index(Request $request)
    {
        $q = Factura::query();
        if ($request->filled('estado')) {
            $q->where('estado', $request->estado);
        }
        if ($request->filled('cliente_id')) {
            $q->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('desde')) {
            $q->whereDate('fecha_emision','>=',$request->desde);
        }
        if ($request->filled('hasta')) {
            $q->whereDate('fecha_emision','<=',$request->hasta);
        }
        $paginator = $q->orderBy('fecha_emision','desc')->paginate(min($request->per_page ?? 20,100));
        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page'=>$paginator->currentPage(),
                'per_page'=>$paginator->perPage(),
                'total'=>$paginator->total()
            ]
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ambiente'=>'required|integer',
            'establecimiento'=>'required|string',
            'punto_emision'=>'required|string',
            'fecha_emision'=>'required|date',
            'cliente.identificacion'=>'required|string',
            'cliente.tipo'=>'required|string',
            'cliente.razon_social'=>'required|string',
            'cliente.email'=>'nullable|email',
            'cliente.direccion'=>'nullable|string',
            'items'=>'required|array|min:1',
            'items.*.descripcion'=>'required|string',
            'items.*.cantidad'=>'required|numeric',
            'items.*.precio_unitario'=>'required|numeric',
            'items.*.descuento'=>'nullable|numeric',
            'items.*.impuesto.codigo'=>'required|string',
            'items.*.impuesto.tarifa'=>'required|numeric',
            'propina'=>'nullable|numeric',
            'observacion'=>'nullable|string'
        ]);
        return DB::transaction(function () use ($data) {
            $factura = Factura::create([
                'ambiente'=>$data['ambiente'],
                'establecimiento'=>$data['establecimiento'],
                'punto_emision'=>$data['punto_emision'],
                'fecha_emision'=>$data['fecha_emision'],
                'estado'=>'borrador',
                'cliente_identificacion'=>$data['cliente']['identificacion'],
                'cliente_tipo'=>$data['cliente']['tipo'],
                'cliente_razon_social'=>$data['cliente']['razon_social'],
                'cliente_email'=>$data['cliente']['email'] ?? null,
                'cliente_direccion'=>$data['cliente']['direccion'] ?? null,
                'propina'=>$data['propina'] ?? 0,
                'observacion'=>$data['observacion'] ?? null,
            ]);
            $totales = ['subtotal_0'=>0,'subtotal_12'=>0,'subtotal_15'=>0,'subtotal_exento'=>0,'subtotal_no_objeto'=>0,'descuento_total'=>0,'iva_total'=>0,'total'=>0];
            foreach ($data['items'] as $it) {
                $base = $it['cantidad'] * $it['precio_unitario'] - ($it['descuento'] ?? 0);
                $iva = $base * (($it['impuesto']['tarifa'] ?? 0)/100);
                $totales['descuento_total'] += $it['descuento'] ?? 0;
                if (($it['impuesto']['tarifa'] ?? 0) == 0) { $totales['subtotal_0'] += $base; }
                elseif (($it['impuesto']['tarifa'] ?? 0) == 12) { $totales['subtotal_12'] += $base; }
                elseif (($it['impuesto']['tarifa'] ?? 0) == 15) { $totales['subtotal_15'] += $base; }
                $totales['iva_total'] += $iva;
                $totales['total'] += $base + $iva;
                FacturaDetalle::create([
                    'factura_id'=>$factura->id,
                    'descripcion'=>$it['descripcion'],
                    'cantidad'=>$it['cantidad'],
                    'precio_unitario'=>$it['precio_unitario'],
                    'descuento'=>$it['descuento'] ?? 0,
                    'impuesto_codigo'=>$it['impuesto']['codigo'],
                    'impuesto_tarifa'=>$it['impuesto']['tarifa'],
                    'iva_monto'=>$iva,
                    'total_linea'=>$base + $iva
                ]);
            }
            $factura->update($totales);
            $factura->load('items');
            return response()->json(['data'=>$factura],201);
        });
    }

    public function show($id)
    {
        $factura = Factura::with('items')->find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        return ['data'=>$factura];
    }

    public function update(Request $request, $id)
    {
        $factura = Factura::with('items')->find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        if ($factura->estado !== 'borrador') return response()->json(['error'=>['message'=>'Solo borrador']],409);
        $data = $request->validate([
            'ambiente'=>'required|integer',
            'establecimiento'=>'required|string',
            'punto_emision'=>'required|string',
            'fecha_emision'=>'required|date',
            'cliente.identificacion'=>'required|string',
            'cliente.tipo'=>'required|string',
            'cliente.razon_social'=>'required|string',
            'cliente.email'=>'nullable|email',
            'cliente.direccion'=>'nullable|string',
            'items'=>'required|array|min:1',
            'items.*.descripcion'=>'required|string',
            'items.*.cantidad'=>'required|numeric',
            'items.*.precio_unitario'=>'required|numeric',
            'items.*.descuento'=>'nullable|numeric',
            'items.*.impuesto.codigo'=>'required|string',
            'items.*.impuesto.tarifa'=>'required|numeric',
            'propina'=>'nullable|numeric',
            'observacion'=>'nullable|string'
        ]);
        return DB::transaction(function () use ($data, $factura) {
            $factura->items()->delete();
            $factura->update([
                'ambiente'=>$data['ambiente'],
                'establecimiento'=>$data['establecimiento'],
                'punto_emision'=>$data['punto_emision'],
                'fecha_emision'=>$data['fecha_emision'],
                'cliente_identificacion'=>$data['cliente']['identificacion'],
                'cliente_tipo'=>$data['cliente']['tipo'],
                'cliente_razon_social'=>$data['cliente']['razon_social'],
                'cliente_email'=>$data['cliente']['email'] ?? null,
                'cliente_direccion'=>$data['cliente']['direccion'] ?? null,
                'propina'=>$data['propina'] ?? 0,
                'observacion'=>$data['observacion'] ?? null,
            ]);
            $totales = ['subtotal_0'=>0,'subtotal_12'=>0,'subtotal_15'=>0,'subtotal_exento'=>0,'subtotal_no_objeto'=>0,'descuento_total'=>0,'iva_total'=>0,'total'=>0];
            foreach ($data['items'] as $it) {
                $base = $it['cantidad'] * $it['precio_unitario'] - ($it['descuento'] ?? 0);
                $iva = $base * (($it['impuesto']['tarifa'] ?? 0)/100);
                $totales['descuento_total'] += $it['descuento'] ?? 0;
                if (($it['impuesto']['tarifa'] ?? 0) == 0) { $totales['subtotal_0'] += $base; }
                elseif (($it['impuesto']['tarifa'] ?? 0) == 12) { $totales['subtotal_12'] += $base; }
                elseif (($it['impuesto']['tarifa'] ?? 0) == 15) { $totales['subtotal_15'] += $base; }
                $totales['iva_total'] += $iva;
                $totales['total'] += $base + $iva;
                FacturaDetalle::create([
                    'factura_id'=>$factura->id,
                    'descripcion'=>$it['descripcion'],
                    'cantidad'=>$it['cantidad'],
                    'precio_unitario'=>$it['precio_unitario'],
                    'descuento'=>$it['descuento'] ?? 0,
                    'impuesto_codigo'=>$it['impuesto']['codigo'],
                    'impuesto_tarifa'=>$it['impuesto']['tarifa'],
                    'iva_monto'=>$iva,
                    'total_linea'=>$base + $iva
                ]);
            }
            $factura->update($totales);
            $factura->load('items');
            return ['data'=>$factura];
        });
    }

    public function destroy($id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        if ($factura->estado !== 'borrador') return response()->json(['error'=>['message'=>'Solo borrador']],409);
        $factura->delete();
        return response()->json(null,204);
    }

    public function emitir(Request $request, $id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        $data = $this->service->emitir($factura);
        return ['data'=>$data];
    }

    public function reintentarEnvio($id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        return ['data'=>$this->service->reintentarEnvio($factura)];
    }

    public function estadoSri($id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        return ['data'=>$this->service->consultarEstado($factura)];
    }

    public function xml($id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        return response($this->service->obtenerXml($factura),200,['Content-Type'=>'application/xml']);
    }

    public function pdf(Request $request, $id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        $formato = $request->query('formato','A4');
        return ['data'=>$this->service->generarPdf($factura,$formato)];
    }

    public function email(Request $request, $id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        $request->validate(['to'=>'required|array']);
        return ['data'=>['enviado'=>true]];
    }

    public function anular($id)
    {
        $factura = Factura::find($id);
        if (!$factura) return response()->json(['error'=>['message'=>'Not found']],404);
        if ($factura->estado === 'autorizada') {
            return response()->json(['error'=>['message'=>'Requiere nota de crédito']],409);
        }
        $factura->estado = 'anulada';
        $factura->save();
        return ['data'=>$factura];
    }
}
