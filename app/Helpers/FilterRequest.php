<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class FilterRequest
{

    /**
     * filter tanggal laporan penjualan
     */
    public static function filterDate(Request $request)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;
        $daterange = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        return [
            'typeReport' => $typeReport,
            'date' => $date,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'daterange' => $daterange,
        ];
    }
}
