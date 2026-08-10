<?php

namespace App\Services;

class KodeHelper
{
    /**
     * Ambil singkatan 3 huruf dari sebuah teks.
     * "Converse" -> "CON", "All Star" -> "AAR", "Hitam" -> "HIT"
     */
    public static function singkat(string $teks, int $panjang = 3): string
    {
        $teks = trim($teks);

        if ($teks === '') {
            return str_pad('', $panjang, 'X');
        }

        $kata = preg_split('/\s+/', $teks);

        if (count($kata) === 1) {
            return strtoupper(str_pad(substr($teks, 0, $panjang), $panjang, 'X'));
        }

        $singkatan = '';
        foreach ($kata as $k) {
            $singkatan .= $k[0];
        }

        if (strlen($singkatan) < $panjang) {
            $singkatan .= substr(end($kata), -1);
        }

        return strtoupper(substr($singkatan, 0, $panjang));
    }
}