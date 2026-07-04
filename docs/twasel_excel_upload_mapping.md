# توثيق رفع ملف Excel لمبيعات تواصل وظهوره في التقفيلة

الملف ده بيوثق المسار الحالي الظاهر في:

- صفحة الرفع: `/admin/upload-form`
- صفحة التقفيلة: `/admin/lock-admin`

المسار ده مختلف عن مسار `employees_sales` الموجود في المشروع. الكلام هنا خاص بفورم "مبيعات تواصل" اللي بيرفع على `excel_import/import` ويحفظ في جدول `tbl_insert_excel`.

## الملفات المسؤولة

| الجزء | الملف | الدور |
|---|---|---|
| فورم رفع مبيعات تواصل | `application/views/view_admin_upload_form.php` | يعرض اختيار التاريخ ورفع ملف Excel، ويعرض السجلات المحفوظة |
| كود قراءة Excel | `application/controllers/Excel_import.php` | يقرأ الشيتات والصفوف ويحضر البيانات للحفظ |
| موديل حفظ/قراءة بيانات Excel | `application/models/Excel_import_model.php` | يحفظ في `tbl_insert_excel` ويقرأ منه حسب الموظف والتاريخ |
| صفحة التقفيلة | `application/views/view_admin_lock_admin.php` | تجمع مبيعات تواصل وباقي أنواع المبيعات وتعرضها لكل موظف |
| عمليات التعديل والحذف | `application/controllers/admin/Other.php` | تعديل/حذف سجلات `tbl_insert_excel` من صفحات الإدارة |
| جدول قاعدة البيانات | `database/find-next.sql` | تعريف `tbl_insert_excel` |

## جدول قاعدة البيانات المستخدم

اسم الجدول: `tbl_insert_excel`

| العمود | نوعه في SQL | استخدامه الحالي |
|---|---:|---|
| `insert_excel_id` | `int` | رقم السجل، Auto Increment |
| `insert_excel_date` | `varchar(120)` | تاريخ العملية بصيغة `Y-m-d` |
| `insert_excel_ordern` | `varchar(120)` | رقم الطلب من Excel |
| `insert_excel_new_ordern` | `varchar(120)` | رقم الطلب الجديد من Excel، وله أولوية العرض في صفحة الرفع |
| `insert_excel_description` | `varchar(255)` | وصف المنتج من Excel |
| `insert_excel_product_serial_number` | `varchar(255)` | PRODUCT SERIAL NUMBER من Excel |
| `insert_excel_uid` | `int` | رقم المستخدم الداخلي `tbl_user.user_id` |
| `insert_excel_twasel` | `varchar(120)` | مبلغ مبيعات تواصل من Excel |
| `insert_excel_electronic` | `varchar(120)` | شحن إلكتروني، لا يقرأ من رفع Excel الحالي |
| `insert_excel_jowy` | `varchar(120)` | مبيعات جوي، لا يقرأ من رفع Excel الحالي |
| `insert_excel_quickplus` | `varchar(120)` | مبيعات كويك بلس، لا يقرأ من رفع Excel الحالي |

## فورم الرفع

في `view_admin_upload_form.php` الفورم بيرسل:

| input | مطلوب؟ | المعنى |
|---|---|---|
| `import_date` | نعم | التاريخ المختار في الصفحة. يستخدم كفلتر، ولا يتم حفظه مباشرة لو تاريخ Excel مختلف |
| `file` | نعم | ملف Excel بامتداد `.xlsx` أو `.xls` |

الفورم يرسل على:

```text
excel_import/import
```

## طريقة قراءة ملف Excel

الكود في `Excel_import::import()` يعمل الآتي:

1. يقرأ الملف المؤقت من `$_FILES["file"]["tmp_name"]`.
2. يفتح الملف بـ `PHPExcel_IOFactory::load($path)`.
3. يمر على كل الشيتات داخل الملف باستخدام `getWorksheetIterator()`.
4. داخل كل شيت، يقرأ الصفوف من الصف رقم `2` إلى آخر صف.
5. لا يحفظ إلا الصفوف التي قيمة العمود `D` فيها تساوي `Complete`.

مهم: ترقيم الأعمدة في `getCellByColumnAndRow()` يبدأ من صفر:

```text
0 = A
1 = B
2 = C
3 = D
...
13 = N
17 = R
```

## الخلايا المقروءة من كل صف

لو رقم الصف الحالي هو `{row}`، فالخلايا المقروءة هي:

| الخلية | كود القراءة | المعنى | أين تذهب؟ |
|---|---|---|---|
| `D{row}` | `getCellByColumnAndRow(3, $row)` | حالة الطلب | شرط فقط. لازم تكون `Complete` ولا تحفظ في قاعدة البيانات |
| `R{row}` | `getCellByColumnAndRow(17, $row)` | الرقم الوظيفي للموظف في Excel | يستخدم للبحث في `tbl_user.user_employee_Id`، والنتيجة تحفظ كـ `insert_excel_uid = tbl_user.user_id` |
| `A{row}` | `getCellByColumnAndRow(0, $row)` | تاريخ الطلب كرقم Excel serial date | يتحول إلى `Y-m-d` ويحفظ في `insert_excel_date` |
| `B{row}` | `getCellByColumnAndRow(1, $row)` | رقم الطلب | يحفظ في `insert_excel_ordern` |
| `F{row}` | `getCellByColumnAndRow(5, $row)` | رقم الطلب الجديد | يحفظ في `insert_excel_new_ordern` |
| `I{row}` | `getCellByColumnAndRow(8, $row)` | الوصف | يحفظ في `insert_excel_description` |
| `K{row}` | `getCellByColumnAndRow(10, $row)` | PRODUCT SERIAL NUMBER | يحفظ في `insert_excel_product_serial_number` |
| `N{row}` | `getCellByColumnAndRow(13, $row)` | مبلغ مبيعات تواصل | يحفظ في `insert_excel_twasel` |

الكود حاليا يقرأ الأعمدة الإضافية `F`, `I`, و `K` بجانب الأعمدة القديمة، ويحفظها لاستخدامها لاحقا.

## شروط قبول الصف قبل الحفظ

الصف لا يدخل قاعدة البيانات إلا لو كل الشروط دي اتحققت:

| الشرط | التفاصيل |
|---|---|
| الحالة `Complete` | قيمة `D{row}` لازم تكون بالضبط `Complete` |
| تاريخ الصف يساوي تاريخ الفورم | تاريخ `A{row}` بعد التحويل لازم يساوي `import_date` |
| مبلغ تواصل أكبر من صفر | قيمة `N{row}` لازم تكون `> 0` |
| الموظف موجود | قيمة `R{row}` لازم تطابق `tbl_user.user_employee_Id` لمستخدم `user_type = 'user'` |

لو مفيش أي صف مطابق، الصفحة تعرض رسالة:

```text
لاتوجد بيانات مطابقة للتاريخ المدخل
```

## شكل البيانات المحفوظة

لما الصف يتقبل، الكود يجهز array بالشكل ده:

```php
[
    'insert_excel_date'   => $insert_excel_date,
    'insert_excel_uid'    => $user_id,
    'insert_excel_twasel' => $insert_excel_twasel,
    'insert_excel_ordern' => $insert_excel_ordern,
    'insert_excel_new_ordern' => $insert_excel_new_ordern,
    'insert_excel_description' => $insert_excel_description,
    'insert_excel_product_serial_number' => $insert_excel_product_serial_number,
]
```

ثم يحفظها في `tbl_insert_excel` عن طريق:

```php
$this->Excel_import_model->insert($data);
```

## منع التكرار أثناء الحفظ

`Excel_import_model::insert()` لا يمنع التكرار برقم الطلب. هو يفحص الموجود مسبقا باستخدام:

```text
insert_excel_uid + insert_excel_date
```

معنى ده:

- لو فيه سجل قديم لنفس الموظف ونفس التاريخ، أي سجل جديد لنفس الموظف والتاريخ سيتم تجاهله حتى لو رقم الطلب مختلف.
- لو نفس ملف الرفع فيه أكثر من صف لنفس الموظف ونفس التاريخ، الكود الحالي ممكن يحفظهم مع بعض لأن الفحص يتم على السجلات الموجودة قبل الرفع فقط.
- `insert_excel_ordern` محفوظ للعرض والمتابعة، لكنه مش داخل في شرط منع التكرار.

## ماذا يظهر في صفحة الرفع بعد الحفظ؟

صفحة `/admin/upload-form` تقرأ من:

```php
$this->Excel_import_model->select()
```

والاستعلام:

- يقرأ من `tbl_insert_excel`
- يرتب حسب `insert_excel_id DESC`
- يعرض فقط السجلات التي `insert_excel_twasel != ''`

الأعمدة المعروضة في صفحة الرفع:

| عمود الصفحة | مصدره |
|---|---|
| التاريخ | `tbl_insert_excel.insert_excel_date` |
| اسم الموظف | `tbl_user.user_fillname` بعد البحث بـ `insert_excel_uid` |
| رقم الطلب | `insert_excel_new_ordern` لو موجود، وإلا `insert_excel_ordern` |
| مبيعات تواصل | `tbl_insert_excel.insert_excel_twasel` |

التعديل من هذه الصفحة يعدل `insert_excel_twasel` فقط. الحذف يحذف سجل `tbl_insert_excel` بالكامل باستخدام `insert_excel_id`.

زر حذف بالتاريخ في نفس الصفحة يحذف كل سجلات `tbl_insert_excel` التي تاريخها يساوي التاريخ المختار، وليس مبيعات تواصل فقط.

## كيف تظهر البيانات في صفحة التقفيلة؟

صفحة `/admin/lock-admin` تعمل الآتي:

1. تحدد الفترة من `GET date_start` و `GET date_end`.
2. لو مفيش تواريخ في الرابط، تستخدم تاريخ اليوم كبداية ونهاية.
3. تقرأ كل الموظفين من `tbl_user` بشرط:
   - `user_type = 'user'`
   - `is_deleted = 0`
4. لكل موظف، تقرأ سجلاته من `tbl_insert_excel` داخل الفترة باستخدام `selectbyid($user_id, $date_start, $date_end)`.
5. تجمع القيم كأرقام `float`.

## صفوف التقفيلة ومصدر كل صف

| صف التقفيلة | مصدر البيانات | طريقة الحساب |
|---|---|---|
| مبيعات تواصل | `insert_excel_twasel` | مجموع كل سجلات الموظف داخل الفترة |
| شحن إلكتروني | `insert_excel_electronic` | مجموع كل سجلات الموظف داخل الفترة |
| مبيعات جوي | `insert_excel_jowy` | مجموع كل سجلات الموظف داخل الفترة |
| مبيعات كويك بلس | `insert_excel_quickplus` | مجموع كل سجلات الموظف داخل الفترة |
| الإجمالي | الأربع خانات السابقة | `twasel + electronic + jowy + quickplus` |
| مبلغ الشبكة | `tbl_user_lock.user_lock_span` | من `getlock_by_userid()` لنفس الموظف والفترة |
| النقدي | `tbl_user_lock.user_lock_cash` | من `getlock_by_userid()` لنفس الموظف والفترة |
| إجمالي المدفوع | النقدي + الشبكة | `cash + span` |
| تمارا | لا يوجد مصدر حاليا | ثابت `0.00` |
| المحفظة | لا يوجد مصدر حاليا | ثابت `0.00` |
| رصيد إضافي | لا يوجد مصدر حاليا | ثابت `0.00` |
| الفروقات | بيانات المبيعات + التقفيلة + التسويات | `(cash + span) - total_sales + settlements` |

ملاحظة: صفحة التقفيلة تعرض الموظفين الذين إجمالي مبيعاتهم أكبر من صفر فقط، وبحد أقصى `17` موظف حاليا بسبب:

```php
$size = 17;
```

## حقول الشحن الإلكتروني وجوي وكويك بلس

رغم أن الأعمدة موجودة في `tbl_insert_excel` وتظهر في التقفيلة، رفع ملف مبيعات تواصل الحالي لا يقرأها من Excel.

هذه الحقول تدخل من مسار آخر:

- `application/controllers/User.php`
- الدالة `sendlock()`
- تحفظ في `tbl_insert_excel` عن طريق `Excel_import_model->insert_sales()`

الحقول:

| input | يحفظ في |
|---|---|
| `user_lock_electronic` | `insert_excel_electronic` |
| `user_lock_jowy` | `insert_excel_jowy` |
| `user_lock_quick_plus` | `insert_excel_quickplus` |

وتتعدل من صفحة `lock-track` عن طريق `admin/edit_lock_track`.

## ملاحظة عن `public/file.xlsx`

يوجد ملف `public/file.xlsx` ظاهر كأنه نموذج قديم، وشكله أفقي:

- صف 1: معرف الموظف
- صف 2: مبيعات تواصل
- صف 3: شحن إلكتروني
- صف 4: مبيعات جوي
- صف 5: مبيعات كويك بلس

لكن كود الرفع الحالي لا يقرأ هذا الشكل. الكود الحالي يتوقع صفوف عمليات، ويستخدم الأعمدة `A`, `B`, `D`, `N`, `R` في كل صف.

لو عايز تعتمد نموذج `public/file.xlsx`، لازم يتعمل كود قراءة جديد مختلف عن الموجود حاليا.

## لو عايز تزود حقل جديد من Excel

اتبع نفس السلسلة دي:

1. أضف عمود جديد في `tbl_insert_excel`، مثال: `insert_excel_tamara`.
2. في `Excel_import::import()` اقرأ الخلية المطلوبة داخل شرط `Complete`.
3. أضف القيمة داخل array الحفظ `$data[]`.
4. لو عايز تظهر في صفحة الرفع، عدل `view_admin_upload_form.php`.
5. لو عايز تتعدل من الواجهة، عدل دالة التعديل المناسبة في `Other.php`.
6. لو عايز تظهر في التقفيلة، عدل `view_admin_lock_admin.php`:
   - عرف array جديدة.
   - اجمعها داخل loop الخاص بـ `$excel_data`.
   - أضف صف في الجدول.
   - قرر هل تدخل في `total_sales` والفروقات أم لا.
7. لو الحقل يدخل من فورم التقفيلة اليدوي وليس من Excel، عدل `User::sendlock()` و `view_admin_lock_track.php`.

مثال تحويل رقم عمود Excel إلى حرف:

| رقم PHPExcel | حرف Excel |
|---:|---|
| `0` | `A` |
| `1` | `B` |
| `3` | `D` |
| `5` | `F` |
| `8` | `I` |
| `10` | `K` |
| `13` | `N` |
| `17` | `R` |

## ملخص سريع

رفع "مبيعات تواصل" الحالي يقرأ فقط:

```text
A{row} = التاريخ
B{row} = رقم الطلب
D{row} = الحالة، لازم Complete
F{row} = رقم الطلب الجديد
I{row} = الوصف
K{row} = PRODUCT SERIAL NUMBER
N{row} = مبلغ مبيعات تواصل
R{row} = الرقم الوظيفي للموظف
```

ويحفظ في:

```text
tbl_insert_excel.insert_excel_date
tbl_insert_excel.insert_excel_ordern
tbl_insert_excel.insert_excel_new_ordern
tbl_insert_excel.insert_excel_description
tbl_insert_excel.insert_excel_product_serial_number
tbl_insert_excel.insert_excel_uid
tbl_insert_excel.insert_excel_twasel
```

ثم صفحة التقفيلة تجمع `insert_excel_twasel` مع `insert_excel_electronic`, `insert_excel_jowy`, و `insert_excel_quickplus` من نفس الجدول لكل موظف داخل الفترة المختارة.
