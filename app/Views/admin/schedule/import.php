<div class="row justify-content-center py-3">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white p-4 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-white mb-1">
                            <i class="fas fa-file-excel me-2"></i>استيراد وفرز الجداول تلقائياً من ملف Excel
                        </h4>
                        <p class="text-white-50 small mb-0">قم برفع ملف Excel واحد (.xlsx أو .csv) يضم جداول جميع التخصصات والمستويات، وسيقوم النظام بقراءته وتوزيعه تلقائياً!</p>
                    </div>
                    <a href="<?php echo url('/admin/schedule/template'); ?>" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm text-dark fw-bold">
                        <i class="fas fa-download text-success me-1"></i> تحميل نموذج Excel جاهز
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="<?php echo url('/admin/schedule/import/upload'); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <!-- دليل الأعمدة والتعليمات الذكية -->
                    <div class="alert alert-light border border-primary border-2 rounded-4 p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-magic text-primary me-2"></i>كيف يفرز النظام ملف الإكسل تلقائياً؟</h5>
                        <p class="text-muted small mb-3">
                            يقرأ النظام كل صف في ملف الإكسل ويطابق عمود <strong>التخصص</strong> وعمود <strong>المستوى</strong> تلقائياً مع البيانات المسجلة في الجامعة:
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border">
                                    <span class="badge bg-primary mb-2">أعمدة الملف المقبولة:</span>
                                    <ul class="list-unstyled mb-0 small text-secondary">
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>التخصص:</strong> (مثل: تقنية المعلومات، IT، علوم الحاسوب، CS، شبكات)</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>المستوى:</strong> (مثل: 1، 2، 3، المستوى الأول)</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>المجموعة / القروب:</strong> (مثل: قروب 1، قروب 2، مجموعة A)</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>رمز الكود الذكي:</strong> (مثل: IT1_G1، CS2_G2 أو توليده تلقائياً)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border">
                                    <span class="badge bg-success mb-2">باقي البيانات المفصلة:</span>
                                    <ul class="list-unstyled mb-0 small text-secondary">
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>اسم الدكتور / المحاضر</strong></li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>اليوم والتوقيت:</strong> (الأحد، 08:00 - 10:00)</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-1"></i> <strong>تاريخ الامتحان:</strong> (لنوع الامتحان)</li>
                                        <li><i class="fas fa-check text-success me-1"></i> <strong>القاعة والملاحظات</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- منطقة رفع الملف -->
                    <div class="border border-2 border-dashed rounded-4 p-5 text-center bg-light mb-4 position-relative hover-shadow transition" style="border-color: #198754 !important;">
                        <i class="fas fa-file-excel display-2 text-success mb-3"></i>
                        <h4 class="fw-bold text-dark mb-2">اختر ملف Excel أو CSV الخاص بالجداول</h4>
                        <p class="text-muted small mb-3">الصيغ المقبولة مباشـرة: .xlsx (Excel) أو .csv</p>
                        
                        <div class="col-md-7 mx-auto">
                            <input type="file" name="csv_file" class="form-control rounded-3 form-control-lg shadow-sm" accept=".xlsx,.csv" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?php echo url('/admin/schedule'); ?>" class="btn btn-light rounded-pill px-4">
                            <i class="fas fa-arrow-right me-1"></i> العودة للجداول
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow fw-bold py-2">
                            <i class="fas fa-upload me-2"></i> بدء استيراد وفرز ملف الإكسل تلقائياً
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
