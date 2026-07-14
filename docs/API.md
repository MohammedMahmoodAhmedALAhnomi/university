# توثيق واجهة API

جميع المسارات تعيد HTML (صفحات كاملة)، باستثناء مسار التقييم الذي يعيد JSON.

---

## المسارات العامة (Public)

### الصفحة الرئيسية
```
GET /
GET /home
```
يعرض الصفحة الرئيسية مع الإحصائيات والتخصصات والإعلانات المثبتة.

### عرض التخصص
```
GET /majors/{id}
```
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| id | int | معرف التخصص |

يعرض تفاصيل التخصص مع المواد مقسمة حسب المستوى والفصل.

### عرض المادة
```
GET /courses/{id}
```
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| id | int | معرف المادة |

يعرض تفاصيل المادة مع التقييم والملفات المرفوعة.

### قائمة المواد
```
GET /courses
```
معاملات اختيارية:
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| major_id | int | فلترة حسب التخصص |

### البحث
```
GET /search
```
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| q | string | كلمة البحث |
| major_id | int | فلترة حسب التخصص |
| level_id | int | فلترة حسب المستوى |
| semester_id | int | فلترة حسب الفصل |

### الإعلانات
```
GET /announcements
```
يعرض جميع الإعلانات النشطة.

```
GET /announcements/{id}
```
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| id | int | معرف الإعلان |

### تحميل ملف
```
GET /files/{id}/download
```
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| id | int | معرف الملف |

يزيد عداد التحميل ويعيد الملف للتحميل.

### معاينة ملف
```
GET /files/{id}/preview
```
| الباراميتر | النوع | الوصف |
|-----------|------|-------|
| id | int | معرف الملف |

يعرض الملف في المتصفح (للملفات التي تدعم العرض المباشر).

### معلومات عن النظام
```
GET /about
```

### اتصل بنا
```
GET /contact
```

---

## مسارات التقييم (Rating API) - JSON

### تقييم مادة
```
POST /courses/{id}/rate
Content-Type: application/x-www-form-urlencoded
```

**Body:**
| الحقل | النوع | مطلوب | الوصف |
|-------|------|-------|-------|
| course_id | int | نعم | معرف المادة |
| rating | int | نعم | التقييم (1-5) |

**الاستجابة (JSON):**
```json
{
    "success": true,
    "message": "تم تسجيل تقييمك بنجاح",
    "avg_rating": 4.2,
    "rating_count": 15
}
```

**حالات الخطأ:**
```json
{
    "success": false,
    "message": "لقد قمت بتقييم هذه المادة من قبل"
}
```

```json
{
    "success": false,
    "message": "المادة غير موجودة"
}
```

```json
{
    "success": false,
    "message": "التقييم يجب أن يكون بين 1 و 5"
}
```

---

## مسارات المصادقة (Auth)

### صفحة تسجيل الدخول
```
GET /login
```

### تنفيذ تسجيل الدخول
```
POST /login/post
Content-Type: application/x-www-form-urlencoded
```

| الحقل | النوع | مطلوب | الوصف |
|-------|------|-------|-------|
| email | string | نعم | البريد الإلكتروني |
| password | string | نعم | كلمة السر |
| _csrf_token | string | نعم | رمز CSRF |

عند النجاح يعيد التوجيه إلى `/admin/dashboard`.

### تسجيل الخروج
```
GET /logout
```
يعيد التوجيه إلى الصفحة الرئيسية.

---

## مسارات لوحة التحكم (Admin - يتطلب مصادقة)

### لوحة الإحصائيات
```
GET /admin/dashboard
```

### إدارة التخصصات
```
GET    /admin/majors            # قائمة التخصصات
GET    /admin/majors/create     # صفحة إضافة تخصص (Admin)
POST   /admin/majors/store      # حفظ تخصص جديد (Admin)
GET    /admin/majors/{id}/edit  # صفحة تعديل تخصص (Admin)
POST   /admin/majors/{id}/update # تحديث تخصص (Admin)
GET    /admin/majors/{id}/delete # حذف تخصص (Admin)
```

### إدارة المستويات
```
GET    /admin/levels            # قائمة المستويات
GET    /admin/levels/create     # صفحة إضافة مستوى (Admin)
POST   /admin/levels/store      # حفظ مستوى جديد (Admin)
GET    /admin/levels/{id}/edit  # صفحة تعديل مستوى (Admin)
POST   /admin/levels/{id}/update # تحديث مستوى (Admin)
GET    /admin/levels/{id}/delete # حذف مستوى (Admin)
```

### إدارة الفصول
```
GET    /admin/semesters          # قائمة الفصول
GET    /admin/semesters/create   # صفحة إضافة فصل (Admin)
POST   /admin/semesters/store    # حفظ فصل جديد (Admin)
GET    /admin/semesters/{id}/edit # صفحة تعديل فصل (Admin)
POST   /admin/semesters/{id}/update # تحديث فصل (Admin)
GET    /admin/semesters/{id}/delete # حذف فصل (Admin)
```

### إدارة المواد
```
GET    /admin/courses            # قائمة المواد
GET    /admin/courses/create     # صفحة إضافة مادة
POST   /admin/courses/store      # حفظ مادة جديدة
GET    /admin/courses/{id}/edit  # صفحة تعديل مادة
POST   /admin/courses/{id}/update # تحديث مادة
GET    /admin/courses/{id}/delete # حذف مادة
```

### إدارة الملفات
```
GET    /admin/files              # قائمة الملفات
GET    /admin/files/create       # صفحة رفع ملف
POST   /admin/files/store        # حفظ ملف مرفوع
GET    /admin/files/{id}/edit    # صفحة تعديل ملف
POST   /admin/files/{id}/update  # تحديث ملف
GET    /admin/files/{id}/delete  # حذف ملف
GET    /admin/files/{id}/toggle  # تبديل حالة الموافقة على الملف
```

### إدارة المستخدمين (Admin فقط)
```
GET    /admin/users              # قائمة المستخدمين
GET    /admin/users/create       # صفحة إضافة مستخدم
POST   /admin/users/store        # حفظ مستخدم جديد
GET    /admin/users/{id}/edit    # صفحة تعديل مستخدم
POST   /admin/users/{id}/update  # تحديث مستخدم
GET    /admin/users/{id}/delete  # حذف مستخدم
```

### إدارة الإعلانات
```
GET    /admin/announcements           # قائمة الإعلانات
GET    /admin/announcements/create    # صفحة إضافة إعلان
POST   /admin/announcements/store     # حفظ إعلان جديد
GET    /admin/announcements/{id}/edit # صفحة تعديل إعلان (Admin)
POST   /admin/announcements/{id}/update # تحديث إعلان (Admin)
GET    /admin/announcements/{id}/delete # حذف إعلان (Admin)
```

### إدارة المشرفين
```
GET    /admin/managers           # قائمة المشرفين
GET    /admin/managers/create    # صفحة إضافة مشرف
POST   /admin/managers/store     # حفظ مشرف جديد
GET    /admin/managers/{id}/edit # صفحة تعديل مشرف
POST   /admin/managers/update    # تحديث مشرف
GET    /admin/managers/{id}/delete # حذف مشرف
```

### إعدادات الموقع (Admin فقط)
```
GET  /admin/settings             # صفحة الإعدادات
POST /admin/settings/update      # حفظ الإعدادات
```

| الحقل في POST | الوصف |
|--------------|-------|
| setting_{key} | قيمة الإعداد (لكل إعداد في قاعدة البيانات) |
| delete_image_{key} | لحذف صورة (1 أو 0) |

---

## الملخص - جميع المسارات

| # | المسار | HTTP | المصادقة | Admin فقط |
|---|--------|------|---------|-----------|
| 1 | `/` | GET | - | - |
| 2 | `/home` | GET | - | - |
| 3 | `/login` | GET | - | - |
| 4 | `/login/post` | POST | - | - |
| 5 | `/logout` | GET | - | - |
| 6 | `/courses` | GET | - | - |
| 7 | `/courses/{id}` | GET | - | - |
| 8 | `/courses/{id}/rate` | POST | - | - |
| 9 | `/courses/{id}/files` | GET | - | - |
| 10 | `/majors/{id}` | GET | - | - |
| 11 | `/search` | GET | - | - |
| 12 | `/about` | GET | - | - |
| 13 | `/contact` | GET | - | - |
| 14 | `/announcements` | GET | - | - |
| 15 | `/announcements/{id}` | GET | - | - |
| 16 | `/files/{id}/download` | GET | - | - |
| 17 | `/files/{id}/preview` | GET | - | - |
| 18 | `/admin/dashboard` | GET | نعم | - |
| 19 | `/admin/majors` | GET | نعم | - |
| 20 | `/admin/majors/create` | GET | نعم | نعم |
| 21 | `/admin/majors/store` | POST | نعم | نعم |
| 22 | `/admin/majors/{id}/edit` | GET | نعم | نعم |
| 23 | `/admin/majors/{id}/update` | POST | نعم | نعم |
| 24 | `/admin/majors/{id}/delete` | GET | نعم | نعم |
| 25 | `/admin/levels` | GET | نعم | - |
| 26 | `/admin/levels/create` | GET | نعم | نعم |
| 27 | `/admin/levels/store` | POST | نعم | نعم |
| 28 | `/admin/levels/{id}/edit` | GET | نعم | نعم |
| 29 | `/admin/levels/{id}/update` | POST | نعم | نعم |
| 30 | `/admin/levels/{id}/delete` | GET | نعم | نعم |
| 31 | `/admin/semesters` | GET | نعم | - |
| 32 | `/admin/semesters/create` | GET | نعم | نعم |
| 33 | `/admin/semesters/store` | POST | نعم | نعم |
| 34 | `/admin/semesters/{id}/edit` | GET | نعم | نعم |
| 35 | `/admin/semesters/{id}/update` | POST | نعم | نعم |
| 36 | `/admin/semesters/{id}/delete` | GET | نعم | نعم |
| 37 | `/admin/courses` | GET | نعم | - |
| 38 | `/admin/courses/create` | GET | نعم | - |
| 39 | `/admin/courses/store` | POST | نعم | - |
| 40 | `/admin/courses/{id}/edit` | GET | نعم | - |
| 41 | `/admin/courses/{id}/update` | POST | نعم | - |
| 42 | `/admin/courses/{id}/delete` | GET | نعم | - |
| 43 | `/admin/files` | GET | نعم | - |
| 44 | `/admin/files/create` | GET | نعم | - |
| 45 | `/admin/files/store` | POST | نعم | - |
| 46 | `/admin/files/{id}/edit` | GET | نعم | - |
| 47 | `/admin/files/{id}/update` | POST | نعم | - |
| 48 | `/admin/files/{id}/delete` | GET | نعم | - |
| 49 | `/admin/files/{id}/toggle` | GET | نعم | - |
| 50 | `/admin/users` | GET | نعم | نعم |
| 51 | `/admin/users/create` | GET | نعم | نعم |
| 52 | `/admin/users/store` | POST | نعم | نعم |
| 53 | `/admin/users/{id}/edit` | GET | نعم | نعم |
| 54 | `/admin/users/{id}/update` | POST | نعم | نعم |
| 55 | `/admin/users/{id}/delete` | GET | نعم | نعم |
| 56 | `/admin/announcements` | GET | نعم | - |
| 57 | `/admin/announcements/create` | GET | نعم | - |
| 58 | `/admin/announcements/store` | POST | نعم | - |
| 59 | `/admin/announcements/{id}/edit` | GET | نعم | نعم |
| 60 | `/admin/announcements/{id}/update` | POST | نعم | نعم |
| 61 | `/admin/announcements/{id}/delete` | GET | نعم | نعم |
| 62 | `/admin/settings` | GET | نعم | نعم |
| 63 | `/admin/settings/update` | POST | نعم | نعم |
| 64 | `/admin/managers` | GET | نعم | - |
| 65 | `/admin/managers/create` | GET | نعم | - |
| 66 | `/admin/managers/store` | POST | نعم | - |
| 67 | `/admin/managers/{id}/edit` | GET | نعم | - |
| 68 | `/admin/managers/update` | POST | نعم | - |
| 69 | `/admin/managers/{id}/delete` | GET | نعم | - |
