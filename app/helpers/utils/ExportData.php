<?php

namespace App\helpers\utils;

class ExportData
{
    public function exportExcel()
    {
        $columns = explode(',', $_GET['columns']);
        $link = $_GET['link'];
        $filters = $_GET['filters'];

        $requestUrl = $link;
        $requestUrl .= (parse_url($link, PHP_URL_QUERY) ? '&' : '?');
        $requestUrl .= 'draw=1&';
        $requestUrl .= 'length=999999&';
        
        $filtersArray = json_decode($filters, true);
        if (is_array($filtersArray)) {
            foreach ($filtersArray as $index => $filter) {
                $requestUrl .= "filters[{$index}][column]=" . urlencode($filter['column']) . "&";
                $requestUrl .= "filters[{$index}][operator]=" . urlencode($filter['operator']) . "&";
                $requestUrl .= "filters[{$index}][value]=" . urlencode($filter['value']) . "&";
                $requestUrl .= "filters[{$index}][text]=" . urlencode($filter['text']) . "&";
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        require 'vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Başlıkları ekle
        $col = 'A';
        foreach ($columns as $column) {
            $colData = explode(';', $column);
            $sheet->setCellValue($col . '1', ucfirst($colData[1]));
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Stil ayarları
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ];

        $sheet->getStyle('A1:' . $col . '1')->applyFromArray($headerStyle);

        // Verileri ekle
        $row = 2;
        foreach ($data['data'] as $rowData) {
            $col = 'A';
            foreach ($columns as $column) {
                $colData = explode(';', $column);
                $value = isset($rowData[$colData[0]]) ? $rowData[$colData[0]] : '';
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Tablo stil ayarları
        $tableStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ];
        
        $sheet->getStyle('A1:' . $col . ($row-1))->applyFromArray($tableStyle);

        // Excel dosyasını indir
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $fName = functions::getInstance()->generateGuid() .'_'.date('Y-m-d_H-i-s');
        header('Content-Disposition: attachment;filename="'.$fName.'.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function exportPDF()
    {
        //{"action":"export\/pdf","filters":"[]","columns":"id,guid,nativeName,commonName,currency,phoneCode,capital,region,status","link":"http:\/\/localhost\/yazilimdelisi\/admin555\/area\/list\/country"}
        $columns = explode(',', $_GET['columns']);
        $link = $_GET['link'];
        $filters = $_GET['filters'];

        $requestUrl = $link;
        $requestUrl .= (parse_url($link, PHP_URL_QUERY) ? '&' : '?');
        $requestUrl .= 'draw=1&';
        $requestUrl .= 'length=999999&';
        
        $filtersArray = json_decode($filters, true);
        if (is_array($filtersArray)) {
            foreach ($filtersArray as $index => $filter) {
                $requestUrl .= "filters[{$index}][column]=" . urlencode($filter['column']) . "&";
                $requestUrl .= "filters[{$index}][operator]=" . urlencode($filter['operator']) . "&";
                $requestUrl .= "filters[{$index}][value]=" . urlencode($filter['value']) . "&";
                $requestUrl .= "filters[{$index}][text]=" . urlencode($filter['text']) . "&";
            }
        }

        Logger::getInstance()->logSaveFile($requestUrl);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false); 
        
        $response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            Logger::getInstance()->logSaveFile('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Logger::getInstance()->logSaveFile('JSON decode error: ' . json_last_error_msg());
            Logger::getInstance()->logSaveFile('Response: ' . $response);
        }

        Logger::getInstance()->logSaveFile(json_encode($data));

        require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Export Data');

        $pdf->AddPage();

        $pdf->SetFont('dejavusans', '', 10);

        $html = '<table border="1" cellpadding="4">';
        $html .= '<tr>';
        foreach ($columns as $column) {
            $col = explode(';', $column);
            $html .= '<th>' . ucfirst($col[1]) . '</th>';
        }
        $html .= '</tr>';

        foreach ($data['data'] as $row) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $col = explode(';', $column);
                $html .= '<td>' . (isset($row[$col[0]]) ? $row[$col[0]] : '') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $random = functions::getInstance()->generateRandomString(10).'_'.time();
        $pdf->Output('pdf_'.$random.'.pdf', 'I');
        exit;
    }
}
