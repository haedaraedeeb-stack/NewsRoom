# NewsRoom — Internal News Platform

> منصة أخبار داخلية لشركة TechNova مبنية بـ Laravel 13

---

## Setup Steps

### Requirements
- PHP 8.3+
- MySQL
- Redis
- Composer

### Installation

```bash
# 1. Clone the project
git clone https://github.com/haedaraedeeb-stack/NewsRoom
cd NewsRoom

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure .env
DB_CONNECTION=mysql
DB_DATABASE=newsroom
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

SESSION_DRIVER=database

# 6. Run migrations
php artisan migrate

# 7. Seed the database
php artisan db:seed

# 8. Run the queue worker
php artisan queue:work redis --queue=notifications,reports

# 9. Run the scheduler (production)
php artisan schedule:work

# 10. Serve the application
php artisan serve
```

---

## Entities & Relationships

### Entities

| Entity | Description |
|--------|-------------|
| `User` | المستخدم — roles: admin / writer / reader |
| `UserProfile` | بيانات إضافية للمستخدم |
| `Article` | المقال — ينشئه writer ويراجعه admin |
| `Comment` | تعليق على أي entity |
| `Attachment` | مرفق على أي entity |
| `Tag` | وسم مشترك بين أكثر من نوع محتوى |

### Relationships

```
User
├── hasOne      → UserProfile
├── hasMany     → Article
└── hasMany     → Comment

Article
├── belongsTo   → User
├── morphMany   → Comment      (commentable)
├── morphMany   → Attachment   (attachable)
└── belongsToMany → Tag        (article_tag pivot)

UserProfile
└── morphMany   → Attachment   (attachable)

Comment
└── morphTo     → commentable  (Article + any future entity)

Attachment
└── morphTo     → attachable   (Article + UserProfile + any future entity)

Tag
└── belongsToMany → Article
```

---

## Architectural Decisions

### 1. Repository Pattern

**القرار:** استخدام `Controller → Service → Repository → Model`

**السبب:** فصل مسؤوليات الطبقات — لو تغير مصدر البيانات من MySQL لـ MongoDB، بس تغير الـ Repository بدون ما تلمس الـ Controllers أو الـ Services.

**البديل:** كتابة الـ queries مباشرة في الـ Controllers — أسرع في البداية لكن صعب الصيانة والاختبار.

---

### 2. Polymorphic Relations للـ Comments و Attachments

**القرار:** استخدام `morphMany` بدل `hasMany`

**السبب:** المتطلبات قالت إن أي entity مستقبلية ممكن تحتوي comments أو attachments — الـ Polymorphic يسمح بهاد بدون تغيير الجدول.

**البديل:** إنشاء جدول منفصل لكل entity (article_comments, user_attachments...) — يكسر الـ scalability.

---

### 3. Contextual Binding للإشعارات

**القرار:** فصل الإشعارات لـ `DatabaseNotificationService` و `EmailNotificationService` عبر Interface

**السبب:** Admin يستقبل إشعار داخلي — Writer يستقبل Email. نفس الحدث، سلوك مختلف. الـ Container يقرر من يأخذ أي Implementation.

**البديل:** `if/else` في `via()` داخل الـ Notification — يعمل لكن يكسر مبدأ Single Responsibility.

---

### 4. Jobs + Queues للعمليات الثقيلة

**القرار:** إرسال الإشعارات عبر `ShouldQueue` على queue منفصل `notifications`

**السبب:** إرسال إشعار لكل المشتركين عملية ثقيلة — لو صارت synchronous بتوقف الـ response.

**البديل:** إرسال مباشر في الـ Service — أبسط لكن يبطئ الـ API.

---

### 5. Cache + Lock لمشكلة الذروة

**القرار:** `Cache::remember()` + `Cache::lock()` معاً للـ Dashboard

**السبب:** لو 300 مستخدم طلبوا الـ Dashboard بنفس اللحظة وخلص الكاش — بدون Lock كلهم بيضربون الـ DB. الـ Lock يسمح لواحد فقط يبني الكاش والباقين ينتظروا.

**البديل:** `Cache::remember()` فقط — يحل المشكلة في الحالات العادية لكن ما يحمي من الذروة (Cache Stampede).

---

### 6. API Versioning

**القرار:** `v1` و `v2` عبر Route prefix + منفصل Controllers + Resources

**السبب:** الـ Mobile App بدو بيانات إضافية (tags, comments_count, reading_time) بدون ما يكسر الـ Web App الحالي.

**البديل:** إضافة parameters للـ request لتحديد الـ version — أقل وضوحاً وأصعب صيانة.

---

### 7. Observer للـ Cache Invalidation

**القرار:** `ArticleObserver` يمسح الكاش تلقائياً عند أي تغيير على المقال

**السبب:** بدون Observer لازم تكتب `Cache::forget()` في كل دالة (create, update, delete) — مكرر وقابل للنسيان.

**البديل:** Cache Invalidation يدوي في الـ Service — يعمل لكن غير موثوق على المدى البعيد.

---

### 8. Events + Listeners للـ Welcome Mail

**القرار:** `UserRegisteredEvent` + `SendWelcomeMailListener`

**السبب:** رسالة الترحيب استجابة تلقائية لحدث واحد — Events أنسب من Jobs هنا لأنها خفيفة ومرتبطة بحدث محدد.

**البديل:** استدعاء الـ Mail مباشرة في الـ Service — يعمل لكن يربط الـ Service بمنطق الإشعارات.

---

## API Endpoints

### Auth
```
POST   /api/register
POST   /api/login
POST   /api/logout        (auth required)
```

### Articles V1
```
GET    /api/v1/articles
POST   /api/v1/articles
GET    /api/v1/articles/{id}
PUT    /api/v1/articles/{id}
DELETE /api/v1/articles/{id}
PATCH  /api/v1/articles/{id}/publish
PATCH  /api/v1/articles/{id}/archive
```

### Articles V2
```
GET    /api/v2/articles       (+ tags, comments_count, reading_time)
GET    /api/v2/articles/{id}
```

### Comments
```
GET    /api/articles/{articleId}/comments
POST   /api/articles/{articleId}/comments  (auth required)
PUT    /api/comments/{id}                  (auth required)
DELETE /api/comments/{id}                  (auth required)
```

### Dashboard
```
GET    /api/dashboard
```

---

## Artisan Commands

```bash
# Archive unpublished articles older than N days (default: 30)
php artisan articles:archive
php artisan articles:archive 60
php artisan articles:archive --dry-run

# Generate monthly report per writer
php artisan articles:report
```

### Scheduler
```
articles:archive  → runs monthly (1st of each month)
articles:report   → runs every Friday at 08:00
```
