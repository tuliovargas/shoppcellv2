<?php

namespace App\Modules;

use Dompdf\Dompdf;

use PDF;

class ThermalPrinter
{

    public static function print($view, $data)
    {
        $domPdf = new Dompdf();
        $domPdf->setPaper(
            array(0, 0, 220, 150)
        );
        $compiled = view($view, $data); // Compila a view com os dados para carregar no domPdf
        $domPdf->loadHtml($compiled);
        $domPdf->render();


        $pageCount = $domPdf->getCanvas()->get_page_count();
        unset($domPdf);

        $height = 150 * $pageCount;

        PDF::setOptions([
            'dpi' => 96,
            'font' => 'Arial'
        ]);

        $pdf = PDF::loadView($view, $data)->setPaper(
            array(0, 0, 220, $height)
        );

        return $pdf->stream();
    }
}
