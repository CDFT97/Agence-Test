<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\CaoCliente;
use App\Models\CaoFactura;
use App\Models\CaoUsuario;
use App\Traits\CalculateTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AgenceController extends Controller
{
    use CalculateTrait;

    public function agence(Request $request)
    {
        $fechaInicio = '2005-01-01';
        $fechaFin = now()->format('Y-m-d');

        $this->storeDateCookies($fechaInicio, $fechaFin);

        $users = CaoUsuario::select('cao_usuario.co_usuario', 'no_usuario')
            ->join('permissao_sistema', 'cao_usuario.co_usuario', '=', 'permissao_sistema.co_usuario')
            ->where('permissao_sistema.co_sistema', 1)
            ->where('permissao_sistema.in_ativo', 'S')
            ->whereIn('permissao_sistema.co_tipo_usuario', [0, 1, 2])
            ->get();

        return view('agence', compact('users'));
    }

    public function processForm(FilterRequest $request)
    {
        $fechaInicio = $request->fechaInicio ?? '2005-01-01';
        $fechaFin = $request->fechaFin ?? now()->format('Y-m-d');

        $this->storeDateCookies($fechaInicio, $fechaFin);

        switch ($request->tipo) {
            case 'report':
                return $this->handleReportType($request);
            case 'barchart':
                return $this->handleBarChartType($request);
            case 'pizza':
                return $this->handlePizzaChartType($request);
            default:
                return $this->returnErrorResponse('EL tipo de consulta no pudo ser procesada, porfavor elija un tipo de dato a consultar.');
        }
    }

    /**
     * Almacena las fechas en cookies
     */
    private function storeDateCookies(string $fechaInicio, string $fechaFin): void
    {
        Cookie::queue(Cookie::forever('fecha_inicio', $fechaInicio));
        Cookie::queue(Cookie::forever('Fecha_fin', $fechaFin));
    }

    /**
     * Maneja la lógica para el tipo 'report'
     */
    private function handleReportType(FilterRequest $request)
    {
        $facturas = $this->calculateReportUsers($request->co_usuario, $request->fechaInicio, $request->fechaFin);

        if ($facturas->isEmpty()) {
            return $this->returnErrorResponse('No hay facturas registradas o no hay suficientes datos para hacer los calculos correspondientes.');
        }

        $titulo = $this->generateTitle($request->fechaInicio, $request->fechaFin);
        $datosFormateados = $this->formatReportData($facturas);

        return view('reports.inform', compact('datosFormateados', 'titulo'));
    }

    /**
     * Maneja la lógica para el tipo 'barchart'
     */
    private function handleBarChartType(FilterRequest $request)
    {
        $facturas = $this->calculateBarchartData($request->co_usuario, $request->fechaInicio, $request->fechaFin);

        if ($facturas->isEmpty()) {
            return $this->returnErrorResponse('No hay facturas registradas o no hay suficientes datos para hacer los calculos correspondientes.');
        }

        $tituloGrafica = $this->generateTitle($request->fechaInicio, $request->fechaFin, "Rendimiento desde ");
        $chartData = $this->formatBarChartData($facturas);

        return view('reports.barchart', array_merge($chartData, ['tituloGrafica' => $tituloGrafica]));
    }


    /**
     * Maneja la lógica para el tipo 'pizza'
     */
    private function handlePizzaChartType(FilterRequest $request)
    {
        $facturas = $this->calculatePizzaChart($request->co_usuario, $request->fechaInicio, $request->fechaFin);

        if ($facturas->isEmpty()) {
            return $this->returnErrorResponse('No hay suficientes datos para hacer los calculos correspondientes.');
        }

        $title = $this->generateTitle($request->fechaInicio, $request->fechaFin, "participación en la recepción desde ");
        $format = $this->formatPizzaChartData($facturas);

        return view('reports.pizzachart', compact('title', 'format'));
    }

    /**
     * Formatea los datos para el reporte
     */
    private function formatReportData($facturas): array
    {
        $datosFormateados = [];

        foreach ($facturas as $co_usuario => $data) {
            foreach ($data as $annio => $listAnnios) {
                foreach ($listAnnios as $mes => $listMes) {
                    $key = convertirNumerosAMeses($mes) . '-' . $annio;
                    $datosFormateados[$co_usuario][$key] = [
                        'gananciasNetas' => $listMes->sum('ganancias_neta'),
                        'costo_fijo' => $listMes->first()->costo_fijo,
                        'comision' => $listMes->sum('comision'),
                        'lucro' => $listMes->sum('lucro')
                    ];
                }
            }
        }

        return $datosFormateados;
    }

    /**
     * Formatea los datos para el gráfico de barras
     */
    private function formatBarChartData($facturas): array
    {
        $mesesName = [];
        $mesesNumero = [];
        $co_usuarios = [];
        $custoFixo = [];
        $allMonths = [];

        // Primero recolectamos todos los meses y usuarios únicos
        foreach ($facturas as $annio => $listAnnios) {
            foreach ($listAnnios as $mes => $listMes) {
                $mesKey = (int)$mes;
                $mesTexto = convertirNumerosAMeses($mesKey);
                $allMonths[$mesKey] = $mesTexto . ' ' . $annio;

                foreach ($listMes as $co_usuario => $data) {
                    if (!isset($co_usuarios[$co_usuario])) {
                        $co_usuarios[$co_usuario] = [
                            'type' => 'column',
                            'name' => $co_usuario,
                            'data' => array_fill_keys(array_keys($allMonths), 0)
                        ];
                        $custoFixo[$co_usuario] = $data->first()->costo_fijo;
                    }
                }
            }
        }

        // Ordenar los meses cronológicamente
        ksort($allMonths);
        $mesesName = array_values($allMonths);

        // Llenar los datos reales
        foreach ($facturas as $annio => $listAnnios) {
            foreach ($listAnnios as $mes => $listMes) {
                $mesKey = (int)$mes;
                foreach ($listMes as $co_usuario => $data) {
                    $co_usuarios[$co_usuario]['data'][$mesKey] = $data->sum('ganancias_neta');
                }
            }
        }

        // Ordenar datos para cada usuario según el orden de los meses
        foreach ($co_usuarios as &$usuario) {
            ksort($usuario['data']);
            $usuario['data'] = array_values($usuario['data']);
        }

        // Calcular promedio de custo fixo
        $promedioCustoFix = array_sum($custoFixo) / count($custoFixo);
        $custoFixoMensual = array_fill(0, count($allMonths), $promedioCustoFix);

        // Formatear datos finales para la gráfica
        $formatoGraficaCosUsuario = array_values($co_usuarios);
        $formatoGraficaCosUsuario[] = [
            'type' => 'line',
            'step' => 'center',
            'name' => 'Custo Fixo Medio',
            'data' => $custoFixoMensual
        ];

        return [
            'mesesName' => $mesesName,
            'formatoGraficaCosUsuario' => $formatoGraficaCosUsuario
        ];
    }

    /**
     * Formatea los datos para el gráfico de pizza
     */
    private function formatPizzaChartData($facturas): array
    {
        $receitasFormat = [];
        $receitaTotal = 0;

        foreach ($facturas as $cos_usuario => $datos) {
            $receitasFormat[$cos_usuario] = $datos->sum('ganancias_neta');
            $receitaTotal += $receitasFormat[$cos_usuario];
        }

        return array_map(function ($receita, $cosUsuario) use ($receitaTotal) {
            return [
                'name' => $cosUsuario,
                'y' => ($receita * 100) / $receitaTotal
            ];
        }, $receitasFormat, array_keys($receitasFormat));
    }

    /**
     * Genera el título para los reportes
     */
    private function generateTitle(string $fechaInicio, string $fechaFin, string $prefix = 'Informe desde el '): string
    {
        return $prefix . Carbon::parse($fechaInicio)->format('d/m/Y') . ' hasta ' . Carbon::parse($fechaFin)->format('d/m/Y');
    }

    /**
     * Retorna una respuesta de error
     */
    private function returnErrorResponse(string $message)
    {
        return back()->with([
            'swal' => [
                'title' => 'Error',
                'message' => $message,
                'icon' => 'error'
            ]
        ]);
    }

    public function client(Request $request)
    {
        $usuarios = CaoCliente::select('cao_usuario.co_usuario', 'no_usuario')
            ->get();

        return view('desempenno', compact('usuarios'));
    }

    private function inform(FiltroRequest $request)
    {
        $usuarios = explode(',', $request->co_usuario);
        $fechaInicioDividir = explode('-', $request->fechaInicio);
        $fechaFinDividir = explode('-', $request->fechaFin);

        $mesInicio = $fechaInicioDividir[0];
        $annioInicio = $fechaInicioDividir[1];

        $mesFin = $fechaFinDividir[0];
        $annioFin = $fechaFinDividir[1];

        $facturas = CaoFactura::join('cao_os', 'cao_fatura.co_os', 'cao_os.co_os')
            ->join('cao_salario', 'cao_os.co_usuario', 'cao_salario.co_usuario')
            ->whereIn('cao_os.co_usuario', $usuarios)
            ->where(function ($query) use ($mesInicio, $annioInicio) {
                $query->whereMonth('data_emissao', '>=', $mesInicio)
                    ->whereYear('data_emissao', '>=', $annioInicio);
            })
            ->where(function ($query) use ($mesFin, $annioFin) {
                $query->whereMonth('data_emissao', '<=', $mesFin)
                    ->whereYear('data_emissao', '<=', $annioFin);
            })

            ->select(
                'cao_os.co_usuario',
                DB::raw('MONTH(data_emissao) as mes'),
                DB::raw('YEAR(data_emissao) AS annio'),
                DB::raw('valor - (valor * (total_imp_inc /100)) as ganancias_neta'),
                'brut_salario as costo_fijo',
                DB::raw('(valor - (valor * (total_imp_inc/100))) * (comissao_cn/100) as comision'),
                DB::raw('(valor - (valor * (total_imp_inc/100))) - (brut_salario + ((valor - (valor * (total_imp_inc/100))) * (comissao_cn/100))) as lucro'),
            )
            ->get()
            ->groupBy(['co_usuario', 'annio', 'mes']);
        //validacion en caso que no encuentre registro
        if ($facturas->count() == 0) {
            return back()->with([
                'swal' => [
                    'title' => 'Error',
                    'message' => 'No hay facturas registradas o no hay suficientes datos para hacer los calculos correspondientes. Puede ser q halla q establecerle salario bruto al consultor.',
                    'icon' => 'error'
                ]
            ]);
        }
        $datosFormateados = [];

        foreach ($facturas as $co_usuario => $data) {
            foreach ($data as $annio => $listAnnios) {
                foreach ($listAnnios as $mes => $listMes) {

                    $gananciasNetas = $listMes->sum('ganancias_neta');
                    $costo_fijo = $listMes->first()->costo_fijo; //obtenemos el primero porq es un monto invariable
                    $comision = $listMes->sum('comision');
                    $lucro = $listMes->sum('lucro');
                    $datosFormateados[$co_usuario][$mes . '-' . $annio] = ['gananciasNetas' => $gananciasNetas, 'costo_fijo' => $costo_fijo, 'comision' => $comision, 'lucro' => $lucro];
                }
            }
        }

        return view('informe', compact('datosFormateados'));
    }

    public function chart(FiltroRequest $request)
    {
        $usuarios = explode(',', $request->co_usuario);
        $fechaInicioDividir = explode('-', $request->fechaInicio);
        $fechaFinDividir = explode('-', $request->fechaFin);

        $mesInicio = $fechaInicioDividir[0];
        $annioInicio = $fechaInicioDividir[1];

        $mesFin = $fechaFinDividir[0];
        $annioFin = $fechaFinDividir[1];

        $tituloGrafica = "Performance comercio desde " . convertirNumerosAMeses(intval($mesInicio)) . ' del ' . $annioInicio . ' hasta ' . $mesFin . ' del ' . $annioFin;

        $facturas = CaoFactura::join('cao_os', 'cao_fatura.co_os', 'cao_os.co_os')
            ->join('cao_salario', 'cao_os.co_usuario', 'cao_salario.co_usuario')
            ->whereIn('cao_os.co_usuario', $usuarios)
            ->where(function ($query) use ($mesInicio, $annioInicio) {
                $query->whereMonth('data_emissao', '>=', $mesInicio)
                    ->whereYear('data_emissao', '>=', $annioInicio);
            })
            ->where(function ($query) use ($mesFin, $annioFin) {
                $query->whereMonth('data_emissao', '<=', $mesFin)
                    ->whereYear('data_emissao', '<=', $annioFin);
            })

            ->select(
                'cao_os.co_usuario',
                'data_emissao',
                DB::raw('MONTH(data_emissao) as mes'),
                DB::raw('YEAR(data_emissao) AS annio'),
                DB::raw('valor - (valor * (total_imp_inc /100)) as ganancias_neta'),
                'brut_salario as costo_fijo',
            )
            ->get()
            ->groupBy(['annio', 'mes', 'co_usuario']);

        //validacion en caso que no encuentre registro
        if ($facturas->count() == 0) {
            return back()->with([
                'swal' => [
                    'title' => 'Error',
                    'message' => 'No hay facturas registradas o no hay suficientes datos para hacer los calculos correspondientes. Puede ser q halla q establecerle salario bruto al consultor.',
                    'icon' => 'error'
                ]
            ]);
        }

        $datosFormateados = [];
        $mesesName = [];
        $mesesNumero = [];
        $co_usuarios = [];

        //formatear las fechas barra horizontal y establecer cada co_usuario
        foreach ($facturas as $annio => $listAnnios) {
            foreach ($listAnnios as $mes => $listMes) {
                $mesTexto = convertirNumerosAMeses(intval($mes));
                $key = $mesTexto . ' ' . $annio;
                $mesesName[] = $key;
                $mesesNumero[$mes] = 0;
                foreach ($listMes as $co_usuario => $data) {
                    $co_usuarios[$co_usuario] = ['type' => 'column', 'name' => $co_usuario, 'data' => []];
                }
            }
        }

        //establecer la renta de cada uno de los co usuarios para q todos tengan la misma cantidad de meses
        foreach ($co_usuarios as $key => $co_usuario) {
            $co_usuarios[$key]['data'] = $mesesNumero;
        }

        //establecer el contenido mensual de cada coo usuario y custo fixo
        $custoFixo = [];

        foreach ($facturas as $annio => $listAnnios) {
            foreach ($listAnnios as $mes => $listMes) {
                foreach ($listMes as $co_usuario => $data) {
                    $co_usuarios[$co_usuario]['data'][$mes] = $data->sum('ganancias_neta');
                    $custoFixo[$co_usuario] = $data->first()->costo_fijo;
                }
            }
        }

        //establecemos el custofixo
        $promedioCustoFix = 0;
        $totalCustoFix = 0;

        foreach ($custoFixo as $custo) {
            $totalCustoFix += $custo;
        }

        $promedioCustoFix = $totalCustoFix / count($custoFixo);
        //establecemos el promedio mensual custo fixo
        foreach ($mesesNumero as $mes) {
            $custoFixoMensual[] = $promedioCustoFix;
        }
        //
        $formatoGraficaCosUsuario = [];
        foreach ($co_usuarios as $co_usuario) {
            $data = array_values($co_usuario['data']);
            $co_usuario['data'] = $data;
            $formatoGraficaCosUsuario[] = $co_usuario;
        }

        //agregamos el custo fixo como grafico de linea
        $formatoGraficaCosUsuario[] = ['type' => 'line', 'step' => 'center', 'name' => 'Custo Fixo Medio', 'data' => $custoFixoMensual];

        return view('grafico', compact('mesesName', 'formatoGraficaCosUsuario', 'tituloGrafica'));
    }

    public function pizza(FiltroRequest $request)
    {
        $usuarios = explode(',', $request->co_usuario);
        $fechaInicioDividir = explode('-', $request->fechaInicio);
        $fechaFinDividir = explode('-', $request->fechaFin);

        $mesInicio = $fechaInicioDividir[0];
        $annioInicio = $fechaInicioDividir[1];

        $mesFin = $fechaFinDividir[0];
        $annioFin = $fechaFinDividir[1];

        $tituloGrafica = "participación en la recepción desde " . convertirNumerosAMeses(intval($mesInicio)) . ' del ' . $annioInicio . ' hasta ' . $mesFin . ' del ' . $annioFin;

        $facturas = CaoFactura::join('cao_os', 'cao_fatura.co_os', 'cao_os.co_os')
            ->join('cao_salario', 'cao_os.co_usuario', 'cao_salario.co_usuario')
            ->whereIn('cao_os.co_usuario', $usuarios)
            ->where(function ($query) use ($mesInicio, $annioInicio) {
                $query->whereMonth('data_emissao', '>=', $mesInicio)
                    ->whereYear('data_emissao', '>=', $annioInicio);
            })
            ->where(function ($query) use ($mesFin, $annioFin) {
                $query->whereMonth('data_emissao', '<=', $mesFin)
                    ->whereYear('data_emissao', '<=', $annioFin);
            })

            ->select(
                'cao_os.co_usuario',
                DB::raw('MONTH(data_emissao) as mes'),
                DB::raw('YEAR(data_emissao) AS annio'),
                DB::raw('valor - (valor * (total_imp_inc /100)) as ganancias_neta')
            )
            ->get()
            ->groupBy(['co_usuario']);

        //validacion en caso que no encuentre registro
        if ($facturas->count() == 0) {
            return back()->with([
                'swal' => [
                    'title' => 'Error',
                    'message' => 'No hay facturas registradas o no hay suficientes datos para hacer los calculos correspondientes.',
                    'icon' => 'error'
                ]
            ]);
        }

        $receitasFormat = [];
        $receitaTotal = 0;
        $countCosUsuario = count($facturas);
        foreach ($facturas as $cos_usuario => $datos) {
            $receitasFormat[$cos_usuario] = $datos->sum('ganancias_neta');
            $receitaTotal += $datos->sum('ganancias_neta');
        }

        $pizzaFormat = [];

        foreach ($receitasFormat as $cosUsuario => $receita) {
            $data = ['name' => $cosUsuario, 'y' => ($receita * 100) / $receitaTotal];
            $pizzaFormat[] = $data;
        }

        return view('pizza', compact('tituloGrafica', 'tituloGrafica', 'pizzaFormat'));
    }
}
