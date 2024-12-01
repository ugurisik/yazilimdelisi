<?php
use App\helpers\utils\DataTable;

$table = (new DataTable('cityTable'))
    ->setUrl(SITE_URL . '/admin555/area/list/city')
    ->setExportUrls(SITE_URL . '/'.ADMIN_URI.'/exportExcel', SITE_URL . '/admin555/exportPdf')
    ->addColumn(field:'id', title:'ID', width:75, type:'number', css:'text-center')
    ->addColumn(field:'guid', title:'GUID',type:'text', visible:false)
    ->addColumn(field:'cityName', title:'Şehir Adı', type:'text')
    ->addColumn(field:'countryGuid', title:'Ülke', type:'text')
    ->addColumn(field:'status', title:'Durum', type:"checkbox", render:'function(data) { if(data == 1) { return "Aktif"; } else if(data == 2) { return "Pasif" } else if(data == 3) { return "Silinmiş" } else { return "-" }   }')
    ->setOrder('cityName', 'ASC')
    ->addButton('Yeni Ekle', 'fas fa-add', 'handleEditItem', 'btn-info')
    ->addButton('Düzenle', 'fas fa-edit', 'handleEditItem', 'btn-warning')
    ->addButton('Sil', 'fas fa-trash', 'handleDeleteItems', 'btn-danger')
   
    ->setPageLength(TABLE_DATA_COUNT);

echo $table->render();

print_r($params['page']);

?>
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