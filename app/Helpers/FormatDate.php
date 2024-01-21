<?php

namespace App\Helpers;

class FormatDate
{
    public static function month($month)
    {
        // Logic to convert numerical month to its name
        $months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        if (isset($months[$month - 1])) {
            return $months[$month - 1];
        } else {
            return 'Invalid Month';
        }
    }

    public static function day($day)
    {
        // Logic to convert numerical day to its name
        $days = [
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu',
        ];

        if (isset($days[$day - 1])) {
            return $days[$day - 1];
        } else {
            return 'Invalid Day';
        }
    }
}
