<?php

use App\helpers\utils\ExportData;

class export extends Controller
{
    public function excel()
    {
        $export = new ExportData();
        $export->exportExcel();
    }
    public function pdf()
    {
        $export = new ExportData();
        $export->exportPDF();
    }
}
