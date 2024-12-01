<div class="card card-flush h-xl-100">
    <div class="card-header pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Ülkeler</span>
        </h3>
    </div>
    <div class="card-body">
        <?php

        use App\helpers\utils\DataTable;

        $table = (new DataTable('countryTable'))
            ->setUrl(SITE_URL . '/admin555/area/list/country')
            ->setExportUrls(SITE_URL . '/' . ADMIN_URI . '/exportExcel', SITE_URL . '/admin555/exportPdf')
            ->addColumn(field: 'id', title: 'ID', width: 75, type: 'number', css: 'text-center')
            ->addColumn(field: 'guid', title: 'GUID', type: 'text', visible: false)
            ->addColumn(field: 'nativeName', title: 'Ülke Adı Lokal', type: 'text')
            ->addColumn(field: 'commonName', title: 'Ülke Adı Genel', type: 'text')
            ->addColumn(field: 'currency', title: 'Para Birimi', type: 'text')
            ->addColumn(field: 'phoneCode', title: 'Telefon Kodu', type: 'text')
            ->addColumn(field: 'capital', title: 'Başkenti', type: 'text')
            ->addColumn(field: 'region', title: 'Bölge', type: 'text')
            ->addColumn(field: 'status', title: 'Durum', type: "checkbox", render: 'function(data) { if(data == 1) { return "Aktif"; } else if(data == 0) { return "Pasif" } else if(data == 3) { return "Silinmiş" } else { return "-" }   }')
            ->setOrder('cityName', 'ASC')
            ->addButton('Yeni Ekle', 'fas fa-add', 'addNewItem', 'btn-info')
            ->addButton('Düzenle', 'fas fa-edit', 'editItem', 'btn-warning')
            ->addButton('Sil', 'fas fa-trash', 'deleteItem', 'btn-danger')

            ->setPageLength(TABLE_DATA_COUNT);

        echo $table->render();
        ?>
    </div>
</div>


<div class="modal bg-body fade" tabindex="-1" id="countryModal">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content shadow-none">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kayıt / Düzenle</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-diamond-fill fs-2x"></i>
                </div>
            </div>

            <div class="modal-body">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-body pt-0 row">
                            <div class="mb-5 col-3">
                                <label class="required form-label">Ülke Adı (Genel)</label>
                                <input type="text" name="commonName" class="form-control mb-2" maxlength="32" placeholder="Ülke Adı (Genel)" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="required form-label">Ülke Adı (Lokal)</label>
                                <input type="text" name="nativeName" class="form-control mb-2" maxlength="32" placeholder="Ülke Adı (Lokal)" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Kısa Kod (2)</label>
                                <input type="text" name="iso2" class="form-control mb-2" maxlength="2" placeholder="Kısa Kod (2)" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Kısa Kod (3)</label>
                                <input type="text" name="iso3" class="form-control mb-2" maxlength="3" placeholder="Kısa Kod (3)" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Para Birimi</label>
                                <input type="text" name="currency" class="form-control mb-2" maxlength="8" placeholder="Para Birimi" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Telefon Kodu</label>
                                <input type="text" name="phoneCode" class="form-control mb-2" maxlength="8" placeholder="Telefon Kodu" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Başkenti</label>
                                <input type="text" name="capital" class="form-control mb-2" maxlength="32" placeholder="Başkenti" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Bölge</label>
                                <input type="text" name="region" class="form-control mb-2" maxlength="32" placeholder="Bölge" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Alt Bölge</label>
                                <input type="text" name="subRegion" class="form-control mb-2" maxlength="32" placeholder="Alt Bölge" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Dil</label>
                                <input type="text" name="languages" class="form-control mb-2" maxlength="16" placeholder="Dil" value="" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Lat Lng</label>
                                <input type="text" name="latLng" class="form-control mb-2" maxlength="32" placeholder="Harita Konum Bilgisi: Lat,Lng" value="awdawd" />
                            </div>
                            <div class="mb-5 col-3">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select mb-2">
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" onclick="saveItem()">Kaydet</button>
            </div>
        </div>
    </div>
</div>











<script>
    function addNewItem() {
        $('#countryModal').modal('show');
        resetForm($('#countryModal'));
    }

    function deleteItem(selected) {
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

    function editItem(selected) {
        if (selected.length !== 1) {
            alert('Lütfen düzenlemek için bir kayıt seçiniz');
            return;
        }
        showAlert('Seçilen Kayıt: ' + selected[0].guid, 'info');
        // window.location.href = `<?= SITE_URL ?>/<?= ADMIN_URI ?>/edit/${selected[0].guid}`;
    }
</script>


<script>
    function saveItem() {
        var data = {};
        data.commonName = $('input[name=commonName]').val();
        data.nativeName = $('input[name=nativeName]').val();
        data.iso2 = $('input[name=iso2]').val();
        data.iso3 = $('input[name=iso3]').val();
        data.currency = $('input[name=currency]').val();
        data.phoneCode = $('input[name=phoneCode]').val();
        data.capital = $('input[name=capital]').val();
        data.region = $('input[name=region]').val();
        data.subRegion = $('input[name=subRegion]').val();
        data.languages = $('input[name=languages]').val();
        data.latLng = $('input[name=latLng]').val();
        data.status = $('select[name=status]').val();
        data.csrf_token = $('input[id=csrf_token]').val();

        let warning = false;
        if (data.commonName.length === 0) {
            showAlert('Ülke adı (Genel) giriş yapmadınız', 'warning');
            $('input[name=commonName]').focus();
            warning = true;
        }
        if (data.nativeName.length === 0) {
            showAlert('Ülke adı (Lokal) giriş yapmadınız', 'warning');
            $('input[name=nativeName]').focus();
            warning = true;
        }

        if (!warning) {
            showLoading(true);
            $.post('<?= SITE_URL ?>/<?= ADMIN_URI ?>/area/add/country', data).done(function(response) {
                if (response.status === 'success') {
                    showAlert(response.message, response.status);
                    table_countryTable.ajax.reload();
                    $('#countryModal').modal('hide');
                } else {
                    showAlert(response.message, response.status);
                }
                showLoading(false);
            });
        }

    }
</script>