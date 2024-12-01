<div class="card card-flush h-xl-100">
    <div class="card-header pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">İlçeler</span>
        </h3>
    </div>
    <div class="card-body">
        <?php

        use App\helpers\utils\DataTable;

        $table = (new DataTable('zoneTable'))
            ->setUrl(SITE_URL . '/admin555/area/list/zone')
            ->setExportUrls(SITE_URL . '/' . ADMIN_URI . '/exportExcel', SITE_URL . '/admin555/exportPdf')
            ->addColumn(field: 'id', title: 'ID', width: 75, type: 'number', css: 'text-center')
            ->addColumn(field: 'guid', title: 'GUID', type: 'text', visible: false)
            ->addColumn(field: 'zoneName', title: 'İlçe Adı', type: 'text')
            ->addColumn(field: 'countryGuid', title: 'Ülkesi', type: 'text')
            ->addColumn(field: 'cityGuid', title: 'Bulunduğu Şehir', type: 'text')
            ->addColumn(field: 'status', title: 'Durum', type: "checkbox", render: 'function(data) { if(data == 1) { return "Aktif"; } else if(data == 2) { return "Pasif" } else if(data == 3) { return "Silinmiş" } else { return "-" }   }')
            ->setOrder('cityName', 'ASC')
            ->addButton('Yeni Ekle', 'fas fa-add', 'handleEditItem', 'btn-info')
            ->addButton('Düzenle', 'fas fa-edit', 'handleEditItem', 'btn-warning')
            ->addButton('Sil', 'fas fa-trash', 'handleDeleteItems', 'btn-danger')

            ->setPageLength(TABLE_DATA_COUNT);

        echo $table->render();
        ?>
    </div>
</div>


<script>
    function handleDeleteItems(selected) {
     
        if (confirm('Seçili kayıtları silmek istediğinize emin misiniz?')) {
            $.post('<?= SITE_URL ?>/<?= ADMIN_URI ?>/delete', {
                items: selected.map(item => item.guid)
            }).done(function(response) {
                if (response.status === 'success') {
                    table_cityTable.ajax.reload();
                }
            });
        }
    }

    function handleEditItem(selected) {
        if (selected.length !== 1) {
            alert('Lütfen düzenlemek için bir kayıt seçiniz');
            return;
        }
        window.location.href = `<?= SITE_URL ?>/<?= ADMIN_URI ?>/edit/${selected[0].guid}`;
    }
</script>