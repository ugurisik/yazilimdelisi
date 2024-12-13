<div class="d-flex flex-column flex-root" id="kt_app_root">
    <style>
        body {
            background-image: url('<?= SITE_URL ?>/public/assets/media/auth/bg1.jpg');
        }

        [data-theme="dark"] body {
            background-image: url('<?= SITE_URL ?>/public/assets/media/auth/bg1-dark.jpg');
        }
    </style>
    <div class="d-flex flex-column flex-center flex-column-fluid">
        <div class="d-flex flex-column flex-center text-center p-10">
            <div class="card card-flush w-lg-600px py-5">
                <div class="card-body py-15 py-lg-20">
                    <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">Hay aksi!</h1>
                    <div class="fw-semibold fs-6 text-gray-500 mb-7">Aradığınız sayfayı bulamadık.</div>
                    <div class="mb-3">
                        <img src="<?= SITE_URL ?>/public/assets/media/auth/404-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
                        <img src="<?= SITE_URL ?>/public/assets/media/auth/404-error-dark.png" class="mw-100 mh-300px theme-dark-show" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
