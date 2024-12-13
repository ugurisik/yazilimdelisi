<?php

use App\helpers\utils\Logger;
use Core\Mysql;

class main extends Controller {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Mysql();
    }

    public function index() {
        $this->view("admin", "tablea", adminAuth:true, params:['page' => 'admin:main']);
    }

    public function list() {
        $draw = $_GET['draw'] ?? 1;
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? TABLE_DATA_COUNT;
        $search = $_GET['search']['value'] ?? null;
        $order = $_GET['order'] ?? [];
        $filters = $_GET['filters'] ?? [];

        $filters[] = [
            'column' => 'status',
            'operator' => '=',
            'value' => '1',
            'type' => 'number'
        ];

        $page = floor($start / $length) + 1;

        $orderColumns = [];
        if (is_array($order)) {
            foreach ($order as $ord) {
                if (isset($ord['column']) && isset($_GET['columns']) && isset($_GET['columns'][$ord['column']]['data'])) {
                    $columnName = $_GET['columns'][$ord['column']]['data'];
                    $orderColumns[] = [
                        'column' => $columnName,
                        'dir' => $ord['dir'] ?? 'asc'
                    ];
                }
            }
        }

        $result = $this->db->getWithPagination2('city', $page, $length, $filters, $search, $orderColumns);

        $response = [
            'draw' => (int)$draw,
            'recordsTotal' => (int)($result['pagination']['total'] ?? 0),
            'recordsFiltered' => (int)($result['pagination']['total'] ?? 0),
            'data' => $result['data'] ?? []
        ];

        $this->jsonResponse($response);
    }

    public function exportExcel() {
        $filters = [];
        if (isset($_GET['filters'])) {
            $filters = json_decode($_GET['filters'], true) ?? [];
        }

        $filters[] = [
            'column' => 'status',
            'operator' => '=',
            'value' => '1',
            'type' => 'number'
        ];

        $result = $this->db->getWithPagination2('city', 1, 999999, $filters, null, []);
        $data = $result['data'] ?? [];

        $headers = [
            'id' => 'ID',
            'cityName' => 'Şehir Adı',
            'countryGuid' => 'Ülke'
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($headers as $key => $header) {
                $sheet->setCellValue($col . $row, $item[$key] ?? '');
                $col++;
            }
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf() {
        $filters = [];
        if (isset($_GET['filters'])) {
            $filters = json_decode($_GET['filters'], true) ?? [];
        }

        $filters[] = [
            'column' => 'status',
            'operator' => '=',
            'value' => '1',
            'type' => 'number'
        ];

        $result = $this->db->getWithPagination2('city', 1, 999999, $filters, null, []);
        $data = $result['data'] ?? [];

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator('YazilimDelisi');
        $pdf->SetTitle('Veri Listesi');
        
        $pdf->SetFont('dejavusans', '', 10);
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->SetMargins(15, 15, 15);
        
        $pdf->AddPage();
        
        $style = '
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 20px;
            }
            th {
                background-color: #f5f5f5;
                font-weight: bold;
                text-align: left;
            }
            td, th {
                border: 1px solid #ddd;
                padding: 8px;
                font-size: 12px;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
        </style>';
        
        $html = $style . '<table>
                <tr>
                    <th>ID</th>
                    <th>Şehir Adı</th>
                    <th>Ülke</th>
                </tr>';
        
        foreach ($data as $row) {
            $html .= '<tr>
                        <td>'.$row['id'].'</td>
                        <td>'.$row['cityName'].'</td>
                        <td>'.$row['countryGuid'].'</td>
                    </tr>';
        }
        
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdf->Output('export.pdf', 'I');
        exit;
    }
}