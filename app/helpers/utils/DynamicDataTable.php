<?php

namespace App\helpers\utils;

use Core\Record;
use Core\ObjectCore;
use Helpers\Field;
use Enums\DisplayType;

class DynamicDataTable
{
    private $record;
    private $listUrl;
    private $editUrl;
    private $id;
    private $columns = [];
    private $buttons = [];
    private $pageLength = 10;
    private $exportExcelUrl;
    private $exportPdfUrl;
    private $html = '';

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
        ],
        'combobox' => [
            '=' => 'Eşittir',
            '<>' => 'Eşit Değildir'
        ]
    ];

    public function __construct(Record $record, string $listUrl)
    {
        $this->record = $record;
        $this->listUrl = $listUrl . '/list/' . $record->getField()->tableName;
        $this->editUrl = $listUrl . '/edit/' . $record->getField()->tableName;
        $this->id = 'table_' . $record->getField()->tableName . '_table';
        $this->initializeColumns();
    }

    private function initializeColumns()
    {
        $fields = $this->record->getField()->fields->fields;
        foreach ($fields as $field) {
            if ($field->isColumn) {
                $column = [
                    'name' => $field->name,
                    'label' => $field->label ?? $field->name,
                    'searchable' => $field->searchable,
                    'sortable' => $field->sortable,
                    'displayType' => $field->displayType,
                    'comboAdapter' => $field->comboAdapter,
                    'isEditableColumn' => $field->isEditableColumn ?? false,
                    'field' => $field
                ];
                $this->columns[] = $column;
            }
        }
    }

    private function getOperators($displayType)
    {
        switch ($displayType) {
            case DisplayType::TEXT:
                return $this->operators['text'];
            case DisplayType::NUMBER:
                return $this->operators['number'];
            case DisplayType::DATE:
                return $this->operators['date'];
            case DisplayType::CHECKBOX:
                return $this->operators['checkbox'];
            case DisplayType::COMBOBOX:
                return $this->operators['combobox'];
            default:
                return $this->operators['text'];
        }
    }

    public function addButton($name, $label, $class = 'primary', $icon = null)
    {
        $this->buttons[] = [
            'name' => $name,
            'label' => $label,
            'class' => $class,
            'icon' => $icon
        ];
        return $this;
    }

    public function setPageLength($length)
    {
        $this->pageLength = $length;
        return $this;
    }

    public function enableExport($excelUrl = null, $pdfUrl = null)
    {
        $this->exportExcelUrl = $excelUrl ?? $this->listUrl . '/excel';
        $this->exportPdfUrl = $pdfUrl ?? $this->listUrl . '/pdf';
        return $this;
    }

    public function render()
    {
        $this->html = '<div class="card">';
        $this->html .= '<div class="card-body">';

        // Buttons and Filters
        $this->html .= '<div class="mb-3 d-flex justify-content-between">';
        $this->html .= '<div class="d-flex gap-2">';
        foreach ($this->buttons as $button) {
            $icon = $button['icon'] ? "<i class='{$button['icon']}'></i> " : '';
            $this->html .= "<button type='button' class='btn btn-{$button['class']} p-1 px-3' onclick='handleButtonClick(\"{$button['name']}\")'>{$icon}{$button['label']}</button>";
        }
        $this->html .= '</div>';

        $this->html .= '<div class="d-flex gap-2">';
        $this->html .= '<button type="button" class="btn btn-primary p-1 px-3" onclick="showFilterModal()"><i class="fas fa-filter"></i> Filtrele</button>';
        if ($this->exportExcelUrl) {
            $this->html .= '<button type="button" class="btn btn-success p-1 px-3" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>';
        }
        if ($this->exportPdfUrl) {
            $this->html .= '<button type="button" class="btn btn-danger p-1 px-3" onclick="exportToPdf()"><i class="fas fa-file-pdf"></i> PDF</button>';
        }
        $this->html .= '<button type="button" class="btn btn-info p-1 px-3" onclick="refreshData()"><i class="fas fa-sync-alt"></i></button>';
        $this->html .= '</div>';
        $this->html .= '</div>';

        // Active Filters
        $this->html .= '<div id="activeFilters" class="mb-3"></div>';

        // Table
        $this->html .= "<table id='{$this->id}' class='table table-bordered table-striped'>";
        $this->html .= '<thead><tr>';
        foreach ($this->columns as $column) {
            $this->html .= "<th>{$column['label']}</th>";
        }
        $this->html .= '</tr></thead>';
        $this->html .= '</table>';

        $this->html .= '</div></div>';

        // Filter Modal
        $this->html .= $this->generateFilterModal();

        // JavaScript
        $this->html .= $this->renderJavaScript();

        return $this->html;
    }

    private function generateFilterModal()
    {
        $html = '<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filtrele</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alan</label>
                            <select class="form-select" id="filterColumn" onchange="updateOperators()">
                                <option value="">Seçiniz</option>';

        foreach ($this->columns as $column) {
            if ($column['searchable']) {
                $html .= sprintf(
                    '<option value="%s" data-type="%s">%s</option>',
                    $column['name'],
                    $this->getDataType($column['displayType']),
                    $column['label']
                );
            }
        }

        $html .= '</select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Operatör</label>
                            <select class="form-select" id="filterOperator">
                                <option value="">Seçiniz</option>
                            </select>
                        </div>
                        <div class="mb-3" id="filterValueContainer">
                            <label class="form-label">Değer</label>
                            <input type="text" class="form-control" id="filterValue">
                        </div>
                        <div id="modalActiveFilters"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">Filtreleri Temizle</button>
                        <button type="button" class="btn btn-primary" onclick="addFilter()">Filtre Ekle</button>
                        <button type="button" class="btn btn-success" onclick="applyFilters()">Uygula</button>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    private function getDataType($displayType)
    {
        switch ($displayType) {
            case DisplayType::NUMBER:
                return 'number';
            case DisplayType::DATE:
                return 'date';
            case DisplayType::CHECKBOX:
                return 'checkbox';
            case DisplayType::COMBOBOX:
                return 'combobox';
            default:
                return 'text';
        }
    }

    private function renderJavaScript()
    {
        $cols = [];
        foreach ($this->columns as $column) {
            $cols[] = $column['name'] . ";" . $column['label'];
        }

        $config = [
            'processing' => true,
            'serverSide' => true,
            'select' => true,
            'language' => [
                'decimal' => '',
                'emptyTable' => 'Tabloda herhangi bir veri mevcut değil',
                'info' => '_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor',
                'infoEmpty' => 'Kayıt yok',
                'infoFiltered' => '(_MAX_ kayıt içerisinden bulunan)',
                'infoPostFix' => '',
                'thousands' => ',',
                'lengthMenu' => 'Sayfada _MENU_ kayıt göster',
                'loadingRecords' => 'Yükleniyor...',
                'processing' => 'İşleniyor...',
                'search' => 'Ara:',
                'zeroRecords' => 'Eşleşen kayıt bulunamadı',
                'paginate' => [
                    'first' => 'İlk',
                    'last' => 'Son',
                    'next' => 'Sonraki',
                    'previous' => 'Önceki'
                ]
            ],
            'columns' => array_map(function ($column) {
                return [
                    'data' => $column['name'],
                    'name' => $column['name'],
                    'orderable' => $column['sortable'],
                    'searchable' => $column['searchable']
                ];
            }, $this->columns)
        ];

        $editableColumns = [];
        foreach ($this->columns as $index => $column) {
            if ($column['field']->isEditableColumn) {
                $editableColumns[] = $index;
            }
        }

        $js = "<script>
            var activeFilters = [];
            var operators = " . json_encode($this->operators) . ";
            var {$this->id};
            var editableColumns = " . json_encode($editableColumns) . ";
            
            // Combobox değerlerini saklayacak obje
            var comboValues = " . $this->getComboboxValues() . ";

            // Editable cell stilleri
            var style = document.createElement('style');
            style.textContent = `
                .editable-cell {
                    position: relative;
                    cursor: pointer;
                    transition: all 0.2s;
                    padding: 5px !important;
                }
                
                .editable-cell:hover {
                    background-color: #f8f9fa;
                }
                
                .editable-cell:focus {
                    outline: 2px solid #0d6efd;
                    background-color: #fff;
                    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
                }
                
                .editable-cell::after {
                    content: '✎';
                    position: absolute;
                    right: 5px;
                    top: 50%;
                    transform: translateY(-50%);
                    opacity: 0;
                    color: #6c757d;
                    transition: opacity 0.2s;
                }
                
                .editable-cell:hover::after {
                    opacity: 1;
                }
                
                .editable-cell select {
                    width: 100%;
                    padding: 4px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                    background-color: #fff;
                    cursor: pointer;
                }
                
                .editable-cell select:focus {
                    border-color: #86b7fe;
                    outline: 0;
                    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
                }
            `;
            document.head.appendChild(style);

            document.addEventListener('DOMContentLoaded', function() {
                {$this->id} = $('#{$this->id}').DataTable($.extend(" . json_encode($config) . ", {
                    ajax: {
                        url: '" . $this->listUrl . "',
                        type: 'GET',
                        data: function(d) {
                            if (activeFilters && activeFilters.length > 0) {
                                d.filters = activeFilters;
                            }
                            return d;
                        }
                    },
                    columnDefs: [
                        {
                            targets: " . $this->getComboboxColumnIndexes() . ",
                            render: function(data, type, row, meta) {
                                var column = " . json_encode($this->columns) . "[meta.col];
                                if (column.comboAdapter && typeof comboValues !== 'undefined' && comboValues[column.name]) {
                                    return comboValues[column.name][data] || data;
                                }
                                return data;
                            }
                        },
                        {
                            targets: editableColumns,
                            createdCell: function (td, cellData, rowData, row, col) {
                                var column = " . json_encode($this->columns) . "[col];
                                $(td).attr('contenteditable', 'true');
                                $(td).addClass('editable-cell');
                                
                                if (column.comboAdapter && comboValues[column.name]) {
                                    $(td).attr('contenteditable', 'false');
                                    var select = $('<select class=\"form-select form-select-sm\"></select>');
                                    for (var value in comboValues[column.name]) {
                                        select.append($('<option></option>')
                                            .attr('value', value)
                                            .text(comboValues[column.name][value])
                                            .prop('selected', value == cellData)
                                        );
                                    }
                                    $(td).html(select);
                                    
                                    select.on('change', function() {
                                        var newValue = $(this).val();
                                        saveEdit(rowData.guid, column.name, newValue);
                                    });
                                }
                            }
                        }
                    ]
                }));

                // Düzenlenebilir hücrelere event listener ekle
                $('#{$this->id}').on('focus', 'td.editable-cell[contenteditable=true]', function() {
                    var originalValue = $(this).text();
                    $(this).data('originalValue', originalValue);
                }).on('blur', 'td.editable-cell[contenteditable=true]', function() {
                    var cell = {$this->id}.cell(this);
                    var rowData = {$this->id}.row($(this).parent()).data();
                    var colIndex = cell.index().column;
                    var column = " . json_encode($this->columns) . "[colIndex];
                    var newValue = $(this).text();
                    var originalValue = $(this).data('originalValue');
                    
                    if (editableColumns.includes(colIndex) && newValue !== originalValue) {
                        saveEdit(rowData.guid, column.name, newValue);
                    }
                }).on('keydown', 'td.editable-cell[contenteditable=true]', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $(this).blur();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        $(this).text($(this).data('originalValue'));
                        $(this).blur();
                    }
                });
            });

            function saveEdit(guid, field, value) {
                $.ajax({
                    url: '" . $this->editUrl . "',
                    type: 'POST',
                    data: {
                        guid: guid,
                        field: field,
                        value: value,
                        csrf_token: '" . \App\helpers\utils\Security::getCSRF() . "'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success('Değişiklikler kaydedildi');
                            {$this->id}.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message || 'Bir hata oluştu');
                        }
                    },
                    error: function() {
                        toastr.error('Bir hata oluştu');
                    }
                });
            }

            function showFilterModal() {
                $('#filterModal').modal('show');
                updateOperators();
            }

            function updateOperators() {
                var column = $('#filterColumn option:selected');
                var type = column.data('type');
                var columnName = column.val();
                var ops = operators[type];
                
                var html = '<option value=\"\">Seçiniz</option>';
                for (var op in ops) {
                    html += '<option value=\"' + op + '\">' + ops[op] + '</option>';
                }
                
                $('#filterOperator').html(html);
                updateValueInput(type, columnName);
            }

            function updateValueInput(type, columnName) {
                var input = '';
                switch(type) {
                    case 'date':
                        input = '<input type=\"date\" class=\"form-control\" id=\"filterValue\">';
                        break;
                    case 'number':
                        input = '<input type=\"number\" class=\"form-control\" id=\"filterValue\">';
                        break;
                    case 'combobox':
                        if (comboValues[columnName]) {
                            input = '<select class=\"form-select\" id=\"filterValue\">';
                            for (var value in comboValues[columnName]) {
                                input += '<option value=\"' + value + '\">' + comboValues[columnName][value] + '</option>';
                            }
                            input += '</select>';
                        } else {
                            input = '<input type=\"text\" class=\"form-control\" id=\"filterValue\">';
                        }
                        break;
                    default:
                        input = '<input type=\"text\" class=\"form-control\" id=\"filterValue\">';
                }
                $('#filterValueContainer').html('<label class=\"form-label\">Değer</label>' + input);
            }

            function updateActiveFilters(shouldReload = false) {
                var html = '';
                activeFilters.forEach(function(filter, index) {
                    html += '<span class=\"badge bg-info me-2\">' +
                        filter.text +
                        '<i class=\"fas fa-times ms-1\" onclick=\"removeFilter(' + index + ')\" style=\"cursor:pointer;\"></i>' +
                    '</span>';
                });
                $('#activeFilters').html(html);
                $('#modalActiveFilters').html(html);
                
                if (shouldReload) {
                    {$this->id}.ajax.reload();
                }
            }

            function addFilter() {
                var column = $('#filterColumn').val();
                var columnText = $('#filterColumn option:selected').text();
                var operator = $('#filterOperator').val();
                var operatorText = $('#filterOperator option:selected').text();
                var value = $('#filterValue').val();
                
                if (!column || !operator || !value) {
                    alert('Lütfen tüm alanları doldurunuz');
                    return;
                }
                
                activeFilters.push({
                    column: column,
                    operator: operator,
                    value: value,
                    text: columnText + ' ' + operatorText + ' ' + value
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
                {$this->id}.ajax.reload();
                $('#filterModal').modal('hide');
            }

            function refreshData() {
                {$this->id}.ajax.reload();
            }

            function exportToExcel() {
                let url = new URL('" . SITE_URL . "/export/excel');
                url.searchParams.append('filters', JSON.stringify(activeFilters));
                url.searchParams.append('columns', '" . implode(',', $cols) . "');
                url.searchParams.append('link', '" . ($this->exportExcelUrl ?? '') . "');
                window.location.href = url.toString();
            }

            function exportToPdf() {
                let url = new URL('" . SITE_URL . "/export/pdf');
                url.searchParams.append('filters', JSON.stringify(activeFilters));
                url.searchParams.append('columns', '" . implode(',', $cols) . "');
                url.searchParams.append('link', '" . ($this->exportPdfUrl ?? '') . "');
                window.open(url.toString(), '_blank');
            }

            function handleButtonClick(functionName) {
                var selected = {$this->id}.rows({selected: true}).data().toArray();
                if (typeof window[functionName] === 'function') {
                    window[functionName](selected);
                }
            }
        </script>";

        return $js;
    }

    private function getComboboxValues()
    {
        $values = [];
        foreach ($this->columns as $column) {
            if ($column['comboAdapter'] && $column['comboAdapter'] instanceof \Helpers\SimpleComboAdapter) {
                $values[$column['name']] = [];
                foreach ($column['comboAdapter']->getPairs() as $pair) {
                    $values[$column['name']][$pair[0]] = $pair[1];
                }
            }
        }
        return json_encode($values);
    }

    private function getComboboxColumnIndexes()
    {
        $indexes = [];
        foreach ($this->columns as $index => $column) {
            if ($column['comboAdapter']) {
                $indexes[] = $index;
            }
        }
        return implode(',', $indexes);
    }
}
