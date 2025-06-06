<?php

namespace App\Traits;

use App\Models\CaoFactura;
use Illuminate\Support\Facades\DB;

trait CalculateTrait
{
    public function calculateReportUsers(array $usuarios, string $fechaInicio, string $fechaFin)
    {
        $query = CaoFactura::select(
            'cao_os.co_usuario',
            DB::raw('MONTH(data_emissao) as mes'),
            DB::raw('YEAR(data_emissao) AS annio'),
            DB::raw('valor - (valor * (total_imp_inc /100)) as ganancias_neta'),
            'brut_salario as costo_fijo',
            DB::raw('(valor - (valor * (total_imp_inc/100))) * (comissao_cn/100) as comision')
        )
            ->join('cao_os', 'cao_fatura.co_os', '=', 'cao_os.co_os')
            ->join('cao_salario', 'cao_os.co_usuario', '=', 'cao_salario.co_usuario')
            ->whereIn('cao_os.co_usuario', $usuarios)
            ->whereBetween('data_emissao', [$fechaInicio, $fechaFin]);

        return $query->get()
            ->groupBy(['co_usuario', 'annio', 'mes']);
    }

    public function calculateBarchartData(array $usuarios, string $fechaInicio, string $fechaFin)
    {
        return CaoFactura::select(
            'cao_os.co_usuario',
            'data_emissao',
            DB::raw('MONTH(data_emissao) as mes'),
            DB::raw('YEAR(data_emissao) AS annio'),
            DB::raw('valor - (valor * (total_imp_inc / 100)) as ganancias_neta'),
            'brut_salario as costo_fijo'
        )
            ->join('cao_os', 'cao_fatura.co_os', '=', 'cao_os.co_os')
            ->join('cao_salario', 'cao_os.co_usuario', '=', 'cao_salario.co_usuario')
            ->whereIn('cao_os.co_usuario', $usuarios)
            ->whereBetween('data_emissao', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy(['annio', 'mes', 'co_usuario']);
    }

    public function calculatePizzaChart(array $usuarios, string $fechaInicio, string $fechaFin)
    {
        return CaoFactura::select(
            'cao_os.co_usuario',
            DB::raw('MONTH(data_emissao) as mes'),
            DB::raw('YEAR(data_emissao) AS annio'),
            DB::raw('SUM(valor - (valor * (total_imp_inc / 100))) as ganancias_neta')
        )
            ->join('cao_os', 'cao_fatura.co_os', '=', 'cao_os.co_os')
            ->join('cao_salario', 'cao_os.co_usuario', '=', 'cao_salario.co_usuario')
            ->whereIn('cao_os.co_usuario', $usuarios)
            ->whereBetween('data_emissao', [$fechaInicio, $fechaFin])
            ->groupBy('cao_os.co_usuario', 'mes', 'annio')
            ->get()
            ->groupBy('co_usuario');
    }
}
