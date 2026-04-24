<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }}</title>
    <style>
        :root {
            --bg: #efe7d8;
            --bg-2: #f8f4ec;
            --card: #fffdf8;
            --line: #ded2be;
            --ink: #1f2937;
            --muted: #6b7280;
            --brand: #0f766e;
            --brand-2: #155e75;
            --brand-soft: #ddf2ee;
            --accent: #b45309;
            --accent-soft: #fff1e2;
            --shadow: 0 18px 42px rgba(53, 42, 24, 0.08);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.11), transparent 30%),
                radial-gradient(circle at left center, rgba(180, 83, 9, 0.09), transparent 24%),
                linear-gradient(180deg, #f7f1e8 0%, #f1e9dc 100%);
        }

        .shell {
            width: min(1380px, calc(100% - 28px));
            margin: 18px auto 28px;
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 20px;
        }

        .sidebar,
        .panel,
        .hero {
            box-shadow: var(--shadow);
        }

        .sidebar {
            align-self: start;
            position: sticky;
            top: 18px;
            display: grid;
            gap: 16px;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 18px;
        }

        .hero {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: #fff;
            border-radius: var(--radius-xl);
            padding: 26px;
            overflow: hidden;
            position: relative;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto -60px -90px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero h1 {
            margin: 10px 0 8px;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.15;
        }

        .hero p {
            margin: 0;
            max-width: 840px;
            line-height: 1.9;
            color: rgba(255, 255, 255, 0.92);
        }

        .hero-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .layout {
            display: grid;
            gap: 18px;
        }

        .kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .kpi {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .kpi strong {
            display: block;
            font-size: 30px;
            margin-bottom: 6px;
        }

        .kpi span {
            color: var(--muted);
            font-size: 14px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1.25fr .95fr;
            gap: 18px;
        }

        .panel h2,
        .panel h3 {
            margin: 0 0 14px;
            font-size: 19px;
        }

        .panel p {
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
        }

        .stack {
            display: grid;
            gap: 16px;
        }

        .account-list,
        .quick-links,
        .forms-grid {
            display: grid;
            gap: 12px;
        }

        .account {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: var(--radius-md);
            padding: 14px;
        }

        .account header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .meta,
        .hint,
        .muted {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        form {
            display: grid;
            gap: 12px;
        }

        .grid-2,
        .grid-3 {
            display: grid;
            gap: 12px;
        }

        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

        label {
            display: block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #d6c9b4;
            border-radius: 14px;
            background: #fff;
            padding: 11px 12px;
            font: inherit;
            color: var(--ink);
        }

        textarea {
            min-height: 92px;
            resize: vertical;
        }

        button {
            border: 0;
            border-radius: 14px;
            padding: 11px 14px;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        .primary {
            background: var(--brand);
            color: #fff;
        }

        .secondary {
            background: var(--brand-soft);
            color: #0f5b55;
        }

        .ghost {
            background: #f6efe2;
            color: #765321;
        }

        .danger-soft {
            background: #ffe9e3;
            color: #a63d1c;
        }

        .actions,
        .toolbar,
        .segmented {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .segmented button.active {
            background: var(--brand);
            color: #fff;
        }

        .session-box {
            border: 1px dashed #c8b89f;
            background: var(--bg-2);
            border-radius: var(--radius-md);
            padding: 14px;
        }

        .permissions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .perm {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #edf5f4;
            color: #0f5b55;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .data-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            text-align: right;
            border-bottom: 1px solid #efe6d8;
            padding: 12px 14px;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: #f9f4eb;
            color: #6c6253;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr:hover td {
            background: #fffcf6;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 9px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge.ok { background: #e6f7ef; color: #166534; }
        .badge.warn { background: #fff1e2; color: #b45309; }
        .badge.muted { background: #edf0f4; color: #4b5563; }

        .console {
            background: #101725;
            color: #e6eef9;
            border-radius: var(--radius-md);
            padding: 15px;
            min-height: 230px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
            margin: 0;
        }

        .form-card {
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 14px;
            background: #fffdfa;
        }

        .stats-card {
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 14px;
            background: linear-gradient(135deg, #fffdf7, #f5f1e8);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .stats-grid .mini {
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e8dcc9;
            padding: 12px;
        }

        .stats-grid strong {
            display: block;
            font-size: 24px;
            margin-bottom: 4px;
        }

        @media (max-width: 1180px) {
            .shell,
            .main-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }
        }

        @media (max-width: 840px) {
            .kpis,
            .grid-2,
            .grid-3,
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <section class="panel">
                <h2>تسجيل الدخول</h2>
                <p class="hint">
                    هذه الواجهة تتحدث مع Laravel API مباشرة. اضغط على أي حساب تجريبي ليتم تعبئة الحقول، ثم سجّل الدخول.
                </p>
                <form id="login-form" style="margin-top: 14px;">
                    <div>
                        <label for="identifier">البريد أو الجوال</label>
                        <input id="identifier" name="identifier" value="{{ $demoAccounts[0]['email'] }}" />
                    </div>
                    <div>
                        <label for="password">كلمة المرور</label>
                        <input id="password" name="password" type="password" value="{{ $demoAccounts[0]['password'] }}" />
                    </div>
                    <div>
                        <label for="school-id">School ID</label>
                        <input id="school-id" name="school_id" value="{{ $demoSchool['id'] }}" />
                    </div>
                    <div class="actions">
                        <button class="primary" type="submit">تسجيل الدخول</button>
                        <button class="ghost" type="button" id="logout-btn">تسجيل الخروج</button>
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>الحسابات التجريبية</h2>
                <div class="account-list">
                    @foreach ($demoAccounts as $account)
                        <article class="account">
                            <header>
                                <strong>{{ $account['label'] }}</strong>
                                <button
                                    class="ghost use-account"
                                    type="button"
                                    data-email="{{ $account['email'] }}"
                                    data-password="{{ $account['password'] }}"
                                    data-school-id="{{ $account['school_id'] ?? '' }}"
                                >استخدام</button>
                            </header>
                            <div class="meta">{{ $account['email'] }}</div>
                            <div class="meta">{{ $account['password'] }}</div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <h2>الجلسة الحالية</h2>
                <div class="session-box" id="session-summary">
                    لا توجد جلسة فعالة بعد.
                </div>
            </section>
        </aside>

        <main class="layout">
            <section class="hero">
                <div class="pill">Browser Demo جاهز</div>
                <h1>تجربة تشغيل النظام من المتصفح</h1>
                <p>
                    هذه واجهة تشغيل مؤقتة فوق Laravel حتى نختبر النظام فعليًا قبل اكتمال واجهة React.
                    يمكنك استعراض المدارس والمستخدمين والطلاب والخطط الفردية IEP والمحادثات الداخلية والإشعارات والتقارير والملفات، وإنشاء سجلات جديدة، وربط ولي أمر بطالب، وتحريك دورة اعتماد الخطة وإرسال الرسائل ورفع ملفات حقيقية وقراءة المؤشرات من نفس الشاشة عبر الـ API الحقيقي الجاري تشغيله الآن.
                </p>
                <div class="hero-meta">
                    <div class="pill">URL: http://127.0.0.1:8000</div>
                    <div class="pill">School ID الافتراضي: {{ $demoSchool['id'] }}</div>
                    <div class="pill">المدرسة: {{ $demoSchool['name_ar'] }}</div>
                </div>
            </section>

            <section class="kpis">
                <article class="kpi">
                    <strong>{{ $stats['schools'] }}</strong>
                    <span>المدارس</span>
                </article>
                <article class="kpi">
                    <strong>{{ $stats['users'] }}</strong>
                    <span>المستخدمون</span>
                </article>
                <article class="kpi">
                    <strong>{{ $stats['students'] }}</strong>
                    <span>الطلاب</span>
                </article>
                <article class="kpi">
                    <strong>{{ $stats['iep_plans'] }}</strong>
                    <span>خطط IEP</span>
                </article>
                <article class="kpi">
                    <strong>{{ $stats['files'] }}</strong>
                    <span>الملفات</span>
                </article>
            </section>

            <section class="main-grid">
                <div class="stack">
                    <section class="panel">
                        <h2>مستكشف البيانات</h2>
                        <div class="toolbar" style="margin-bottom: 14px;">
                            <button class="secondary" type="button" id="load-all-btn">تحميل الكل</button>
                            <button class="ghost" type="button" data-dataset="schools">المدارس</button>
                            <button class="ghost" type="button" data-dataset="users">المستخدمون</button>
                            <button class="ghost" type="button" data-dataset="students">الطلاب</button>
                            <button class="ghost" type="button" data-dataset="iep-plans">خطط IEP</button>
                            <button class="ghost" type="button" data-dataset="messages">الرسائل</button>
                            <button class="ghost" type="button" data-dataset="notifications">الإشعارات</button>
                            <button class="ghost" type="button" data-dataset="files">الملفات</button>
                            <button class="ghost" type="button" data-dataset="reports">التقارير</button>
                            <button class="ghost" type="button" id="load-me-btn">المستخدم الحالي</button>
                        </div>
                        <div class="segmented" id="dataset-switcher">
                            <button class="active" type="button" data-table="schools">جدول المدارس</button>
                            <button type="button" data-table="users">جدول المستخدمين</button>
                            <button type="button" data-table="students">جدول الطلاب</button>
                            <button type="button" data-table="iep-plans">جدول IEP</button>
                            <button type="button" data-table="messages">جدول الرسائل</button>
                            <button type="button" data-table="notifications">جدول الإشعارات</button>
                            <button type="button" data-table="files">جدول الملفات</button>
                            <button type="button" data-table="reports">جدول التقارير</button>
                        </div>
                        <div class="hint" id="dataset-caption" style="margin: 14px 0;">
                            اضغط "تحميل الكل" بعد تسجيل الدخول، أو اختر مجموعة بيانات محددة.
                        </div>
                        <div class="data-wrap">
                            <table id="data-table">
                                <thead></thead>
                                <tbody>
                                    <tr>
                                        <td>بانتظار تحميل البيانات...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="panel">
                        <h2>نتيجة آخر تفاعل</h2>
                        <pre class="console" id="console-output">بانتظار أول طلب...</pre>
                    </section>
                </div>

                <div class="stack">
                    <section class="panel">
                        <h2>نظرة تشغيلية</h2>
                        <div class="stats-card">
                            <div class="hint">يمكنك تحميل إحصاءات المدرسة المحددة عند امتلاك الصلاحية المناسبة.</div>
                            <div class="actions" style="margin-top: 12px;">
                                <button class="secondary" type="button" id="school-stats-btn">إحصاءات المدرسة الحالية</button>
                                <button class="ghost" type="button" id="notifications-read-all-btn">تعليم كل الإشعارات كمقروءة</button>
                            </div>
                            <div class="stats-grid" id="school-stats-grid">
                                <div class="mini">
                                    <strong>-</strong>
                                    <span class="muted">الطلاب</span>
                                </div>
                                <div class="mini">
                                    <strong>-</strong>
                                    <span class="muted">المعلمون</span>
                                </div>
                                <div class="mini">
                                    <strong>-</strong>
                                    <span class="muted">الخطط</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="panel">
                        <h2>عمليات النظام من المتصفح</h2>
                        <div class="forms-grid">
                            <article class="form-card">
                                <h3>إنشاء مدرسة</h3>
                                <form id="school-form">
                                    <div class="grid-2">
                                        <div>
                                            <label>اسم المدرسة</label>
                                            <input name="name" value="مدرسة تجريبية جديدة لذوي الإعاقة" />
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>المرحلة</label>
                                            <select name="stage">
                                                <option value="ابتدائي" selected>ابتدائي</option>
                                                <option value="متوسط">متوسط</option>
                                                <option value="ثانوي">ثانوي</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label>نوع البرنامج</label>
                                            <select name="program_type">
                                                <option value="يسير التعليمي" selected>يسير التعليمي</option>
                                                <option value="فرط حركة وتشتت انتباه">فرط حركة وتشتت انتباه</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>خط العرض</label>
                                            <input name="location_lat" value="24.7135517" />
                                        </div>
                                        <div>
                                            <label>خط الطول</label>
                                            <input name="location_lng" value="46.6752957" />
                                        </div>
                                    </div>
                                    <button class="primary" type="submit">إنشاء مدرسة</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>إنشاء مستخدم</h3>
                                <form id="user-form">
                                    <div class="grid-2">
                                        <div>
                                            <label>الاسم الكامل</label>
                                            <input name="full_name" value="مستخدم متصفح جديد" />
                                        </div>
                                        <div>
                                            <label>الدور</label>
                                            <select name="role" id="user-role-select"></select>
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>البريد</label>
                                            <input name="email" value="browser.user@maak.local" />
                                        </div>
                                        <div>
                                            <label>الجوال</label>
                                            <input name="phone" value="0551111111" />
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>School ID</label>
                                            <input name="school_id" value="{{ $demoSchool['id'] }}" />
                                        </div>
                                        <div>
                                            <label>كلمة المرور</label>
                                            <input name="password" value="Password@123" />
                                        </div>
                                    </div>
                                    <button class="primary" type="submit">إنشاء مستخدم</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>إنشاء طالب</h3>
                                <form id="student-form">
                                    <div class="grid-3">
                                        <div>
                                            <label>الاسم الأول</label>
                                            <input name="first_name" value="ليان" />
                                        </div>
                                        <div>
                                            <label>اسم الأب</label>
                                            <input name="father_name" value="خالد" />
                                        </div>
                                        <div>
                                            <label>اسم العائلة</label>
                                            <input name="family_name" value="العتيبي" />
                                        </div>
                                    </div>
                                    <div class="grid-3">
                                        <div>
                                            <label>School ID</label>
                                            <input name="school_id" value="{{ $demoSchool['id'] }}" />
                                        </div>
                                        <div>
                                            <label>النوع</label>
                                            <select name="gender">
                                                <option value="female">female</option>
                                                <option value="male">male</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label>رقم الطالب</label>
                                            <input name="student_number" value="ST-BROWSER-01" />
                                        </div>
                                    </div>
                                    <div class="grid-3">
                                        <div>
                                            <label>الصف</label>
                                            <input name="grade_level" value="الثالث" />
                                        </div>
                                        <div>
                                            <label>الفصل</label>
                                            <input name="classroom" value="3B" />
                                        </div>
                                        <div>
                                            <label>تاريخ الالتحاق</label>
                                            <input name="joined_at" type="date" value="2026-04-18" />
                                        </div>
                                    </div>
                                    <div class="grid-3">
                                        <div>
                                            <label>السنة الدراسية</label>
                                            <select name="academic_year_id" id="academic-year-select"></select>
                                        </div>
                                        <div>
                                            <label>البرنامج</label>
                                            <select name="education_program_id" id="program-select"></select>
                                        </div>
                                        <div>
                                            <label>الإعاقة</label>
                                            <select name="disability_category_id" id="disability-select"></select>
                                        </div>
                                    </div>
                                    <div>
                                        <label>المعلم الأساسي</label>
                                        <select name="primary_teacher_user_id" id="teacher-select"></select>
                                    </div>
                                    <button class="primary" type="submit">إنشاء طالب</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>ربط ولي أمر</h3>
                                <form id="guardian-form">
                                    <div class="grid-2">
                                        <div>
                                            <label>الطالب</label>
                                            <select name="student_id" id="guardian-student-select"></select>
                                        </div>
                                        <div>
                                            <label>ولي الأمر</label>
                                            <select name="parent_user_id" id="guardian-parent-select"></select>
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>العلاقة</label>
                                            <input name="relationship" value="mother" />
                                        </div>
                                        <div>
                                            <label>أساسي؟</label>
                                            <select name="is_primary">
                                                <option value="true">true</option>
                                                <option value="false">false</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button class="primary" type="submit">ربط ولي أمر</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>إنشاء خطة IEP</h3>
                                <p class="hint" style="margin-bottom: 12px;">
                                    ابدأ من حساب المعلم لإنشاء الخطة ثم انتقل إلى المدير وبعدها المشرف لتجربة دورة الاعتماد كاملة من الجدول.
                                </p>
                                <form id="iep-form">
                                    <div class="grid-2">
                                        <div>
                                            <label>الطالب</label>
                                            <select name="student_id" id="iep-student-select"></select>
                                        </div>
                                        <div>
                                            <label>السنة الدراسية</label>
                                            <select name="academic_year_id" id="iep-academic-year-select"></select>
                                        </div>
                                    </div>
                                    <div>
                                        <label>عنوان الخطة</label>
                                        <input name="title" value="الخطة الفردية التجريبية" />
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>تاريخ البداية</label>
                                            <input name="start_date" type="date" value="2026-04-18" />
                                        </div>
                                        <div>
                                            <label>تاريخ النهاية</label>
                                            <input name="end_date" type="date" value="2026-06-18" />
                                        </div>
                                    </div>
                                    <div>
                                        <label>الملخص</label>
                                        <textarea name="summary">خطة فردية تجريبية لرفع مهارات الطالب الأكاديمية والسلوكية خلال الفصل الحالي.</textarea>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>نقاط القوة</label>
                                            <textarea name="strengths">استجابة جيدة للتعليمات البصرية، واندماج إيجابي داخل الحصة.</textarea>
                                        </div>
                                        <div>
                                            <label>الاحتياجات</label>
                                            <textarea name="needs">تعزيز الاستقلالية، وتنظيم المهام، وتحسين التواصل أثناء النشاط.</textarea>
                                        </div>
                                    </div>
                                    <div>
                                        <label>التسهيلات مفصولة بفاصلة</label>
                                        <input name="accommodations_text" value="وقت إضافي, تعليمات مبسطة, دعم بصري" />
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>مجال الهدف الأول</label>
                                            <input name="goal_domain" value="التواصل" />
                                        </div>
                                        <div>
                                            <label>تاريخ استحقاق الهدف</label>
                                            <input name="goal_due_date" type="date" value="2026-06-01" />
                                        </div>
                                    </div>
                                    <div>
                                        <label>نص الهدف الأول</label>
                                        <textarea name="goal_text">أن يستخدم الطالب جملة من ثلاث كلمات للتعبير عن احتياجه في 4 من 5 مواقف صفية.</textarea>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>طريقة القياس</label>
                                            <input name="goal_measurement_method" value="ملاحظة أسبوعية وقائمة متابعة" />
                                        </div>
                                        <div>
                                            <label>القيمة المستهدفة</label>
                                            <input name="goal_target_value" value="80%" />
                                        </div>
                                    </div>
                                    <button class="primary" type="submit">إنشاء خطة IEP</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>إرسال رسالة داخلية</h3>
                                <p class="hint" style="margin-bottom: 12px;">
                                    اترك مفتاح السلسلة فارغًا لبدء محادثة جديدة، أو افتح سلسلة من الجدول وسيتم تعبئته تلقائيًا للرد داخلها.
                                </p>
                                <form id="message-form">
                                    <div class="grid-2">
                                        <div>
                                            <label>المستلم</label>
                                            <select name="recipient_id" id="message-recipient-select"></select>
                                        </div>
                                        <div>
                                            <label>مفتاح السلسلة</label>
                                            <input name="thread_key" id="message-thread-key" placeholder="اختياري" />
                                        </div>
                                    </div>
                                    <div>
                                        <label>الموضوع</label>
                                        <input name="subject" value="متابعة تشغيلية" />
                                    </div>
                                    <div>
                                        <label>نص الرسالة</label>
                                        <textarea name="body">هذه رسالة تجريبية من واجهة المتصفح للتحقق من وحدة المراسلات الداخلية.</textarea>
                                    </div>
                                    <button class="primary" type="submit">إرسال الرسالة</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>رفع ملف</h3>
                                <p class="hint" style="margin-bottom: 12px;">
                                    ارفع ملفًا حقيقيًا من جهازك، ثم أنشئ له رابط تحميل مؤقت من الجدول أو اعرض بياناته الوصفية مباشرة.
                                </p>
                                <form id="file-form">
                                    <div>
                                        <label>الملف</label>
                                        <input name="file" id="file-upload-input" type="file" />
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>الفئة</label>
                                            <select name="category">
                                                <option value="portfolio">portfolio</option>
                                                <option value="iep_attachment">iep_attachment</option>
                                                <option value="student_report">student_report</option>
                                                <option value="supervision">supervision</option>
                                                <option value="general">general</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label>مستوى الظهور</label>
                                            <select name="visibility">
                                                <option value="private">private</option>
                                                <option value="school">school</option>
                                                <option value="public">public</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>المعرف المرتبط</label>
                                            <input name="related_id" placeholder="اختياري" />
                                        </div>
                                        <div>
                                            <label>النوع المرتبط</label>
                                            <input name="related_type" placeholder="App\\Models\\Student" value="App\\Models\\Student" />
                                        </div>
                                    </div>
                                    <div class="grid-2">
                                        <div>
                                            <label>ملف حساس؟</label>
                                            <select name="is_sensitive">
                                                <option value="0">false</option>
                                                <option value="1">true</option>
                                            </select>
                                        </div>
                                        <div class="hint" style="display:flex;align-items:end;">
                                            يوصى بإبقاء الملفات التجريبية على `school` أو `private`.
                                        </div>
                                    </div>
                                    <button class="primary" type="submit">رفع الملف</button>
                                </form>
                            </article>

                            <article class="form-card">
                                <h3>تشغيل التقارير</h3>
                                <p class="hint" style="margin-bottom: 12px;">
                                    استخدم حساب `principal` أو `supervisor` أو `admin` لتجربة هذه الوحدة، ثم اختر التقرير المطلوب لعرضه مباشرة في الجدول.
                                </p>
                                <div class="grid-2" style="margin-bottom: 12px;">
                                    <div>
                                        <label>الطالب المختار للتقرير الفردي</label>
                                        <select id="report-student-select"></select>
                                    </div>
                                    <div>
                                        <label>البعد المستخدم في Pivot</label>
                                        <select id="report-pivot-dimension-select">
                                            <option value="grade_level">grade_level</option>
                                            <option value="gender">gender</option>
                                            <option value="disability_category">disability_category</option>
                                            <option value="education_program">education_program</option>
                                            <option value="iep_status">iep_status</option>
                                            <option value="teacher">teacher</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="actions">
                                    <button class="secondary" type="button" id="report-school-summary-btn">تقرير المدرسة</button>
                                    <button class="secondary" type="button" id="report-student-summary-btn">تقرير الطالب</button>
                                    <button class="ghost" type="button" id="report-comparison-btn">المقارنة</button>
                                    <button class="ghost" type="button" id="report-pivot-btn">Pivot</button>
                                    <button class="ghost" type="button" id="report-export-pdf-btn">تصدير PDF</button>
                                    <button class="ghost" type="button" id="report-export-excel-btn">تصدير Excel</button>
                                </div>
                            </article>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </div>

    <script>
        const demoAccounts = @json($demoAccounts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        const referenceData = @json($referenceData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        const storageKey = 'maak_demo_token';

        const state = {
            token: localStorage.getItem(storageKey),
            currentUser: null,
            permissions: [],
            schools: referenceData.schools || [],
            users: [],
            students: [],
            iepPlans: [],
            messages: [],
            notifications: [],
            files: [],
            reportRows: [],
            reportCaption: 'آخر تقرير تم تشغيله سيظهر هنا.',
            activeTable: 'schools',
            schoolId: String(@json($demoSchool['id'])),
        };

        const identifierInput = document.getElementById('identifier');
        const passwordInput = document.getElementById('password');
        const schoolIdInput = document.getElementById('school-id');
        const sessionSummary = document.getElementById('session-summary');
        const consoleOutput = document.getElementById('console-output');
        const tableHead = document.querySelector('#data-table thead');
        const tableBody = document.querySelector('#data-table tbody');
        const datasetCaption = document.getElementById('dataset-caption');
        const schoolStatsGrid = document.getElementById('school-stats-grid');

        const roleSelect = document.getElementById('user-role-select');
        const academicYearSelect = document.getElementById('academic-year-select');
        const programSelect = document.getElementById('program-select');
        const disabilitySelect = document.getElementById('disability-select');
        const teacherSelect = document.getElementById('teacher-select');
        const guardianStudentSelect = document.getElementById('guardian-student-select');
        const guardianParentSelect = document.getElementById('guardian-parent-select');
        const iepStudentSelect = document.getElementById('iep-student-select');
        const iepAcademicYearSelect = document.getElementById('iep-academic-year-select');
        const messageRecipientSelect = document.getElementById('message-recipient-select');
        const messageThreadKeyInput = document.getElementById('message-thread-key');
        const reportStudentSelect = document.getElementById('report-student-select');
        const reportPivotDimensionSelect = document.getElementById('report-pivot-dimension-select');
        const fileUploadInput = document.getElementById('file-upload-input');

        const setConsole = (label, payload) => {
            consoleOutput.textContent = `${label}\n\n${JSON.stringify(payload, null, 2)}`;
        };

        const hasPermission = (permission) => (
            state.permissions.includes('*') || state.permissions.includes(permission)
        );

        const setActiveTable = (tableName) => {
            state.activeTable = tableName;
            document.querySelectorAll('#dataset-switcher button').forEach((item) => {
                item.classList.toggle('active', item.dataset.table === tableName);
            });
        };

        const updateSessionBox = () => {
            if (!state.currentUser) {
                sessionSummary.innerHTML = 'لا توجد جلسة فعالة بعد.';
                return;
            }

            const permissionHtml = state.permissions.length
                ? `<div class="permissions">${state.permissions.slice(0, 18).map((permission) => `<span class="perm">${permission}</span>`).join('')}</div>`
                : '<div class="hint" style="margin-top: 10px;">لا توجد صلاحيات محملة.</div>';

            sessionSummary.innerHTML = `
                <strong>${state.currentUser.full_name}</strong>
                <div class="meta">الدور: ${state.currentUser.role}</div>
                <div class="meta">البريد: ${state.currentUser.email || '-'}</div>
                <div class="meta">School ID: ${state.currentUser.school_id ?? schoolIdInput.value || '-'}</div>
                ${permissionHtml}
            `;
        };

        const toOptions = (items, mapper) => items.map(mapper).join('');

        const normalizeListInput = (value) => String(value || '')
            .split(/[\n,]+/)
            .map((item) => item.trim())
            .filter(Boolean);

        const refreshReferenceSelects = () => {
            roleSelect.innerHTML = toOptions(referenceData.roles || [], (role) =>
                `<option value="${role.name}">${role.display_name_ar || role.name}</option>`
            );

            academicYearSelect.innerHTML = toOptions(referenceData.academic_years || [], (item) =>
                `<option value="${item.id}">${item.name_ar}${item.is_active ? ' (نشط)' : ''}</option>`
            );

            programSelect.innerHTML = toOptions(referenceData.education_programs || [], (item) =>
                `<option value="${item.id}">${item.name_ar}</option>`
            );

            disabilitySelect.innerHTML = toOptions(referenceData.disability_categories || [], (item) =>
                `<option value="${item.id}">${item.name_ar}</option>`
            );

            const teachers = state.users.filter((user) => user.role === 'teacher');
            const fallbackTeacher = !teachers.length && state.currentUser?.role === 'teacher'
                ? [{ id: state.currentUser.id, full_name: state.currentUser.full_name }]
                : [];
            const availableTeachers = teachers.length ? teachers : fallbackTeacher;

            teacherSelect.innerHTML = teachers.length
                ? teachers.map((teacher) => `<option value="${teacher.id}">${teacher.full_name}</option>`).join('')
                : availableTeachers.length
                    ? availableTeachers.map((teacher) => `<option value="${teacher.id}">${teacher.full_name}</option>`).join('')
                    : '<option value="">حمّل المستخدمين أولًا</option>';

            iepAcademicYearSelect.innerHTML = academicYearSelect.innerHTML || '<option value="">لا توجد سنة دراسية</option>';

            iepStudentSelect.innerHTML = state.students.length
                ? state.students.map((student) => `<option value="${student.id}">${student.full_name}</option>`).join('')
                : '<option value="">حمّل الطلاب أولًا</option>';

            const parents = state.users.filter((user) => user.role === 'parent');
            guardianParentSelect.innerHTML = parents.length
                ? parents.map((parent) => `<option value="${parent.id}">${parent.full_name}</option>`).join('')
                : '<option value="">حمّل أولياء الأمور أولًا</option>';

            guardianStudentSelect.innerHTML = state.students.length
                ? state.students.map((student) => `<option value="${student.id}">${student.full_name}</option>`).join('')
                : '<option value="">حمّل الطلاب أولًا</option>';

            reportStudentSelect.innerHTML = state.students.length
                ? state.students.map((student) => `<option value="${student.id}">${student.full_name}</option>`).join('')
                : '<option value="">حمّل الطلاب أولًا</option>';

            const messagingTargets = (state.users.length ? state.users : (referenceData.messaging_targets || []))
                .filter((user) => String(user.id) !== String(state.currentUser?.id || ''));

            messageRecipientSelect.innerHTML = messagingTargets.length
                ? messagingTargets.map((user) => `<option value="${user.id}">${user.full_name} - ${user.role || '-'}</option>`).join('')
                : '<option value="">لا يوجد مستلمون متاحون</option>';
        };

        const apiRequest = async (path, options = {}) => {
            const isFormData = options.body instanceof FormData;
            const headers = {
                'Accept': 'application/json',
                ...(!isFormData && options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(schoolIdInput.value.trim() ? { 'X-School-Id': schoolIdInput.value.trim() } : {}),
                ...(state.token ? { 'Authorization': `Bearer ${state.token}` } : {}),
                ...(options.headers || {}),
            };

            const response = await fetch(path, {
                ...options,
                headers,
            });

            let payload = null;

            try {
                payload = await response.json();
            } catch (error) {
                payload = { raw: await response.text() };
            }

            setConsole(`${options.method || 'GET'} ${path} [${response.status}]`, payload);

            if (!response.ok) {
                throw payload;
            }

            return payload;
        };

        const translateIepStatus = (status) => ({
            draft: 'مسودة',
            pending_principal_review: 'بانتظار اعتماد المدير',
            pending_supervisor_review: 'بانتظار اعتماد المشرف',
            approved: 'معتمدة',
            rejected: 'مرفوضة',
            archived: 'مؤرشفة',
        }[String(status || '').toLowerCase()] || status || '-');

        const badgeForIepStatus = (status) => {
            const normalized = String(status || '').toLowerCase();
            const className = normalized === 'approved'
                ? 'ok'
                : (normalized === 'rejected' || normalized.startsWith('pending_') ? 'warn' : 'muted');

            return `<span class="badge ${className}">${translateIepStatus(status)}</span>`;
        };

        const renderIepActions = (plan) => {
            const actions = [
                `<button class="secondary row-action" data-action="iep-show" data-id="${plan.id}">عرض</button>`,
                `<button class="ghost row-action" data-action="iep-versions" data-id="${plan.id}">الإصدارات</button>`,
                `<button class="ghost row-action" data-action="iep-pdf" data-id="${plan.id}">PDF</button>`,
                `<button class="ghost row-action" data-action="iep-comment" data-id="${plan.id}">تعليق</button>`,
            ];

            if (plan.status === 'draft') {
                actions.push(`<button class="primary row-action" data-action="iep-submit" data-id="${plan.id}">إرسال</button>`);
            }

            if (plan.status === 'pending_principal_review') {
                actions.push(`<button class="primary row-action" data-action="iep-principal-approve" data-id="${plan.id}">اعتماد المدير</button>`);
                actions.push(`<button class="danger-soft row-action" data-action="iep-reject" data-id="${plan.id}">رفض</button>`);
            }

            if (plan.status === 'pending_supervisor_review') {
                actions.push(`<button class="primary row-action" data-action="iep-supervisor-approve" data-id="${plan.id}">اعتماد المشرف</button>`);
                actions.push(`<button class="danger-soft row-action" data-action="iep-reject" data-id="${plan.id}">رفض</button>`);
            }

            if (plan.status === 'rejected') {
                actions.push(`<button class="secondary row-action" data-action="iep-reopen" data-id="${plan.id}">إعادة فتح</button>`);
            }

            return `<div class="actions">${actions.join('')}</div>`;
        };

        const serializeReportValue = (value) => {
            if (value === null || value === undefined || value === '') {
                return '-';
            }

            if (Array.isArray(value)) {
                return value.map((item) => {
                    if (item && typeof item === 'object') {
                        return item.full_name || item.name_ar || item.label || JSON.stringify(item);
                    }

                    return String(item);
                }).join('، ');
            }

            if (typeof value === 'object') {
                return JSON.stringify(value);
            }

            return String(value);
        };

        const addReportSectionRows = (rows, section, record) => {
            Object.entries(record || {}).forEach(([key, value]) => {
                rows.push([
                    `${section}: ${key}`,
                    Array.isArray(value) ? value.length : (value && typeof value === 'object' ? 'انظر التفاصيل' : serializeReportValue(value)),
                    serializeReportValue(value),
                ]);
            });
        };

        const setReportRows = (caption, rows) => {
            state.reportCaption = caption;
            state.reportRows = rows;
            setActiveTable('reports');
            renderTable();
        };

        const renderTable = () => {
            const tableConfigs = {
                schools: {
                    caption: 'بيانات المدارس المتاحة من الـ API.',
                    columns: ['ID', 'الاسم', 'المدينة', 'المنطقة', 'الحالة', 'إجراء'],
                    rows: state.schools.map((school) => [
                        school.id,
                        school.name_ar,
                        school.city,
                        school.region,
                        badgeForStatus(school.status),
                        `<button class="secondary row-action" data-action="school-stats" data-id="${school.id}">إحصاءات</button>`,
                    ]),
                },
                users: {
                    caption: 'المستخدمون الحاليون بحسب صلاحيات الحساب المسجل.',
                    columns: ['ID', 'الاسم', 'البريد', 'الجوال', 'الدور', 'School ID', 'الحالة'],
                    rows: state.users.map((user) => [
                        user.id,
                        user.full_name,
                        user.email || '-',
                        user.phone || '-',
                        user.role || '-',
                        user.school_id ?? '-',
                        badgeForStatus(user.status),
                    ]),
                },
                students: {
                    caption: 'الطلاب ضمن نطاق المدرسة الحالية.',
                    columns: ['ID', 'الاسم', 'رقم الطالب', 'المعلم', 'الصف', 'الفصل', 'الحالة', 'إجراء'],
                    rows: state.students.map((student) => [
                        student.id,
                        student.full_name,
                        student.student_number || '-',
                        student.primary_teacher?.full_name || '-',
                        student.grade_level || '-',
                        student.classroom || '-',
                        badgeForStatus(student.enrollment_status),
                        `<button class="secondary row-action" data-action="student-guardians" data-id="${student.id}">الأولياء</button>`,
                    ]),
                },
                'iep-plans': {
                    caption: 'الخطط الفردية IEP داخل المدرسة الحالية مع إجراءات المسار التشغيلي.',
                    columns: ['ID', 'الطالب', 'العنوان', 'الإصدار', 'الحالة', 'المعلم', 'الأهداف', 'إجراء'],
                    rows: state.iepPlans.map((plan) => [
                        plan.id,
                        plan.student?.full_name || '-',
                        plan.title || '-',
                        plan.current_version_number || '-',
                        badgeForIepStatus(plan.status),
                        plan.teacher?.full_name || '-',
                        plan.goals?.length || 0,
                        renderIepActions(plan),
                    ]),
                },
                messages: {
                    caption: 'المحادثات الداخلية المتاحة للمستخدم الحالي داخل نطاق المدرسة.',
                    columns: ['السلسلة', 'الموضوع', 'آخر مرسل', 'المشاركون', 'عدد الرسائل', 'غير مقروءة', 'آخر تحديث', 'إجراء'],
                    rows: state.messages.map((thread) => [
                        thread.thread_key,
                        thread.subject || '-',
                        thread.latest_sender_name || '-',
                        (thread.participants || []).map((participant) => participant.full_name).join('، ') || '-',
                        thread.message_count || 0,
                        thread.unread_count || 0,
                        thread.latest_message_at ? new Date(thread.latest_message_at).toLocaleString('ar-SA') : '-',
                        `<div class="actions">
                            <button class="secondary row-action" data-action="message-thread" data-thread-key="${thread.thread_key}">فتح السلسلة</button>
                            ${thread.latest_unread_message_id
                                ? `<button class="ghost row-action" data-action="message-read" data-id="${thread.latest_unread_message_id}">تعليم كمقروءة</button>`
                                : ''
                            }
                        </div>`,
                    ]),
                },
                notifications: {
                    caption: 'الإشعارات الخاصة بالمستخدم الحالي داخل النظام.',
                    columns: ['ID', 'النوع', 'العنوان', 'النص', 'الحالة', 'أُرسل في', 'إجراء'],
                    rows: state.notifications.map((notification) => [
                        notification.id,
                        notification.type || '-',
                        notification.title || '-',
                        notification.body || '-',
                        notification.read_at ? '<span class="badge ok">مقروء</span>' : '<span class="badge warn">غير مقروء</span>',
                        notification.sent_at ? new Date(notification.sent_at).toLocaleString('ar-SA') : '-',
                        notification.read_at
                            ? '-'
                            : `<button class="secondary row-action" data-action="notification-read" data-id="${notification.id}">تعليم كمقروء</button>`,
                    ]),
                },
                files: {
                    caption: 'الملفات المرفوعة داخل نطاق المدرسة الحالية مع دعم روابط التحميل المؤقتة.',
                    columns: ['ID', 'الاسم', 'الفئة', 'الحجم', 'الظهور', 'حساس', 'تاريخ الرفع', 'إجراء'],
                    rows: state.files.map((file) => [
                        file.id,
                        file.original_name || '-',
                        file.category || '-',
                        file.size_bytes ? `${file.size_bytes} bytes` : '-',
                        file.visibility || '-',
                        file.is_sensitive ? '<span class="badge warn">نعم</span>' : '<span class="badge ok">لا</span>',
                        file.uploaded_at ? new Date(file.uploaded_at).toLocaleString('ar-SA') : '-',
                        `<div class="actions">
                            <button class="secondary row-action" data-action="file-show" data-id="${file.id}">عرض</button>
                            <button class="ghost row-action" data-action="file-temp-link" data-id="${file.id}">رابط مؤقت</button>
                            <button class="danger-soft row-action" data-action="file-delete" data-id="${file.id}">حذف</button>
                        </div>`,
                    ]),
                },
                reports: {
                    caption: state.reportCaption,
                    columns: ['العنصر', 'القيمة', 'التفاصيل'],
                    rows: state.reportRows,
                },
            };

            const config = tableConfigs[state.activeTable];
            const colSpan = config.columns.length;

            datasetCaption.textContent = config.caption;
            tableHead.innerHTML = `<tr>${config.columns.map((column) => `<th>${column}</th>`).join('')}</tr>`;
            tableBody.innerHTML = config.rows.length
                ? config.rows.map((row) => `<tr>${row.map((cell) => `<td>${cell}</td>`).join('')}</tr>`).join('')
                : `<tr><td colspan="${colSpan}">لا توجد بيانات محملة حاليًا.</td></tr>`;
        };

        const badgeForStatus = (status) => {
            const normalized = String(status || '').toLowerCase();
            const className = normalized === 'active' ? 'ok' : (normalized === 'archived' ? 'warn' : 'muted');
            return `<span class="badge ${className}">${status || '-'}</span>`;
        };

        const updateSchoolStats = (stats) => {
            schoolStatsGrid.innerHTML = `
                <div class="mini"><strong>${stats.students_count ?? 0}</strong><span class="muted">الطلاب</span></div>
                <div class="mini"><strong>${stats.teachers_count ?? 0}</strong><span class="muted">المعلمون</span></div>
                <div class="mini"><strong>${stats.iep_plans_count ?? 0}</strong><span class="muted">الخطط</span></div>
            `;
        };

        const loadCurrentUser = async () => {
            const payload = await apiRequest('/api/v1/auth/me');
            state.currentUser = payload.data.user;
            state.permissions = payload.data.permissions || [];
            updateSessionBox();
            refreshReferenceSelects();
            return payload;
        };

        const loadSchools = async () => {
            const payload = await apiRequest('/api/v1/schools');
            state.schools = payload.data || [];
            renderTable();
            return payload;
        };

        const loadUsers = async () => {
            const payload = await apiRequest('/api/v1/users');
            state.users = payload.data || [];
            refreshReferenceSelects();
            renderTable();
            return payload;
        };

        const loadStudents = async () => {
            const payload = await apiRequest('/api/v1/students');
            state.students = payload.data || [];
            refreshReferenceSelects();
            renderTable();
            return payload;
        };

        const loadIepPlans = async () => {
            const payload = await apiRequest('/api/v1/iep-plans');
            state.iepPlans = payload.data || [];
            renderTable();
            return payload;
        };

        const loadMessages = async () => {
            const payload = await apiRequest('/api/v1/messages');
            state.messages = payload.data || [];
            renderTable();
            return payload;
        };

        const loadNotifications = async () => {
            const payload = await apiRequest('/api/v1/notifications');
            state.notifications = payload.data || [];
            renderTable();
            return payload;
        };

        const loadFiles = async () => {
            const payload = await apiRequest('/api/v1/files');
            state.files = payload.data || [];
            renderTable();
            return payload;
        };

        const showSchoolSummaryReport = async () => {
            const schoolId = schoolIdInput.value.trim();
            const payload = await apiRequest(`/api/v1/reports/schools/${schoolId}/summary`);
            const rows = [];
            addReportSectionRows(rows, 'المدرسة', payload.data.school);
            addReportSectionRows(rows, 'المؤشرات', payload.data.overview);
            addReportSectionRows(rows, 'التوزيع - المستخدمون', payload.data.breakdowns?.users_by_role);
            addReportSectionRows(rows, 'التوزيع - الطلاب حسب الحالة', payload.data.breakdowns?.students_by_status);
            addReportSectionRows(rows, 'التوزيع - الطلاب حسب الجنس', payload.data.breakdowns?.students_by_gender);
            addReportSectionRows(rows, 'التوزيع - الطلاب حسب الصف', payload.data.breakdowns?.students_by_grade);
            addReportSectionRows(rows, 'التوزيع - IEP', payload.data.breakdowns?.iep_by_status);
            setReportRows(`تقرير المدرسة: ${payload.data.school?.name_ar || schoolId}`, rows);
        };

        const showStudentSummaryReport = async () => {
            const studentId = reportStudentSelect.value;
            const payload = await apiRequest(`/api/v1/reports/students/${studentId}/summary`);
            const rows = [];
            addReportSectionRows(rows, 'الطالب', payload.data.student);
            addReportSectionRows(rows, 'التعليم', payload.data.education);
            addReportSectionRows(rows, 'IEP', payload.data.iep);
            addReportSectionRows(rows, 'النشاط', payload.data.activity);
            rows.push([
                'الأولياء',
                payload.data.guardians?.length || 0,
                serializeReportValue((payload.data.guardians || []).map((guardian) => `${guardian.parent_name} (${guardian.relationship})`)),
            ]);
            setReportRows(`تقرير الطالب: ${payload.data.student?.full_name || studentId}`, rows);
        };

        const showComparisonReport = async () => {
            const schoolId = schoolIdInput.value.trim();
            const query = schoolId ? `?school_ids=${encodeURIComponent(schoolId)}` : '';
            const payload = await apiRequest(`/api/v1/reports/comparison${query}`);
            const rows = (payload.data || []).map((item) => [
                item.school_name,
                item.students_count ?? 0,
                `معلمون: ${item.teachers_count ?? 0} | IEP: ${item.iep_plans_count ?? 0} | رسائل: ${item.messages_count ?? 0} | إشعارات: ${item.notifications_count ?? 0}`,
            ]);
            setReportRows('تقرير المقارنة', rows);
        };

        const showPivotReport = async () => {
            const schoolId = schoolIdInput.value.trim();
            const dimension = reportPivotDimensionSelect.value;
            const queryParts = [`dimension=${encodeURIComponent(dimension)}`];

            if (schoolId) {
                queryParts.push(`school_id=${encodeURIComponent(schoolId)}`);
            }

            const payload = await apiRequest(`/api/v1/reports/pivot?${queryParts.join('&')}`);
            const rows = (payload.data.rows || []).map((item) => [
                item.label,
                item.value,
                `dimension: ${payload.data.dimension}`,
            ]);
            setReportRows(`Pivot Report: ${payload.data.dimension}`, rows);
        };

        const showExportPreview = async (format) => {
            const schoolId = schoolIdInput.value.trim();
            const payload = await apiRequest(`/api/v1/reports/export/${format}?type=school_summary&school_id=${encodeURIComponent(schoolId)}`);
            const rows = [];
            addReportSectionRows(rows, 'التصدير', {
                format: payload.data.format,
                type: payload.data.type,
                generated_at: payload.data.generated_at,
                queued: payload.data.queued,
                download_available: payload.data.download_available,
                message: payload.data.message,
            });
            addReportSectionRows(rows, 'معاينة المدرسة', payload.data.preview?.overview);
            setReportRows(`معاينة تصدير ${String(format).toUpperCase()}`, rows);
        };

        const loadAll = async () => {
            try {
                await loadCurrentUser();
            } catch (error) {
                console.warn(error);
                return;
            }

            const tasks = [];

            if (hasPermission('schools.view_any')) tasks.push(loadSchools());
            if (hasPermission('users.view_any')) tasks.push(loadUsers());
            if (hasPermission('students.view_any')) tasks.push(loadStudents());
            if (hasPermission('iep.view_any')) tasks.push(loadIepPlans());
            if (hasPermission('messages.view_any')) tasks.push(loadMessages());
            if (hasPermission('notifications.view_any')) tasks.push(loadNotifications());
            if (hasPermission('files.view')) tasks.push(loadFiles());

            const settled = await Promise.allSettled(tasks);

            settled.forEach((result) => {
                if (result.status === 'rejected') {
                    console.warn(result.reason);
                }
            });
        };

        document.getElementById('login-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            try {
                const payload = await apiRequest('/api/v1/auth/login', {
                    method: 'POST',
                    body: JSON.stringify({
                        identifier: identifierInput.value.trim(),
                        password: passwordInput.value,
                    }),
                });

                state.token = payload.data.token;
                localStorage.setItem(storageKey, state.token);
                state.currentUser = payload.data.user;
                state.permissions = payload.data.permissions || [];
                updateSessionBox();
                await loadAll();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('logout-btn').addEventListener('click', async () => {
            try {
                if (state.token) {
                    await apiRequest('/api/v1/auth/logout', { method: 'POST' });
                }
            } catch (error) {
                console.warn(error);
            }

            localStorage.removeItem(storageKey);
            state.token = null;
            state.currentUser = null;
            state.permissions = [];
            updateSessionBox();
            setConsole('Logout', { success: true });
        });

        document.querySelectorAll('.use-account').forEach((button) => {
            button.addEventListener('click', () => {
                identifierInput.value = button.dataset.email || '';
                passwordInput.value = button.dataset.password || '';
                schoolIdInput.value = button.dataset.schoolId || schoolIdInput.value;
            });
        });

        document.getElementById('load-all-btn').addEventListener('click', () => loadAll());
        document.getElementById('load-me-btn').addEventListener('click', () => loadCurrentUser());

        document.querySelectorAll('[data-dataset]').forEach((button) => {
            button.addEventListener('click', async () => {
                const dataset = button.dataset.dataset;

                if (dataset === 'schools') await loadSchools();
                if (dataset === 'users') await loadUsers();
                if (dataset === 'students') await loadStudents();
                if (dataset === 'iep-plans') await loadIepPlans();
                if (dataset === 'messages') await loadMessages();
                if (dataset === 'notifications') await loadNotifications();
                if (dataset === 'files') await loadFiles();
                if (dataset === 'reports') renderTable();
            });
        });

        document.querySelectorAll('#dataset-switcher button').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelectorAll('#dataset-switcher button').forEach((item) => item.classList.remove('active'));
                button.classList.add('active');
                state.activeTable = button.dataset.table;
                renderTable();
            });
        });

        document.getElementById('school-stats-btn').addEventListener('click', async () => {
            try {
                const schoolId = schoolIdInput.value.trim();
                const payload = await apiRequest(`/api/v1/schools/${schoolId}/stats`);
                updateSchoolStats(payload.data || {});
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('notifications-read-all-btn').addEventListener('click', async () => {
            try {
                await apiRequest('/api/v1/notifications/read-all', {
                    method: 'POST',
                });
                await loadNotifications();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('report-school-summary-btn').addEventListener('click', async () => {
            try {
                await showSchoolSummaryReport();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('report-student-summary-btn').addEventListener('click', async () => {
            try {
                await showStudentSummaryReport();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('report-comparison-btn').addEventListener('click', async () => {
            try {
                await showComparisonReport();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('report-pivot-btn').addEventListener('click', async () => {
            try {
                await showPivotReport();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('report-export-pdf-btn').addEventListener('click', async () => {
            try {
                await showExportPreview('pdf');
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('report-export-excel-btn').addEventListener('click', async () => {
            try {
                await showExportPreview('excel');
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('school-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);

            try {
                await apiRequest('/api/v1/schools', {
                    method: 'POST',
                    body: JSON.stringify(Object.fromEntries(formData.entries())),
                });
                await loadSchools();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('user-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());

            payload.school_id = payload.school_id ? Number(payload.school_id) : null;
            payload.must_change_password = false;

            try {
                await apiRequest('/api/v1/users', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                await loadUsers();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('student-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());

            ['school_id', 'academic_year_id', 'education_program_id', 'disability_category_id', 'primary_teacher_user_id']
                .forEach((key) => {
                    payload[key] = payload[key] ? Number(payload[key]) : null;
                });

            try {
                await apiRequest('/api/v1/students', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                await loadStudents();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('guardian-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());
            const studentId = Number(payload.student_id);

            payload.parent_user_id = Number(payload.parent_user_id);
            payload.is_primary = payload.is_primary === 'true';

            delete payload.student_id;

            try {
                await apiRequest(`/api/v1/students/${studentId}/guardians`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                await loadStudents();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('iep-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());

            payload.student_id = Number(payload.student_id);
            payload.academic_year_id = payload.academic_year_id ? Number(payload.academic_year_id) : null;
            payload.accommodations = normalizeListInput(payload.accommodations_text);
            payload.goals = [
                {
                    domain: payload.goal_domain,
                    goal_text: payload.goal_text,
                    measurement_method: payload.goal_measurement_method || null,
                    target_value: payload.goal_target_value || null,
                    due_date: payload.goal_due_date || null,
                    sort_order: 0,
                },
            ];

            delete payload.accommodations_text;
            delete payload.goal_domain;
            delete payload.goal_text;
            delete payload.goal_measurement_method;
            delete payload.goal_target_value;
            delete payload.goal_due_date;

            try {
                await apiRequest('/api/v1/iep-plans', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                setActiveTable('iep-plans');
                await loadIepPlans();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('message-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());

            payload.recipient_ids = payload.recipient_id ? [Number(payload.recipient_id)] : [];

            delete payload.recipient_id;

            if (!payload.thread_key) {
                delete payload.thread_key;
            }

            if (!payload.subject) {
                delete payload.subject;
            }

            try {
                await apiRequest('/api/v1/messages', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                setActiveTable('messages');
                await loadMessages();
            } catch (error) {
                console.error(error);
            }
        });

        document.getElementById('file-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.currentTarget);

            if (!fileUploadInput.files.length) {
                window.alert('اختر ملفًا أولًا قبل الرفع.');
                return;
            }

            if (!formData.get('related_id')) {
                formData.delete('related_id');
            }

            if (!formData.get('related_type')) {
                formData.delete('related_type');
            }

            try {
                await apiRequest('/api/v1/files', {
                    method: 'POST',
                    body: formData,
                });
                event.currentTarget.reset();
                setActiveTable('files');
                await loadFiles();
            } catch (error) {
                console.error(error);
            }
        });

        tableBody.addEventListener('click', async (event) => {
            const button = event.target.closest('.row-action');

            if (!button) {
                return;
            }

            const id = button.dataset.id;
            const action = button.dataset.action;
            const threadKey = button.dataset.threadKey;

            try {
                if (action === 'school-stats') {
                    const payload = await apiRequest(`/api/v1/schools/${id}/stats`);
                    updateSchoolStats(payload.data || {});
                }

                if (action === 'student-guardians') {
                    await apiRequest(`/api/v1/students/${id}/guardians`);
                }

                if (action === 'iep-show') {
                    await apiRequest(`/api/v1/iep-plans/${id}`);
                }

                if (action === 'iep-versions') {
                    await apiRequest(`/api/v1/iep-plans/${id}/versions`);
                }

                if (action === 'iep-pdf') {
                    await apiRequest(`/api/v1/iep-plans/${id}/pdf`);
                }

                if (action === 'iep-submit') {
                    const notes = window.prompt('ملاحظات الإرسال للمدير (اختياري):', '') ?? '';
                    await apiRequest(`/api/v1/iep-plans/${id}/submit`, {
                        method: 'POST',
                        body: JSON.stringify({ notes }),
                    });
                    await loadIepPlans();
                }

                if (action === 'iep-principal-approve') {
                    const notes = window.prompt('ملاحظات اعتماد المدير (اختياري):', '') ?? '';
                    await apiRequest(`/api/v1/iep-plans/${id}/principal-approve`, {
                        method: 'POST',
                        body: JSON.stringify({ notes }),
                    });
                    await loadIepPlans();
                }

                if (action === 'iep-supervisor-approve') {
                    const notes = window.prompt('ملاحظات اعتماد المشرف (اختياري):', '') ?? '';
                    await apiRequest(`/api/v1/iep-plans/${id}/supervisor-approve`, {
                        method: 'POST',
                        body: JSON.stringify({ notes }),
                    });
                    await loadIepPlans();
                }

                if (action === 'iep-reject') {
                    const reason = window.prompt('سبب الرفض مطلوب:', '');

                    if (reason === null || !reason.trim()) {
                        return;
                    }

                    const notes = window.prompt('ملاحظات إضافية (اختياري):', '') ?? '';
                    await apiRequest(`/api/v1/iep-plans/${id}/reject`, {
                        method: 'POST',
                        body: JSON.stringify({ reason, notes }),
                    });
                    await loadIepPlans();
                }

                if (action === 'iep-reopen') {
                    const notes = window.prompt('ملاحظات إعادة الفتح (اختياري):', '') ?? '';
                    await apiRequest(`/api/v1/iep-plans/${id}/reopen`, {
                        method: 'POST',
                        body: JSON.stringify({ notes }),
                    });
                    await loadIepPlans();
                }

                if (action === 'iep-comment') {
                    const targetSection = window.prompt('القسم المستهدف من التعليق:', 'summary');

                    if (targetSection === null) {
                        return;
                    }

                    const commentText = window.prompt('نص التعليق:', '');

                    if (commentText === null || !commentText.trim()) {
                        return;
                    }

                    const isInternal = window.confirm('هل التعليق داخلي؟');
                    await apiRequest(`/api/v1/iep-plans/${id}/comments`, {
                        method: 'POST',
                        body: JSON.stringify({
                            target_section: targetSection,
                            comment_text: commentText,
                            is_internal: isInternal,
                        }),
                    });
                    await apiRequest(`/api/v1/iep-plans/${id}`);
                    await loadIepPlans();
                }

                if (action === 'message-thread') {
                    messageThreadKeyInput.value = threadKey || '';
                    await apiRequest(`/api/v1/messages/thread/${encodeURIComponent(threadKey)}`);
                }

                if (action === 'message-read') {
                    await apiRequest(`/api/v1/messages/${id}/read`, {
                        method: 'POST',
                    });
                    await loadMessages();
                }

                if (action === 'notification-read') {
                    await apiRequest(`/api/v1/notifications/${id}/read`, {
                        method: 'POST',
                    });
                    await loadNotifications();
                }

                if (action === 'file-show') {
                    await apiRequest(`/api/v1/files/${id}`);
                }

                if (action === 'file-temp-link') {
                    const expires = window.prompt('مدة صلاحية الرابط بالدقائق:', '30');

                    if (expires === null) {
                        return;
                    }

                    const payload = await apiRequest(`/api/v1/files/${id}/temporary-link`, {
                        method: 'POST',
                        body: JSON.stringify({
                            expires_in_minutes: Number(expires) || 30,
                        }),
                    });

                    const url = payload.data?.temporary_link?.url;

                    if (url) {
                        window.open(url, '_blank', 'noopener');
                    }
                }

                if (action === 'file-delete') {
                    const confirmed = window.confirm('سيتم حذف مرجع الملف منطقيًا. هل تريد المتابعة؟');

                    if (!confirmed) {
                        return;
                    }

                    await apiRequest(`/api/v1/files/${id}`, {
                        method: 'DELETE',
                    });
                    await loadFiles();
                }
            } catch (error) {
                console.error(error);
            }
        });

        refreshReferenceSelects();
        updateSessionBox();
        renderTable();

        if (state.token) {
            loadAll().catch((error) => {
                console.error(error);
            });
        }
    </script>
</body>
</html>
