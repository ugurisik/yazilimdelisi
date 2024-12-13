<div class="card card-flush h-xl-100">
    <div class="card-header pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Ülkeler</span>
        </h3>
    </div>
    <div class="card-body">
        <?php

        use App\helpers\utils\DataTable;

        $table = (new DataTable('datatable'))
            ->setUrl(SITE_URL . '/' . ADMIN_URI . '/area/list/country')
            ->setExportUrls(SITE_URL . '/' . ADMIN_URI . '/area/list/country', SITE_URL . '/' . ADMIN_URI . '/area/list/country')
            ->addColumn(field: 'id', title: 'ID', width: 75, type: 'number', css: 'text-center')
            ->addColumn(field: 'guid', title: 'GUID', type: 'text', visible: false)
            ->addColumn(field: 'nativeName', title: 'Ülke Adı Lokal', type: 'text')
            ->addColumn(field: 'commonName', title: 'Ülke Adı Genel', type: 'text')
            ->addColumn(field: 'currency', title: 'Para Birimi', type: 'text')
            ->addColumn(field: 'phoneCode', title: 'Telefon Kodu', type: 'text')
            ->addColumn(field: 'capital', title: 'Başkenti', type: 'text')
            ->addColumn(field: 'region', title: 'Bölge', type: 'text')
            ->addColumn(field: 'status', title: 'Durum', type: "checkbox", render: 'function(data) { if(data == 1) { return "Aktif"; } else if(data == 0) { return "Pasif" } else if(data == 2) { return "Silinmiş" } else { return "-" }   }')
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
                                    <option value="2">Silinmiş</option>
                                </select>
                            </div>

                            <div class="col-12" style="max-height: 300px;">
                                <div id="map" style="height: 300px;"></div>
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
    let selectedData = "";

    function addNewItem() {
        $('#countryModal').modal('show');
        resetForm($('#countryModal'));
        selectedData = "";
    }

    async function deleteItem(selected) {
        deleteItemFunc('<?= SITE_URL ?>/<?= ADMIN_URI ?>/area/remove/country', selected, table_datatable)
    }

    function editItem(selected) {
        if (selected.length !== 1) {
            showAlert('Lütfen düzenlemek için bir kayıt seçiniz', 'warning');
            return;
        }
        showLoading(true);
        $.post('<?= SITE_URL ?>/<?= ADMIN_URI ?>/area/get/country/' + selected[0].guid).done(function(response) {
            if (response.status === 'success') {
                console.log(response);
                selectedData = response.data.guid;
                $('#countryModal').modal('show');
                $('input[name=commonName]').val(response.data.commonName);
                $('input[name=nativeName]').val(response.data.nativeName);
                $('input[name=iso2]').val(response.data.iso2);
                $('input[name=iso3]').val(response.data.iso3);
                $('input[name=currency]').val(response.data.currency);
                $('input[name=phoneCode]').val(response.data.phoneCode);
                $('input[name=capital]').val(response.data.capital);
                $('input[name=region]').val(response.data.region);
                $('input[name=subRegion]').val(response.data.subRegion);
                $('input[name=languages]').val(response.data.languages);
                $('input[name=latLng]').val(response.data.latLng);
                $('select[name=status]').val(response.data.status);

                if (response.data.latLng.length > 0) {
                    let latLng = response.data.latLng.split(',');
                    let lat = parseFloat(latLng[0]);
                    let lng = parseFloat(latLng[1]);
                    var map = new google.maps.Map(document.getElementById("map"), {
                        center: { lat: lat, lng: lng },
                        zoom: 3
                    });
                    var marker = new google.maps.Marker({
                        position: { lat: lat, lng: lng },
                        map: map
                    });
                }


            } else {
                showAlert(response.message, response.status);
            }
        });
        showLoading(false);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCfOs48TpyxrcKSsLv926_L-0HgvILsWGs&callback=map"></script>

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
        data.guid = selectedData;

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
            let type = selectedData.length === 0 ? 'add' : 'edit';
            $.post('<?= SITE_URL ?>/<?= ADMIN_URI ?>/area/' + type + '/country', data).done(function(response) {
                if (response.status === 'success') {
                    showAlert(response.message, response.status);
                    table_datatable.ajax.reload();
                    $('#countryModal').modal('hide');
                } else {
                    showAlert(response.message, response.status);
                }
                showLoading(false);
            });
        }

    }
</script>