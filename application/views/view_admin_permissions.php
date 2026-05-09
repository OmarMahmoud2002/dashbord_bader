<?php $this->view('view_header'); ?>
<title>الصلاحيات</title>
<?php $this->view('view_admin_sidebar'); ?>

<?php
$pages = [];
foreach ($permission_groups as $group) {
    foreach ($group['pages'] as $page) {
        $pages[] = $page;
    }
}
?>

<div class="app-content admin-ui-page admin-ui-permissions permissions-simple-page" dir="rtl">
    <div class="app-content-header">
        <h1 class="app-content-headerText">الصلاحيات</h1>
    </div>

    <form id="permissionsForm" class="permissions-simple-form" method="post" action="<?php echo base_url().MOD_VALUE.'admin/permissions'; ?>">
        <section class="permissions-create-box">
            <div class="permissions-create-title">
                <i class="bi bi-shield-plus"></i>
                <div>
                    <h2>إنشاء صلاحية</h2>
                    <p>اكتب اسم الصلاحية واختار المسموح به من الجدول.</p>
                </div>
            </div>

            <div class="permissions-create-fields">
                <div class="permissions-field">
                    <label for="roleName">اسم الصلاحية</label>
                    <input id="roleName" name="role_name" type="text" placeholder="مثال: مسؤول مخزن">
                </div>

                <div class="permissions-field">
                    <label for="roleDescription">الوصف</label>
                    <input id="roleDescription" name="role_description" type="text" placeholder="اختياري">
                </div>

                <button type="submit" class="permissions-main-save">
                    <i class="bi bi-check2-circle"></i>
                    حفظ
                </button>
            </div>
        </section>

        <section class="permissions-table-box">
            <div class="permissions-table-head">
                <h2>جدول الصلاحيات</h2>
                <div class="permissions-table-actions">
                    <button type="button" id="selectAllPermissions">
                        <i class="bi bi-check2-square"></i>
                        تحديد الكل
                    </button>
                    <button type="button" id="clearPermissions">
                        <i class="bi bi-x-circle"></i>
                        تفريغ
                    </button>
                </div>
            </div>

            <div class="permissions-table-wrap">
                <table class="permissions-simple-table">
                    <thead>
                        <tr>
                            <th>اسم الصفحة</th>
                            <th>الصلاحيات المتاحة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $page) { ?>
                            <tr>
                                <td class="permissions-page-name">
                                    <strong><?php echo htmlspecialchars($page['label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                </td>
                                <td>
                                    <div class="permissions-check-list">
                                        <?php foreach ($page['permissions'] as $permission) { ?>
                                            <label class="permissions-check">
                                                <input type="checkbox" name="permissions[]" value="<?php echo htmlspecialchars($permission['key'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <span></span>
                                                <b><?php echo htmlspecialchars($permission['label'], ENT_QUOTES, 'UTF-8'); ?></b>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </form>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('permissionsForm');
    var inputs = Array.prototype.slice.call(document.querySelectorAll('.permissions-check input'));

    document.getElementById('selectAllPermissions').addEventListener('click', function () {
        inputs.forEach(function (input) {
            input.checked = true;
        });
    });

    document.getElementById('clearPermissions').addEventListener('click', function () {
        inputs.forEach(function (input) {
            input.checked = false;
        });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (window.toastr) {
            toastr.success('تم تجهيز الشكل فقط، ولسه الحفظ الفعلي هيتربط مع اللوجيك بعدين.');
        }
    });
});
</script>

<?php $this->view('view_footer'); ?>
