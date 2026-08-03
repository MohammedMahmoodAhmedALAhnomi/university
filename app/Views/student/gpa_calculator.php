<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- الهيدر والترحيب -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white bg-primary" style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);">
                <div class="card-body p-4 p-md-5 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold mb-3 shadow-sm">
                                <i class="fas fa-calculator me-1"></i> أدوات الطالب الأكاديمية
                            </span>
                            <h2 class="fw-bold mb-2">حاسبة المعدل التراكمي والفصلي (GPA)</h2>
                            <p class="opacity-90 mb-0">احسب معدلك الفصلي والتراكمي بدقة عالية، وتوقع تقديرك الأكاديمي بناءً على الساعات المعتمدة.</p>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="fas fa-graduation-cap display-1 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- خيارات الحاسبة والمدخلات -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i class="fas fa-sliders-h text-primary me-2"></i>نظام تقييم المعدل (Scale)</label>
                            <select id="gpaScale" class="form-select rounded-3 shadow-sm">
                                <option value="4" selected>نظام من 4.0 (A=4, B=3, C=2, D=1, F=0)</option>
                                <option value="5">نظام من 5.0 (A+=5, A=4.75, B+=4.5, B=4.0 ...)</option>
                                <option value="100">نظام النسبة المئوية (100%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-md-4">
                                <input class="form-check-input" type="checkbox" id="includeCumulative">
                                <label class="form-check-label fw-bold text-dark" for="includeCumulative">
                                    تضمين الساعات والمعدل التراكمي السابق
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- الساعات والمعدل التراكمي السابق (مخفي افتراضياً) -->
                    <div id="cumulativeSection" class="row g-3 mb-4 p-3 bg-light rounded-3 d-none border">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">إجمالي الساعات المقطوعة سابقاً</label>
                            <input type="number" id="prevHours" class="form-control" min="0" placeholder="مثال: 45">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المعدل التراكمي السابق</label>
                            <input type="number" id="prevGpa" class="form-control" step="0.01" min="0" placeholder="مثال: 3.45">
                        </div>
                    </div>

                    <!-- جدول المواد الدراسية -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-book-open text-primary me-2"></i>مواد الفصل الحالي</h5>
                        <button type="button" id="addCourseBtn" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="fas fa-plus me-1"></i> إضافة مادة
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-hover align-middle border rounded-3 overflow-hidden">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">اسم المادة (اختياري)</th>
                                    <th style="width: 25%;">الساعات المعتمدة</th>
                                    <th style="width: 25%;">التقدير / الدرجة</th>
                                    <th style="width: 10%; text-align: center;">إزالة</th>
                                </tr>
                            </thead>
                            <tbody id="coursesContainer">
                                <!-- سيتم توليد الصفوف هنا بواسطة JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- الأزرار والنتائج -->
                    <div class="d-flex gap-2 justify-content-end mb-4">
                        <button type="button" id="resetBtn" class="btn btn-light rounded-pill px-4">
                            <i class="fas fa-undo me-1"></i> إعادة ضبط
                        </button>
                        <button type="button" id="calculateBtn" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold">
                            <i class="fas fa-calculator me-1"></i> احسب المعدل
                        </button>
                    </div>

                    <!-- بطاقة النتائج -->
                    <div id="resultsCard" class="card border-0 bg-light rounded-4 p-4 text-center d-none shadow-sm">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6 border-end-md">
                                <span class="text-muted fw-bold d-block mb-1">المعدل الفصلي (Semester GPA)</span>
                                <div id="semesterGpaResult" class="display-4 fw-bold text-primary mb-1">0.00</div>
                                <span id="semesterBadge" class="badge bg-info px-3 py-2 rounded-pill fs-6">تقدير غير معروف</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted fw-bold d-block mb-1">المعدل التراكمي الإجمالي (Cumulative GPA)</span>
                                <div id="cumulativeGpaResult" class="display-4 fw-bold text-success mb-1">0.00</div>
                                <span id="cumulativeBadge" class="badge bg-success px-3 py-2 rounded-pill fs-6">ممتاز</span>
                            </div>
                        </div>
                        <div id="statusAlert" class="alert alert-primary mb-0 mt-4 rounded-3 text-start d-flex align-items-center gap-3">
                            <i class="fas fa-award fs-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1" id="statusTitle">التقدير الأكاديمي</h6>
                                <p class="mb-0 small" id="statusText">أداء أكاديمي متميز، استمر في التفوق!</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coursesContainer = document.getElementById('coursesContainer');
    const addCourseBtn = document.getElementById('addCourseBtn');
    const calculateBtn = document.getElementById('calculateBtn');
    const resetBtn = document.getElementById('resetBtn');
    const gpaScale = document.getElementById('gpaScale');
    const includeCumulative = document.getElementById('includeCumulative');
    const cumulativeSection = document.getElementById('cumulativeSection');

    const grades4 = [
        { label: 'A+ (4.0 - ممتاز مرتفع)', value: 4.0 },
        { label: 'A  (3.75 - ممتاز)', value: 3.75 },
        { label: 'B+ (3.5 - جيد جداً مرتفع)', value: 3.5 },
        { label: 'B  (3.0 - جيد جداً)', value: 3.0 },
        { label: 'C+ (2.5 - جيد مرتفع)', value: 2.5 },
        { label: 'C  (2.0 - جيد)', value: 2.0 },
        { label: 'D+ (1.5 - مقبول مرتفع)', value: 1.5 },
        { label: 'D  (1.0 - مقبول)', value: 1.0 },
        { label: 'F  (0.0 - راسب)', value: 0.0 }
    ];

    function createCourseRow(name = '', hours = 3, grade = 4.0) {
        const tr = document.createElement('tr');
        const scale = gpaScale.value;

        let gradeOptionsHtml = '';
        if (scale === '100') {
            gradeOptionsHtml = `<input type="number" class="form-control course-score" min="0" max="100" value="85" placeholder="من 100">`;
        } else {
            gradeOptionsHtml = `<select class="form-select course-grade">`;
            grades4.forEach(g => {
                const val = scale === '5' ? (g.value * 1.25).toFixed(2) : g.value;
                gradeOptionsHtml += `<option value="${val}" ${g.value === grade ? 'selected' : ''}>${g.label}</option>`;
            });
            gradeOptionsHtml += `</select>`;
        }

        tr.innerHTML = `
            <td><input type="text" class="form-control course-name" value="${name}" placeholder="اسم المادة"></td>
            <td>
                <select class="form-select course-hours">
                    <option value="1" ${hours == 1 ? 'selected' : ''}>1 ساعة</option>
                    <option value="2" ${hours == 2 ? 'selected' : ''}>2 ساعات</option>
                    <option value="3" ${hours == 3 ? 'selected' : ''}>3 ساعات</option>
                    <option value="4" ${hours == 4 ? 'selected' : ''}>4 ساعات</option>
                    <option value="5" ${hours == 5 ? 'selected' : ''}>5 ساعات</option>
                </select>
            </td>
            <td>${gradeOptionsHtml}</td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn rounded-circle">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;

        tr.querySelector('.remove-row-btn').addEventListener('click', function() {
            if (coursesContainer.children.length > 1) {
                tr.remove();
            } else {
                alert('يجب أن تحتوي الحاسبة على مادة واحدة على الأقل');
            }
        });

        coursesContainer.appendChild(tr);
    }

    // إضافة 4 مواد افتراضية
    ['رياضيات تفاضلية', 'برمجة حاسوب', 'فيزياء عامة', 'مهارات تواصل'].forEach(n => createCourseRow(n));

    addCourseBtn.addEventListener('click', function() {
        createCourseRow();
    });

    includeCumulative.addEventListener('change', function() {
        if (this.checked) {
            cumulativeSection.classList.remove('d-none');
        } else {
            cumulativeSection.classList.add('d-none');
        }
    });

    gpaScale.addEventListener('change', function() {
        coursesContainer.innerHTML = '';
        ['رياضيات تفاضلية', 'برمجة حاسوب', 'فيزياء عامة', 'مهارات تواصل'].forEach(n => createCourseRow(n));
    });

    resetBtn.addEventListener('click', function() {
        coursesContainer.innerHTML = '';
        ['رياضيات تفاضلية', 'برمجة حاسوب', 'فيزياء عامة', 'مهارات تواصل'].forEach(n => createCourseRow(n));
        document.getElementById('resultsCard').classList.add('d-none');
        document.getElementById('prevHours').value = '';
        document.getElementById('prevGpa').value = '';
    });

    calculateBtn.addEventListener('click', function() {
        const scale = parseFloat(gpaScale.value);
        let totalSemesterPoints = 0;
        let totalSemesterHours = 0;

        const rows = coursesContainer.querySelectorAll('tr');
        rows.forEach(tr => {
            const hours = parseFloat(tr.querySelector('.course-hours').value) || 0;
            let points = 0;

            if (scale === 100) {
                points = parseFloat(tr.querySelector('.course-score').value) || 0;
            } else {
                points = parseFloat(tr.querySelector('.course-grade').value) || 0;
            }

            totalSemesterHours += hours;
            totalSemesterPoints += (points * hours);
        });

        if (totalSemesterHours === 0) {
            alert('يرجى اختيار ساعات المادة بشكل صحيح');
            return;
        }

        const semesterGpa = (totalSemesterPoints / totalSemesterHours);

        let cumulativeGpa = semesterGpa;
        if (includeCumulative.checked) {
            const prevH = parseFloat(document.getElementById('prevHours').value) || 0;
            const prevG = parseFloat(document.getElementById('prevGpa').value) || 0;

            if (prevH > 0) {
                const totalAllHours = prevH + totalSemesterHours;
                const totalAllPoints = (prevH * prevG) + totalSemesterPoints;
                cumulativeGpa = totalAllPoints / totalAllHours;
            }
        }

        const semResult = document.getElementById('semesterGpaResult');
        const cumResult = document.getElementById('cumulativeGpaResult');
        const semBadge = document.getElementById('semesterBadge');
        const cumBadge = document.getElementById('cumulativeBadge');
        const statusText = document.getElementById('statusText');
        const statusTitle = document.getElementById('statusTitle');

        if (scale === 100) {
            semResult.textContent = semesterGpa.toFixed(2) + '%';
            cumResult.textContent = cumulativeGpa.toFixed(2) + '%';
        } else {
            semResult.textContent = semesterGpa.toFixed(2) + ' / ' + scale;
            cumResult.textContent = cumulativeGpa.toFixed(2) + ' / ' + scale;
        }

        function getRating(gpa, scaleVal) {
            let ratio = gpa / scaleVal;
            if (scaleVal === 100) ratio = gpa / 100;
            if (ratio >= 0.88) return { text: 'ممتاز مرتفع', bg: 'bg-success' };
            if (ratio >= 0.76) return { text: 'جيد جداً', bg: 'bg-primary' };
            if (ratio >= 0.65) return { text: 'جيد', bg: 'bg-info' };
            if (ratio >= 0.50) return { text: 'مقبول', bg: 'bg-warning' };
            return { text: 'ضعيف / إنذار', bg: 'bg-danger' };
        }

        const sRating = getRating(semesterGpa, scale);
        const cRating = getRating(cumulativeGpa, scale);

        semBadge.textContent = sRating.text;
        semBadge.className = `badge ${sRating.bg} px-3 py-2 rounded-pill fs-6`;

        cumBadge.textContent = cRating.text;
        cumBadge.className = `badge ${cRating.bg} px-3 py-2 rounded-pill fs-6`;

        statusTitle.textContent = `التقدير العام: ${cRating.text}`;
        statusText.textContent = `إجمالي عدد ساعات الفصل الحالي: ${totalSemesterHours} ساعة معتمدة.`;

        document.getElementById('resultsCard').classList.remove('d-none');
        document.getElementById('resultsCard').scrollIntoView({ behavior: 'smooth' });
    });
});
</script>
