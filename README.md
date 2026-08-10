<h1 align="center">🎮 Unified PlayStation & Café POS System ☕</h1>
<p align="center">
  <b>نظام نقاط البيع الموحد لإدارة صالات البلايستيشن والكافيهات</b><br>
  <i>A unified single-screen POS management system built for commercial PlayStation lounges and cafés.</i>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/RTL-Arabic_Native-10B981?style=for-the-badge" alt="Arabic RTL">
</p>

---

## 📌 نبذة عن المشروع (Project Overview)

يُعد **نظام نقاط البيع الموحد (Unified POS System)** حلاً برمجياً متكاملاً وفائق السرعة تم تطويره خصيصاً ليناسب متطلبات **صالات البلايستيشن والكافيهات المدمجة** في السوق المصري والوطن العربي. 

يقوم النظام على رؤية **"إدارة الشاشة الواحدة" (Single-Screen Management)**، حيث يجمع بين:
1. **تتبع جلسات البلايستيشن اللحظية** وحساب وقت اللعب بالدقيقة والثانية (Single / Multi).
2. **محاسبة طاولات الكافيه ومبيعات التيك أواي السريعة**.
3. **الخصم التلقائي اللحظي من المخزون** عند بيع أي صنف.
4. **تجميع كافة الخدمات والمشروبات في فاتورة حرارية واحدة موحدة**.

---

## ⚡ المميزات الرئيسية (Key Features)

- 🖥️ **نظام نقاط بيع موحد (Single-Screen Unified POS):** شاشة رئيسية واحدة تسمح لمسؤول الكاشير بإدارة الأجهزة، الطاولات، والتيك أواي دون الحاجة للتنقل بين الصفحات.
- 🎮 **إدارة جلسات البلايستيشن (PlayStation Session Tracking):**
  - دعم الجلسات المفتوحة (Open Sessions) والجلسات مسبوقة الدفع (Prepaid Sessions).
  - التبديل المرن بين تسعيرة اللعب الفردي (Single) والجماعي (Multi) خلال نفس الجلسة.
  - حساب آلي دقيق للوقت والتكلفة بالدقيقة والثانية.
- ☕ **إدارة طاولات الكافيه والتيك أواي (Café Tables & Takeaway):** محاسبة مستقلة لطاولات الصالة الخارجية والطلبات السريعة على الكاونتر.
- 📦 **الخصم التلقائي للمخزون (Real-Time Inventory Control):** خصم فوري لرصيد المنتجات فور بيعها مع تنبيهات ذكية بنواقص المخزون لمنع النفاذ.
- 💸 **تسجيل المصروفات والنثريات (Expense Management):** خصم المصروفات اليومية تلقائياً للحصول على **صافي الدخل النهائي للوردية**.
- 🎨 **واجهة لمسية فاتحة عالية التباين (High-Contrast Light Theme):** تصميم مريح للعين يعتمد على **النص الأسود الداكن صريح الوضوح** مع دعم كامل للغة العربية والاتجاه من اليمين إلى اليسار (Arabic RTL).

---

## 🛠️ التقنيات المستخدمة (Tech Stack)

### 🖥️ البنية التحتية والهيكلية (Architecture Layout)
المشروع مقسم إلى مجلدين رئيسيين لسهولة الصيانة والتأطير البرمجي:
- **`backend/`**: يحتوي على كود Laravel 11، قاعدة البيانات، المسارات، والاختبارات.
- **`frontend/`**: يحتوي على واجهات المستخدم (Blade Views)، ملفات الأنماط (CSS/Tailwind)، وتكاوين بناء الأصول (Vite).

```
coffe_ps_system/
├── backend/                  # Laravel 11 Application Core
│   ├── app/                  # Controllers, Models, Services & Middleware
│   ├── config/               # System & View Configurations
│   ├── database/             # Migrations, Factories & Seeders
│   ├── routes/               # Web & Console Routes
│   ├── storage/              # Cache & App Storage
│   └── tests/                # Feature & Unit Test Suite
│
├── frontend/                 # UI Templates & Asset Management
│   ├── views/                # Blade Templates (POS, Reports, Sessions, etc.)
│   ├── css/                  # Application Stylesheets
│   └── js/                   # Alpine.js & JavaScript Modules
│
└── PROJECT_PROFILE.md        # Comprehensive System Documentation
```

### ⚙️ التقنيات الأساسية:
| الطبقة | التقنيات |
| :--- | :--- |
| **Backend Framework** | PHP 8.2+ / Laravel 11 (MVC Architecture) |
| **Database** | MySQL / SQLite (with `DB::transaction` financial locks) |
| **Frontend UI** | Blade Templates, Alpine.js, Tailwind CSS |
| **Testing Suite** | PHPUnit 11 (46 Feature & Unit Tests) |
| **Receipt Printing** | Direct Thermal Printer Engine (80mm / 58mm) |

---

## 🚀 خطوات التثبيت والتشغيل (Installation & Setup Guide)

### 📋 متطلبات التشغيل (Prerequisites):
- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite / MySQL Database

---

### 1️⃣ استنساخ المستودع (Clone Repository):
```bash
git clone https://github.com/mohanadhassan110/coffe_ps_system.git
cd coffe_ps_system
```

---

### 2️⃣ اعداد وتجهيز الباك إند (Setup Backend):
```bash
# الانتقال لمجلد الباك إند
cd backend

# تثبيت الحزم والمكتبات البرمجية
composer install

# إنشاء ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# تشغيل قواعد البيانات وتغذيتها بالبيانات الأولية
php artisan migrate --seed

# تشغيل خادم التطبيق المحتلي
php artisan serve
```
> الخادم سيعمل على العنوان المحلي: `http://127.0.0.1:8000`

---

### 3️⃣ اعداد وتجهيز الواجهة والأصول (Setup Frontend):
من نافذة مبوبة أخرى في التيرمينال:
```bash
# الانتقال لمجلد الواجهة
cd frontend

# تثبيت حزم الجافاسكريبت
npm install

# تشغيل خادم التطوير اللحظي للأصول
npm run dev
```

---

## 🧪 تشغيل الاختبارات الآلية (Running Automated Tests)

يحتوي النظام على **46 اختباراً آلياً** مخصصاً لضمان صحة المعاملات الحسابية، تتبع الوقت، الخصم الآلي من المخزن، وسلامة تقارير التقفيل اليومي.

لتشغيل الاختبارات:
```bash
cd backend
vendor/bin/phpunit
```

---

## 📸 معاينة واجهات النظام (UI Screenshots & Walkthrough)

### 🖥️ 1. الشاشة الرئيسية الموحدة لنقاط البيع (Unified POS Screen)
تجمع الأجهزة النشطة، طاولات الكافيه، وقائمة المنتجات اللمسية في واجهة واحدة بدون تنقل.

---

### 📊 2. تقرير إغلاق الوردية والتحليلات اليومية (Shift Closing & Reports)
تقارير شفافة تفصل بين دخل البلايستيشن، دخل الكافيه، والمصروفات لحساب صافي الأرباح الصافي.

---

## 📄 التوثيق الشامل (Comprehensive System Profile)
لمزيد من التفاصيل الفنية، التخطيط الهيكلي، ودراسة الجدوى التشغيلية، راجع الملف التوثيقي:
📄 [PROJECT_PROFILE.md](./PROJECT_PROFILE.md)

---

## 📝 الترخيص (License)
هذا المشروع مرخص بموجب [MIT License](LICENSE).
