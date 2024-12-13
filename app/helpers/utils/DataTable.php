<?php

namespace App\helpers\utils;

class DataTable
{
    private $id;
    private $url;
    private $columns = [];
    private $orderColumns = [];
    private $buttons = [];
    private $pageLength = 10;
    private $exportExcelUrl;
    private $exportPdfUrl;

    private $operators = [
        'text' => [
            '=' => 'Eşittir',
            '<>' => 'Eşit Değildir',
            'LIKE' => 'İçerir',
            'NOT LIKE' => 'İçermez',
            'START' => 'İle Başlar',
            'END' => 'İle Biter'
        ],
        'number' => [
            '=' => 'Eşittir',
            '<>' => 'Eşit Değildir',
            '>' => 'Büyüktür',
            '<' => 'Küçüktür',
            '>=' => 'Büyük Eşittir',
            '<=' => 'Küçük Eşittir'
        ],
        'date' => [
            '=' => 'Eşittir',
            '<>' => 'Eşit Değildir',
            '>' => 'Sonra',
            '<' => 'Önce',
            'BETWEEN' => 'Arasında'
        ],
        'checkbox' => [
            '=' => 'Eşittir',
            '<>' => 'Eşit Değildir'
        ]
    ];

    public function __construct($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setExportUrls($excelUrl, $pdfUrl)
    {
        $this->exportExcelUrl = $excelUrl;
        $this->exportPdfUrl = $pdfUrl;
        return $this;
    }

    public function addColumn($field, $title, $render = null, $width = null, $type = 'text', $visible = true, $css = '')
    {
        $this->columns[] = [
            'field' => $field,
            'title' => $title,
            'render' => $render,
            'width' => $width,
            'type' => $type,
            'visible' => $visible,
            'css' => $css
        ];
        return $this;
    }

    public function setOrder($column, $direction = 'ASC')
    {
        $this->orderColumns[$column] = $direction;
        return $this;
    }

    public function addButton($text, $icon, $onClick, $className = 'btn-primary')
    {
        $this->buttons[] = [
            'text' => $text,
            'icon' => $icon,
            'onClick' => $onClick,
            'className' => "p-1 px-3 " . $className
        ];
        return $this;
    }

    public function setPageLength($length)
    {
        $this->pageLength = $length;
        return $this;
    }

    public function render()
    {
        $html = $this->generateHtml();
        $js = $this->generateJavaScript();
        return $html . $js;
    }

    private function generateHtml()
    {
        $html = '<div class="table-container">';

        $html .= $this->generateFilterModal();

        $html .= '<div class="mb-3 d-flex justify-content-between"><div class="d-flex">';


        foreach ($this->buttons as $button) {
            $html .= sprintf(
                '<button class="btn %s me-2" onclick="handleButtonClick(\'%s\')"><i class="%s"></i> %s</button>',
                $button['className'],
                $button['onClick'],
                $button['icon'],
                $button['text']
            );
        }
        $html .= '</div><div class="d-flex">';

        if ($this->exportExcelUrl) {
            $html .= '<button type="button" class="btn btn-success me-2 p-1 px-3" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>';
        }

        if ($this->exportPdfUrl) {
            $html .= '<button type="button" class="btn btn-danger me-2 p-1 px-3" onclick="exportToPdf()"><i class="fas fa-file-pdf"></i> PDF</button>';
        }

        $html .= '<button type="button" class="btn btn-primary me-2 p-1 px-3" onclick="showFilterModal()"><i class="fas fa-filter"></i> Filtrele</button><button type="button" class="btn btn-primary me-2 p-1 px-3" onclick="refreshData()"><i class="bi bi-arrow-clockwise p-0"></i></button></div></div>';

        $html .= '<div id="activeFilters" class="mb-3"></div>';

        $html .= sprintf('<table id="%s" class="table table-striped">', $this->id);
        $html .= '<thead><tr>';
        foreach ($this->columns as $column) {
            $html .= sprintf('<th class="%s">%s</th>', $column['css'], $column['title']);
        }
        $html .= '</tr></thead></table></div>';

        return $html;
    }

    private function generateFilterModal()
    {
        $html = '
        <div class="modal fade" id="filterModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filtrele</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sütun Seç</label>
                            <select class="form-select" id="filterColumn" onchange="updateOperators()">
                                <option value="">Seçiniz</option>';
        foreach ($this->columns as $column) {
            if (!$column['visible']) {
                continue;
            }
            $html .= sprintf(
                '<option value="%s" data-type="%s">%s</option>',
                $column['field'],
                $column['type'],
                $column['title']
            );
        }
        $html .= '
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">İşlem</label>
                            <select class="form-select" id="filterOperator"></select>
                        </div>
                        <div class="mb-3" id="filterValueContainer">
                            <label class="form-label">Değer</label>
                            <input type="text" class="form-control" id="filterValue">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" onclick="addFilter()">Filtre Ekle</button>
                        <button type="button" class="btn btn-success" onclick="applyFilters()">Uygula</button>
                    </div>
                </div>
            </div>
        </div>';
        return $html;
    }

    private function generateJavaScript()
    {
        $js = '<script>';

        $js .= sprintf('var table_%s;', $this->id);

        $js .= 'var activeFilters = [];';
        $js .= sprintf('var operators = %s;', json_encode($this->operators));

        $js .= 'document.addEventListener("DOMContentLoaded", function() {';
        $js .= sprintf('table_%s = $("#%s").DataTable({', $this->id, $this->id);
        $js .= 'processing: true,';
        $js .= 'serverSide: true,';
        $js .= sprintf('pageLength: %d,', $this->pageLength);
        $js .= 'select: true,';
        $js .= 'language: {
            "decimal":        "",
            "emptyTable":    "Tabloda veri bulunmamaktadır",
            "info":          "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
            "infoEmpty":     "Kayıt yok",
            "infoFiltered":  "(_MAX_ kayıt içerisinden bulunan)",
            "infoPostFix":   "",
            "thousands":     ",",
            "lengthMenu":    "_MENU_ kayıt göster",
            "loadingRecords": "Yükleniyor...",
            "processing":    "İşleniyor...",
            "search":        "Ara:",
            "zeroRecords":   "Eşleşen kayıt bulunamadı",
            "paginate": {
                "first":     "İlk",
                "last":      "Son",
                "next":      "Sonraki",
                "previous":  "Önceki"
            }
        },';

        $js .= sprintf('ajax: {
        url: "%s",
        type: "GET",
        data: function(d) {
            d.filters = activeFilters;
            return d;
        }
    },', $this->url);

        $cols = [];
        foreach ($this->columns as $column) {
            if (!$column['visible']) {
                continue;
            }
            $cols[] = $column['field'].";". $column['title'];
        }

        $js .= 'columns: [';
        foreach ($this->columns as $column) {
            $js .= '{';
            $js .= sprintf('data: "%s",', $column['field']);
            if ($column['render']) {
                $js .= sprintf('render: %s,', $column['render']);
            }
            if ($column['width']) {
                $js .= sprintf('width: "%s",', $column['width']);
            }
            $js .= sprintf('visible: %s,', $column['visible'] ? 'true' : 'false');
            $js .= sprintf('className: "%s",', $column['css']);
            $js .= '},';
        }
        $js .= ']';
        $js .= '});';
        $js .= '});';

        $js .= '
        function showFilterModal() {
            $("#filterModal").modal("show");
        }

        function updateOperators() {
            var column = $("#filterColumn option:selected");
            var type = column.data("type");
            var ops = operators[type];
            
            var html = "<option value=\"\">Seçiniz</option>";
            for (var op in ops) {
                html += "<option value=\"" + op + "\">" + ops[op] + "</option>";
            }
            
            $("#filterOperator").html(html);
            updateValueInput(type);
        }

        function updateValueInput(type) {
            var input = "";
            switch(type) {
                case "date":
                    input = "<input type=\"date\" class=\"form-control\" id=\"filterValue\">";
                    break;
                case "number":
                    input = "<input type=\"number\" class=\"form-control\" id=\"filterValue\">";
                    break;
                case "checkbox":
                    input = `<select class="form-select" id="filterValue">
                        <option value="1">Aktif</option>
                        <option value="2">Pasif</option>
                        <option value="3">Silinmiş</option>
                    </select>`;
                    break;
                default:
                    input = "<input type=\"text\" class=\"form-control\" id=\"filterValue\">";
            }
            $("#filterValueContainer").html("<label class=\"form-label\">Değer</label>" + input);
        }

        function updateActiveFilters(shouldReload = false) {
            var html = "";
            activeFilters.forEach(function(filter, index) {
                html += `<span class="badge bg-info me-2">
                    ${filter.text}
                    <i class="fas fa-times ms-1" onclick="removeFilter(${index})" style="cursor:pointer;"></i>
                </span>`;
            });
            $("#activeFilters").html(html);
            
            if (shouldReload) {
                table_' . $this->id . '.ajax.reload();
            }
        }

        function addFilter() {
            var column = $("#filterColumn").val();
            var columnText = $("#filterColumn option:selected").text();
            var operator = $("#filterOperator").val();
            var operatorText = $("#filterOperator option:selected").text();
            var value = $("#filterValue").val();
            
            if (!column || !operator || !value) {
                alert("Lütfen tüm alanları doldurunuz");
                return;
            }
            
            activeFilters.push({
                column: column,
                operator: operator,
                value: value,
                text: columnText + " " + operatorText + " " + value
            });
            
            updateActiveFilters(false);
        }

        function removeFilter(index) {
            activeFilters.splice(index, 1);
            updateActiveFilters(true);
        }

        function clearFilters() {
            activeFilters = [];
            updateActiveFilters(true);
        }

        function applyFilters() {
            table_' . $this->id . '.ajax.reload();
            $("#filterModal").modal("hide");
        }

        function refreshData() {
            table_' . $this->id . '.ajax.reload();
        }

        function exportToExcel() {
            let url = new URL("' . SITE_URL . '/export/excel");
            url.searchParams.append("filters", JSON.stringify(activeFilters));
            url.searchParams.append("columns", ' . json_encode($cols) . ');
            url.searchParams.append("link", "' . $this->exportExcelUrl . '");
            window.location.href = url.toString();
        }

        function exportToPdf() {
            let url = new URL("' . SITE_URL . '/export/pdf");
            url.searchParams.append("filters", JSON.stringify(activeFilters));
            url.searchParams.append("columns", ' . json_encode($cols) . ');
            url.searchParams.append("link", "' . $this->exportPdfUrl . '");
            window.open(url.toString(), "_blank");
        }

        function handleButtonClick(functionName) {
            var selected = table_' . $this->id . '.rows({selected: true}).data().toArray();
            if (typeof window[functionName] === "function") {
                window[functionName](selected);
            }
        }';

        $js .= '</script>';
        return $js;
    }
}
