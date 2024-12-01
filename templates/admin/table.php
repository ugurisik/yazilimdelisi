<?php
use App\helpers\utils\DataTable;

$table = (new DataTable('cityTable'))
    ->setUrl(SITE_URL . '/admin555/list')
    ->setExportUrls(SITE_URL . '/admin555/exportExcel', SITE_URL . '/admin555/exportPdf')
    ->addColumn('id', 'ID', null, 75, 'number', true, 'text-center')
    ->addColumn('guid', 'GUID', null, null, 'text', false)
    ->addColumn('cityName', 'Şehir Adı', null, null, 'text')
    ->addColumn('countryGuid', 'Ülke', null, null, 'text')
    ->addColumn('status', 'Durum', 'function(data) { if(data == 1) { return "Aktif"; } else if(data == 2) { return "Pasif" } else if(data == 3) { return "Silinmiş" } else { return "-" }   }', null, "checkbox")
    ->setOrder('cityName', 'ASC')
    ->addButton('Sil', 'fas fa-trash', 'handleDeleteItems', 'btn-danger')
    ->addButton('Düzenle', 'fas fa-edit', 'handleEditItem', 'btn-warning')
    ->setPageLength(TABLE_DATA_COUNT);

echo $table->render();
?>
<script>
function handleDeleteItems(selected) {
    if (confirm('Seçili kayıtları silmek istediğinize emin misiniz?')) {
        $.post('<?= SITE_URL ?>/admin555/delete', {
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
    window.location.href = `<?= SITE_URL ?>/admin555/edit/${selected[0].guid}`;
}
</script>