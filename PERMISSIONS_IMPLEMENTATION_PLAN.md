# خطة تنفيذ نظام صلاحيات شامل للوحة الأدمن

## ملخص المطلوب

المطلوب هو بناء صفحة باسم "الصلاحيات" يمكن منها إنشاء صلاحية/دور باسم مخصص، ثم تحديد ما يظهر لصاحب هذا الدور وما يستطيع تنفيذه داخل كل صفحة.

مثال:

- صفحة الموظفين: هل تظهر في السايد بار؟ هل يستطيع فتحها؟ هل يظهر له زر إضافة موظف؟ هل يستطيع حذف موظف؟ هل يستطيع فتح تفاصيل الموظف أو تعديل بياناته؟
- صفحة الشحنات: هل يستطيع رؤية الصفحة؟ هل يستطيع إضافة شحنة؟ تسليم شحنة؟ حذف شحنة؟ رؤية الأكياس؟

النقطة المهمة: إخفاء الزر وحده غير كاف. لازم كل أكشن في الكنترولر يتراجع عليه بالسيرفر، لأن أي مستخدم ممكن يضرب الرابط أو AJAX endpoint مباشرة.

## الوضع الحالي في المشروع

المشروع CodeIgniter 3 تقريبا، وصفحات الأدمن موجودة في:

- `application/controllers/admin/Dashboard.php`
- `application/controllers/admin/Products.php`
- `application/controllers/admin/Shelves.php`
- `application/controllers/admin/Other.php`

الحماية الحالية تعتمد على أن المستخدم يكون `user_type = admin` داخل `tbl_user`، وكل الأدمنز حاليا لهم نفس الصلاحيات.

النمط الحالي المتكرر:

```php
if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
    redirect(base_url());
}
```

هذا معناه أن النظام لا يفرق بين مدير كامل ومدير محدود.

السايد بار حاليا ثابت في:

- `application/views/view_admin_sidebar.php`

وقائمة الصفحات فيه تشمل: الرئيسية، البحث عن منتج، المنتجات، منخفض الكمية، العمليات، الرفوف، الموظفين، مبيعات الموظفين، جداول الموظفين، المديرين، الطلبات، المبيعات، الفروقات، الشحنات، التقفيلة، تأكيد التقفيلة، جرد المخزون، النماذج، الإعدادات.

ملاحظة أمنية مهمة:

- `admin/Other/delete_shipment` يقبل POST ويحذف شحنة بدون فحص أدمن ظاهر داخل نفس الدالة. عند تنفيذ نظام الصلاحيات لازم يتقفل بصلاحية `shipments.delete`.

## التصميم المقترح

أفضل تصميم هنا هو نظام Roles + Permission Keys.

الدور هو "الصلاحية" التي يسميها المدير، مثل:

- مدير كامل
- موظف مخزن
- مسؤول شحنات
- مراقب فقط

وكل دور يحتوي على قائمة مفاتيح صلاحيات، مثل:

- `employees.view`
- `employees.create`
- `employees.delete`
- `shipments.view`
- `shipments.create`
- `shipments.deliver`
- `shipments.delete`

## اقتراح قاعدة البيانات

إضافة الجداول التالية:

```sql
CREATE TABLE `tbl_roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `description` VARCHAR(255) NULL,
  `is_super` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_role_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_role_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `permission_key` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_role_permission` (`role_id`, `permission_key`),
  KEY `idx_permission_key` (`permission_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `tbl_user`
  ADD COLUMN `role_id` INT UNSIGNED NULL AFTER `user_type`;
```

للبساطة، كل أدمن يأخذ دور واحد فقط في البداية. لو احتجنا بعدين أكتر من دور لنفس الشخص، نبدل `role_id` بجدول `tbl_user_roles`.

## صلاحية المدير الكامل

لازم يكون فيه Super Admin لا يمكن قفل نفسه خارج النظام.

الاقتراح:

- إنشاء دور `مدير كامل` بقيمة `is_super = 1`.
- إسناده للمستخدم الرئيسي الحالي، غالبا `user_id = 2`.
- أي مستخدم على دور `is_super = 1` يمر من كل الصلاحيات.
- صفحة الصلاحيات نفسها لا تظهر إلا لمن عنده `roles.view` ويفضل أن تعديلها يكون لمن عنده `roles.manage` أو Super Admin فقط.

## كتالوج الصلاحيات

الأفضل أن يكون كتالوج الصفحات والأكشنز في ملف config ثابت، وليس في قاعدة البيانات، لأن الأكشنز مرتبطة فعليا بكود موجود في الكنترولرز.

ملف مقترح:

- `application/config/admin_permissions.php`

مثال هيكل:

```php
$config['admin_permissions'] = [
    'employees' => [
        'label' => 'الموظفين',
        'route' => 'admin/employees',
        'permissions' => [
            'employees.view' => 'عرض الصفحة',
            'employees.create' => 'إضافة موظف',
            'employees.detail' => 'فتح تفاصيل موظف',
            'employees.update' => 'تعديل بيانات موظف',
            'employees.delete' => 'حذف موظف',
            'employees.custody.create' => 'إضافة عهدة',
            'employees.settlement.create' => 'إضافة تسوية',
        ],
    ],
];
```

قاعدة مهمة:

- أي صفحة لها مفتاح `*.view`.
- أي زر أو تنفيذ مهم له مفتاح منفصل.
- ظهور الصفحة في السايد بار يعتمد على `*.view`.
- تنفيذ POST/AJAX يعتمد على صلاحية الأكشن نفسه.

## قائمة صلاحيات أولية مقترحة

### الصفحة الرئيسية

- `home.view`: عرض الرئيسية

### البحث عن منتج / التسليم

- `product_search.view`: عرض صفحة البحث عن منتج
- `product_search.cart.add`: إضافة منتج للسلة
- `product_search.cart.remove`: حذف عنصر من السلة
- `product_search.custody.create`: تسليم لموظف
- `product_search.delivery.create`: تسليم للتوصيل
- `product_search.custody.return`: إرجاع من عهدة
- `product_search.delivery.return`: إرجاع من التوصيل

### المنتجات / المخزون

- `products.view`: عرض المنتجات
- `products.import`: رفع ملف مخزون
- `products.edit.view`: فتح صفحة تعديل المنتج
- `products.update.basic`: تعديل بيانات المنتج الأساسية
- `products.update.extra`: تعديل بيانات إضافية
- `products.low.view`: عرض منخفض الكمية

### العمليات

- `operations.view`: عرض سجل العمليات

### الرفوف

- `shelves.view`: عرض الرفوف
- `shelves.create`: إضافة رف
- `shelves.rearrange`: ترتيب/نقل منتجات داخل رف
- `shelves.delete`: حذف رف
- `shelves.items.view`: عرض منتجات الرف
- `shelves.items.remove`: إزالة منتج من الرف

### الموظفين

- `employees.view`: عرض الموظفين
- `employees.create`: إضافة موظف
- `employees.detail`: فتح صفحة موظف
- `employees.update`: تعديل بيانات موظف
- `employees.delete`: حذف موظف
- `employees.custody.create`: إضافة عهدة لموظف
- `employees.settlement.create`: إضافة تسوية

### مبيعات الموظفين

- `employees_sales.view`: عرض صفحة مبيعات الموظفين
- `employees_sales.upload`: رفع ملف مبيعات الموظفين
- `employees_sales.search`: البحث في مبيعات الموظفين
- `employees_sales.download`: تحميل بيانات موظف

### جداول الموظفين

- `employees_timetable.view`: عرض الجداول
- `employees_timetable.save`: حفظ جدول موظف
- `employees_timetable.supervisors.view`: عرض جداول المشرفين

### المديرين

- `admins.view`: عرض المديرين
- `admins.create`: إضافة مدير
- `admins.update`: تعديل مدير
- `admins.delete`: حذف مدير
- `admins.assign_role`: تعيين دور/صلاحية لمدير

### الصلاحيات

- `roles.view`: عرض صفحة الصلاحيات
- `roles.create`: إنشاء صلاحية
- `roles.update`: تعديل صلاحية
- `roles.delete`: حذف صلاحية

### الطلبات

- `requests.view`: عرض الطلبات
- `requests.return`: إرجاع منتج من التوصيل للمخزون

### المبيعات

- `sales.view`: عرض المبيعات

### الفروقات / التسويات

- `settlements.view`: عرض الفروقات
- `settlements.create`: إضافة تسوية

### الشحنات

- `shipments.view`: عرض الشحنات
- `shipments.create`: إضافة شحنة
- `shipments.deliver`: تسليم شحنة
- `shipments.delete`: حذف شحنة
- `shipments.packs.view`: رؤية الأكياس

### التقفيلة

- `lock_admin.view`: عرض التقفيلة
- `lock_track.view`: عرض تأكيد التقفيلة
- `lock_track.update`: تعديل سجل تقفيلة
- `lock_track.delete`: حذف سجل تقفيلة

### النماذج

- `forms.view`: عرض صفحة النماذج
- `forms.settlement.view`: نموذج تسوية الغرامة
- `forms.replacement.view`: نموذج استبدال الجهاز

### الإعدادات

- `settings.view`: عرض الإعدادات
- `settings.update`: تعديل الإعدادات

## الملفات الجديدة المقترحة

- `application/config/admin_permissions.php`
- `application/models/Model_roles.php`
- `application/libraries/Admin_permissions.php`
- `application/controllers/admin/Permissions.php`
- `application/views/view_admin_permissions.php`
- `database/permissions_migration.sql`

لو هنستخدم migrations الرسمية في CodeIgniter:

- `application/migrations/YYYYMMDDHHIISS_create_roles_permissions.php`

لكن migrations مقفولة حاليا في `application/config/migration.php`، لذلك الأسهل في المرحلة الأولى إنشاء ملف SQL داخل `database`.

## خطوات التنفيذ

### المرحلة 1: تجهيز قاعدة البيانات

1. أخذ نسخة احتياطية من قاعدة البيانات.
2. إنشاء `tbl_roles`.
3. إنشاء `tbl_role_permissions`.
4. إضافة `role_id` داخل `tbl_user`.
5. إنشاء دور `مدير كامل`.
6. ربط كل الأدمنز الحاليين بدور المدير الكامل مؤقتا حتى لا يتعطل النظام.

### المرحلة 2: بناء كتالوج الصلاحيات

1. إنشاء `application/config/admin_permissions.php`.
2. تعريف كل صفحة وكل أكشن بمفتاح ثابت.
3. تقسيم الصلاحيات حسب module حتى تظهر منظمة في صفحة الصلاحيات.
4. إضافة label عربي واضح لكل خيار.

### المرحلة 3: بناء Model الصلاحيات

في `Model_roles.php`:

1. `get_roles()`
2. `get_role($id)`
3. `create_role($data, $permissions)`
4. `update_role($id, $data, $permissions)`
5. `delete_role($id)`
6. `get_user_role($user_id)`
7. `get_user_permissions($user_id)`
8. `assign_role_to_user($user_id, $role_id)`

### المرحلة 4: بناء مكتبة الفحص

في `Admin_permissions.php`:

1. `is_admin()`: يتأكد أن المستخدم أدمن.
2. `is_super()`: يتأكد من دور المدير الكامل.
3. `can($permissionKey)`: ترجع true/false.
4. `can_any($keys)`: لو عنده أي صلاحية من مجموعة.
5. `require($permissionKey)`: لو لا يملك الصلاحية يعمل redirect أو يرجع JSON error حسب نوع الطلب.
6. `visible_menu_items()`: تساعد السايد بار يعرض الصفحات المسموحة فقط.

مهم: AJAX/POST يرجع JSON مثل:

```json
{"status":"forbidden","message":"ليس لديك صلاحية لتنفيذ هذا الإجراء"}
```

والصفحات العادية تعمل redirect إلى `admin/index` أو صفحة دخول حسب الحالة.

### المرحلة 5: تعديل تسجيل الدخول والجلسة

عند تسجيل الدخول في `application/controllers/User.php`:

1. بعد نجاح login، تحميل `role_id`.
2. يمكن تخزين `is_super` في session.
3. لا يفضل تخزين كل الصلاحيات في session بشكل دائم إلا مع refresh عند تعديل الصلاحية، حتى لا تظهر مشاكل cache.

اقتراح عملي:

- في البداية اقرأ الصلاحيات من قاعدة البيانات عند كل request أدمن.
- لاحقا يمكن إضافة cache لو الأداء احتاج.

### المرحلة 6: صفحة الصلاحيات

إنشاء صفحة `admin/permissions`.

المطلوب في الواجهة:

1. قائمة الصلاحيات الموجودة.
2. زر إنشاء صلاحية.
3. اسم الصلاحية.
4. وصف اختياري.
5. جدول/أكورديون لكل صفحة.
6. داخل كل صفحة checkbox:
   - عرض الصفحة
   - الإضافة
   - التعديل
   - الحذف
   - الأكشنز الخاصة مثل تسليم شحنة أو إرجاع عهدة
7. زر حفظ.
8. منع حذف دور المدير الكامل.
9. منع المستخدم أن يحذف أو يقفل صلاحيات الدور الذي يعتمد عليه لو هو آخر Super Admin.

الصفحة تتبع نفس UI Pattern الحالي:

- `view_header`
- `view_admin_sidebar`
- `admin-ui-page`
- `public/css/admin-ui-pattern.css`
- أزرار Bootstrap Icons عند الحاجة.

### المرحلة 7: ربط الصلاحيات بالسايد بار

في `application/views/view_admin_sidebar.php`:

1. تحميل مكتبة الصلاحيات أو تمرير helper جاهز.
2. كل عنصر في السايد بار يظهر فقط إذا:
   - المستخدم Super Admin، أو
   - لديه صلاحية `*.view`.

مثال:

```php
<?php if ($this->admin_permissions->can('shipments.view')): ?>
    <!-- shipments link -->
<?php endif; ?>
```

إضافة عنصر جديد:

- `admin/permissions`
- يظهر لمن لديه `roles.view`.

### المرحلة 8: حماية الكنترولرز

استبدال الفحص العام `get_admins()` بفحصين:

1. هل المستخدم أدمن ومسجل دخول؟
2. هل يملك صلاحية الصفحة/الأكشن؟

أمثلة:

- `Other::employees()` يحتاج `employees.view`
- `Other::registration()`:
  - لو `user_type = user` يحتاج `employees.create`
  - لو `user_type = admin` يحتاج `admins.create`
- `Other::delete_employee()` يحتاج `employees.delete`
- `Other::shipments()`:
  - GET يحتاج `shipments.view`
  - POST action `add_shipment` يحتاج `shipments.create`
  - POST action `deliver_shipment` يحتاج `shipments.deliver`
- `Other::delete_shipment()` يحتاج `shipments.delete`
- `Products::add()` يحتاج `products.import`
- `Products::edit()` يحتاج `products.edit.view`
- `Products::update_basic_product_data()` يحتاج `products.update.basic`
- `Products::add_item_as_custody()` يحتاج `product_search.custody.create`
- `Products::add_product_to_delivery()` يحتاج `product_search.delivery.create`
- `Products::product_delivery_return()` يحتاج `product_search.delivery.return`
- `Shelves::index()`:
  - GET يحتاج `shelves.view`
  - POST action `add` يحتاج `shelves.create`
  - POST action `rearrange_shelf` يحتاج `shelves.rearrange`
- `Shelves::delete_shelf()` يحتاج `shelves.delete`
- `Shelves::show()` يحتاج `shelves.items.view`
- `Shelves::show()` POST delete يحتاج `shelves.items.remove`

### المرحلة 9: إخفاء الأزرار حسب الصلاحية

بعد حماية السيرفر، يتم تنظيف الواجهة:

- زر إضافة موظف يظهر مع `employees.create`
- زر حذف موظف يظهر مع `employees.delete`
- رابط تفاصيل موظف يظهر مع `employees.detail`
- زر إضافة شحنة يظهر مع `shipments.create`
- زر تسليم الشحنة يظهر مع `shipments.deliver`
- زر حذف الشحنة يظهر مع `shipments.delete`
- زر رؤية الأكياس يظهر مع `shipments.packs.view`
- أزرار تعديل/حذف التقفيلة حسب `lock_track.update` و `lock_track.delete`
- أزرار الإعدادات والحفظ حسب `settings.update`

### المرحلة 10: تعيين الصلاحية للمديرين

في صفحة المديرين `view_admin_admins.php`:

1. إضافة اختيار role عند إنشاء مدير.
2. إضافة اختيار role عند تعديل مدير.
3. حماية تغيير الدور بصلاحية `admins.assign_role`.
4. منع تعديل دور آخر Super Admin.

### المرحلة 11: اختبارات يدوية مطلوبة

إنشاء 4 أدوار للتجربة:

1. مدير كامل: كل الصلاحيات.
2. مشاهد فقط: view فقط بدون أي POST actions.
3. مسؤول شحنات: `shipments.view/create/deliver/delete/packs.view`.
4. مسؤول موظفين: `employees.view/create/detail/delete`.

سيناريوهات الاختبار:

1. أدمن بدون `shipments.view` لا يرى الشحنات في السايد بار ولا يفتح الرابط مباشرة.
2. أدمن لديه `shipments.view` فقط يرى الصفحة، لكن لا تظهر أزرار إضافة/تسليم/حذف.
3. ضرب endpoint حذف شحنة مباشرة بدون `shipments.delete` يرجع forbidden ولا يحذف.
4. أدمن لديه `employees.create` يرى زر إضافة موظف وينجح الحفظ.
5. أدمن بدون `employees.delete` لا يرى زر الحذف ولو أرسل POST لا يتم الحذف.
6. Super Admin يقدر يعمل كل شيء.
7. لا يمكن حذف آخر Super Admin أو إزالة صلاحياته الحرجة.

## اقتراح تبسيط لو التنفيذ كبير

بدل تنفيذ كل الأكشنز مرة واحدة، نبدأ بنسخة مرحلية:

### نسخة أولى سريعة وآمنة

1. Roles.
2. صلاحية `view` لكل صفحة.
3. حماية السايد بار وفتح الصفحات.
4. صفحة إنشاء/تعديل الصلاحية.

### نسخة ثانية

1. إضافة صلاحيات الأزرار الأساسية:
   - create
   - update
   - delete
2. حماية POST endpoints.

### نسخة ثالثة

1. الأكشنز الخاصة:
   - تسليم شحنة
   - إرجاع من عهدة
   - رفع ملفات
   - تحميل تقارير
   - تعديل التقفيلة

هذا يقلل المخاطرة لأن المشروع فيه صفحات كثيرة وأكشنز AJAX متعددة.

## توصية نهائية

أنسب حل لهذا المشروع:

- كتالوج صلاحيات ثابت في config.
- تخزين الأدوار والقيم المختارة في DB.
- Super Admin دائم.
- فحص مركزي في مكتبة واحدة.
- حماية السيرفر أولا، ثم إخفاء عناصر الواجهة ثانيا.

بهذا الشكل النظام يبقى شامل وقابل للتوسع، وفي نفس الوقت لا نفتح باب أخطاء بسبب صلاحيات منشأة عشوائيا لا يقابلها كود فعلي.
