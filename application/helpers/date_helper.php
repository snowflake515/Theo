<?php

if (!function_exists('get_format_one')) {

    function get_format_date_ddmmyy($date = NULL) {
        $tgl = $date;
        $tanggal = strtotime($tgl);
        $Jam = true;
        $bln_array = array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        );

        $tggl = date('j', $tanggal);
        $bln = @$bln_array[date('m', $tanggal)];
        $thn = date('Y', $tanggal);
        return "$tggl $bln $thn";
    }

}

