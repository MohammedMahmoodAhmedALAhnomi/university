<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Schedule;
use App\Models\Major;
use App\Models\Level;
use App\Models\Semester;
use App\Config\Database;
use App\Traits\Uploadable;

class ScheduleController
{
    use Uploadable;

    public function index(): void
    {
        $schedules = Schedule::getAllWithDetails();

        View::render('admin/schedule/index', [
            'pageTitle' => 'إدارة جداول المحاضرات والامتحانات',
            'schedules' => $schedules,
        ]);
    }

    public function create(): void
    {
        $majors = Major::getActive();
        $levels = Level::getActive();
        $semesters = Semester::getActive();

        View::render('admin/schedule/create', [
            'pageTitle' => 'إضافة موعد جدول جديد',
            'majors' => $majors,
            'levels' => $levels,
            'semesters' => $semesters,
        ]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('danger', 'رمز الحماية غير صالح');
            redirect(url('/admin/schedule/create'));
            return;
        }

        $majorId = (int)($_POST['major_id'] ?? 0);
        $levelId = (int)($_POST['level_id'] ?? 0);
        $semesterId = !empty($_POST['semester_id']) ? (int)$_POST['semester_id'] : null;
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? 'lecture';
        $subjectName = trim($_POST['subject_name'] ?? '');
        $doctorName = trim($_POST['doctor_name'] ?? '');
        $dayOfWeek = $_POST['day_of_week'] ?? null;
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        $examDate = !empty($_POST['exam_date']) ? $_POST['exam_date'] : null;
        $locationHall = trim($_POST['location_hall'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $userId = $_SESSION['user_id'] ?? 1;

        if (!$majorId || !$levelId || empty($title) || empty($subjectName)) {
            flash('danger', 'يرجى ملء جميع الحقول المطلوبة (التخصص، المستوى، عنوان الجدول، واسم المادة)');
            redirect(url('/admin/schedule/create'));
            return;
        }

        $groupName = trim($_POST['group_name'] ?? '');
        $sectionCode = trim($_POST['section_code'] ?? '');

        if (empty($sectionCode)) {
            $majors = Major::getActive();
            $levels = Level::getActive();
            $mCode = 'IT';
            foreach ($majors as $m) {
                if ($m->id == $majorId) {
                    $nM = mb_strtolower($m->name);
                    if (str_contains($nM, 'تقنية') || str_contains($nM, 'it')) $mCode = 'IT';
                    elseif (str_contains($nM, 'علوم') || str_contains($nM, 'cs')) $mCode = 'CS';
                    elseif (str_contains($nM, 'شبكات') || str_contains($nM, 'net')) $mCode = 'NET';
                    elseif (str_contains($nM, 'أمن') || str_contains($nM, 'cyber')) $mCode = 'CYBER';
                    elseif (str_contains($nM, 'ذكاء') || str_contains($nM, 'ai')) $mCode = 'AI';
                    elseif (str_contains($nM, 'نظم') || str_contains($nM, 'is')) $mCode = 'IS';
                    else $mCode = 'MAJOR' . $m->id;
                    break;
                }
            }

            $lNum = '1';
            foreach ($levels as $l) {
                if ($l->id == $levelId) {
                    $lNum = (string)$l->level_number;
                    break;
                }
            }

            $gNum = 'G1';
            if (!empty($groupName)) {
                if (preg_match('/(\d+)/', $groupName, $gM)) {
                    $gNum = 'G' . $gM[1];
                } elseif (preg_match('/([a-zA-Z])/', $groupName, $gM)) {
                    $gNum = 'G' . strtoupper($gM[1]);
                }
            }

            $sectionCode = "{$mCode}{$lNum}_{$gNum}";
        }

        Database::insert('schedules', [
            'major_id' => $majorId,
            'level_id' => $levelId,
            'group_name' => !empty($groupName) ? $groupName : 'مجموعة 1',
            'section_code' => $sectionCode,
            'semester_id' => $semesterId,
            'title' => $title,
            'type' => $type,
            'subject_name' => $subjectName,
            'doctor_name' => $doctorName,
            'day_of_week' => $type === 'lecture' ? $dayOfWeek : null,
            'start_time' => $type === 'lecture' && !empty($startTime) ? $startTime : null,
            'end_time' => $type === 'lecture' && !empty($endTime) ? $endTime : null,
            'exam_date' => $type === 'exam' && !empty($examDate) ? $examDate : null,
            'location_hall' => $locationHall,
            'notes' => $notes,
            'created_by' => $userId,
        ]);

        log_activity('إضافة عنصر في الجدول الدراسي', 'schedules', null, "العنوان: $title");

        flash('success', 'تم حفظ الجدول بنجاح');
        redirect(url('/admin/schedule'));
    }

    public function importForm(): void
    {
        View::render('admin/schedule/import', [
            'pageTitle' => 'استيراد جدول كامل من ملف Excel (.xlsx / .csv)',
        ]);
    }

    public function downloadTemplate(): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=schedule_template.csv');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, [
            'التخصص', 'المستوى', 'المجموعة / القروب', 'رمز الكود', 'النوع', 'اسم المادة', 'العنوان', 
            'اسم الدكتور', 'اليوم', 'وقت البداية', 'وقت النهاية', 'تاريخ الامتحان', 'القاعة', 'ملاحظات'
        ]);

        fputcsv($out, [
            'تقنية المعلومات', '1', 'قروب 1', 'IT1_G1', 'محاضرة', 'مقدمة حاسوب', 'محاضرة نظري', 
            'د. أحمد سالم', 'الأحد', '08:00', '10:00', '', 'قاعة 101', 'حضور إجباري'
        ]);

        fputcsv($out, [
            'تقنية المعلومات', '1', 'قروب 2', 'IT1_G2', 'محاضرة', 'برمجة حاسوب 1', 'محاضرة نظري وعملي', 
            'د. علي عبدالله', 'الأحد', '10:00', '12:00', '', 'معمل 1', ''
        ]);

        fputcsv($out, [
            'علوم الحاسوب', '2', 'قروب 1', 'CS2_G1', 'محاضرة', 'هياكل بيانات', 'محاضرة نظري وعملي', 
            'د. خالد عمر', 'الإثنين', '10:00', '12:00', '', 'معمل 2', 'احضار اللابتوب'
        ]);

        fputcsv($out, [
            'شبكات الحاسوب', 'المستوى الثالث', 'قروب 1', 'NET3_G1', 'محاضرة', 'أمن الشبكات', 'محاضرة أسبوعية', 
            'د. ياسر محمود', 'الثلاثاء', '12:00', '02:00', '', 'قاعة 204', ''
        ]);

        fclose($out);
        exit;
    }

    private function parseXlsxFile(string $filePath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $sharedStrings = [];
        $stringsXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXmlContent) {
            $xml = @simplexml_load_string($stringsXmlContent);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string)$val->t;
                    } elseif (isset($val->r)) {
                        $str = '';
                        foreach ($val->r as $r) {
                            $str .= (string)$r->t;
                        }
                        $sharedStrings[] = $str;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $rows = [];
        // Read ALL worksheets (Sheets) inside the Excel file
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat && str_contains($stat['name'], 'worksheets/sheet')) {
                $sheetContent = $zip->getFromIndex($i);
                if (!$sheetContent) continue;

                $sheetXml = @simplexml_load_string($sheetContent);
                if (!$sheetXml || !isset($sheetXml->sheetData->row)) continue;

                foreach ($sheetXml->sheetData->row as $rowXml) {
                    $row = [];
                    foreach ($rowXml->c as $cell) {
                        $val = (string)$cell->v;
                        $type = (string)$cell['t'];
                        if ($type === 's') {
                            $val = $sharedStrings[(int)$val] ?? '';
                        } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                            $val = (string)$cell->is->t;
                        }
                        $row[] = trim($val);
                    }
                    if (!empty(array_filter($row))) {
                        $rows[] = $row;
                    }
                }
            }
        }

        $zip->close();
        return $rows;
    }

    private function normalizeArabic(string $str): string
    {
        $str = mb_strtolower(trim($str));
        $str = strtr($str, ['١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9', '٠'=>'0']);
        $str = preg_replace('/[أإآ]/u', 'ا', $str);
        $str = preg_replace('/[ى]/u', 'ي', $str);
        $str = preg_replace('/[ة]/u', 'ه', $str);
        return $str;
    }

    public function processImport(): void
    {
        if (!verify_csrf()) {
            flash('danger', 'رمز الحماية غير صالح');
            redirect(url('/admin/schedule/import'));
            return;
        }

        if (empty($_FILES['csv_file']['tmp_name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            flash('danger', 'يرجى اختيار ملف Excel (.xlsx / .csv) صالح للرفع');
            redirect(url('/admin/schedule/import'));
            return;
        }

        $filePath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $rows = [];
        if ($ext === 'xlsx') {
            $rows = $this->parseXlsxFile($filePath);
        } else {
            $handle = fopen($filePath, 'r');
            if ($handle) {
                while (($r = fgetcsv($handle)) !== false) {
                    $rows[] = $r;
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            flash('danger', 'تعذر قراءة بيانات ملف Excel المرفوع. تأكد من سلامة الملف.');
            redirect(url('/admin/schedule/import'));
            return;
        }

        // Dynamic Flexible Column Mapping (updated dynamically upon finding any header row)
        $map = [
            'major' => null, 'level' => null, 'group' => null, 'sub_group' => null, 'code' => null, 'type' => null, 'subject' => null, 'title' => null,
            'doctor' => null, 'day' => null, 'start_time' => null, 'end_time' => null,
            'time' => null, 'exam_date' => null, 'hall' => null, 'notes' => null
        ];

        $headerKeywords = ['تخصص', 'مستوى', 'مجموعة', 'قروب', 'شعبة', 'مادة', 'مقرر', 'دكتور', 'محاضر', 'استاذ', 'أستاذ', 'يوم', 'قاعة', 'معمل', 'مختبر', 'مكان', 'توقيت', 'وقت', 'subject', 'course', 'doctor', 'hall', 'major', 'level', 'group', 'section', 'code'];

        $majors = Major::getActive();
        $levels = Level::getActive();
        $userId = $_SESSION['user_id'] ?? 1;

        $insertedCount = 0;
        $failedCount = 0;

        $daysMap = [
            'الاحد' => 'Sunday', 'sunday' => 'Sunday',
            'الاثنين' => 'Monday', 'monday' => 'Monday',
            'الثلاثاء' => 'Tuesday', 'tuesday' => 'Tuesday',
            'الاربعاء' => 'Wednesday', 'wednesday' => 'Wednesday',
            'الخميس' => 'Thursday', 'thursday' => 'Thursday',
            'الجمعة' => 'Friday', 'الجمعه' => 'Friday', 'friday' => 'Friday',
            'السبت' => 'Saturday', 'saturday' => 'Saturday',
        ];

        foreach ($rows as $row) {
            if (empty(array_filter($row))) continue;

            $joinedRowText = $this->normalizeArabic(implode(' ', $row));
            $matchCount = 0;
            foreach ($headerKeywords as $kw) {
                if (str_contains($joinedRowText, $kw)) {
                    $matchCount++;
                }
            }

            // If this row is a header row, update mapping for subsequent rows
            if ($matchCount >= 2) {
                foreach ($row as $colIdx => $colName) {
                    $normalizedName = $this->normalizeArabic((string)$colName);
                    if (empty($normalizedName)) continue;

                    if (str_contains($normalizedName, 'تخصص') || str_contains($normalizedName, 'major') || str_contains($normalizedName, 'قسم') || str_contains($normalizedName, 'department') || str_contains($normalizedName, 'كلية')) {
                        $map['major'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'مستوي') || str_contains($normalizedName, 'müstawa') || str_contains($normalizedName, 'level') || str_contains($normalizedName, 'سنه') || str_contains($normalizedName, 'سنة') || str_contains($normalizedName, 'دفعه') || str_contains($normalizedName, 'دفعة') || str_contains($normalizedName, 'year')) {
                        $map['level'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'عملي') || str_contains($normalizedName, 'مجموعة عملي') || str_contains($normalizedName, 'مجموعة المعمل') || str_contains($normalizedName, 'شريحة') || str_contains($normalizedName, 'subgroup') || str_contains($normalizedName, 'sub_group') || str_contains($normalizedName, 'practical') || str_contains($normalizedName, 'lab')) {
                        $map['sub_group'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'مجموع') || str_contains($normalizedName, 'قروب') || str_contains($normalizedName, 'جروب') || str_contains($normalizedName, 'شعب') || str_contains($normalizedName, 'group') || str_contains($normalizedName, 'section')) {
                        $map['group'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'رمز') || str_contains($normalizedName, 'كود') || str_contains($normalizedName, 'code')) {
                        $map['code'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'نوع') || str_contains($normalizedName, 'type') || str_contains($normalizedName, 'نمط')) {
                        $map['type'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'ماده') || str_contains($normalizedName, 'مادة') || str_contains($normalizedName, 'مقرر') || str_contains($normalizedName, 'subject') || str_contains($normalizedName, 'course')) {
                        $map['subject'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'عنوان') || str_contains($normalizedName, 'title') || str_contains($normalizedName, 'وصف')) {
                        $map['title'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'دكتور') || str_contains($normalizedName, 'محاضر') || str_contains($normalizedName, 'استاذ') || str_contains($normalizedName, 'أستاذ') || str_contains($normalizedName, 'مدرس') || str_contains($normalizedName, 'doctor') || str_contains($normalizedName, 'instructor') || str_contains($normalizedName, 'lecturer')) {
                        $map['doctor'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'يوم') || str_contains($normalizedName, 'day')) {
                        $map['day'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'بداية') || str_contains($normalizedName, 'من') || str_contains($normalizedName, 'start')) {
                        $map['start_time'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'نهاية') || str_contains($normalizedName, 'الي') || str_contains($normalizedName, 'إلى') || str_contains($normalizedName, 'end')) {
                        $map['end_time'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'توقيت') || str_contains($normalizedName, 'وقت') || str_contains($normalizedName, 'زمان') || str_contains($normalizedName, 'time')) {
                        $map['time'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'امتحان') || str_contains($normalizedName, 'اختبار') || str_contains($normalizedName, 'تاريخ') || str_contains($normalizedName, 'exam') || str_contains($normalizedName, 'date')) {
                        $map['exam_date'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'قاعه') || str_contains($normalizedName, 'قاعة') || str_contains($normalizedName, 'معمل') || str_contains($normalizedName, 'مختبر') || str_contains($normalizedName, 'مكان') || str_contains($normalizedName, 'موقع') || str_contains($normalizedName, 'hall') || str_contains($normalizedName, 'room')) {
                        $map['hall'] = $colIdx;
                    } elseif (str_contains($normalizedName, 'ملاحظ') || str_contains($normalizedName, 'notes') || str_contains($normalizedName, 'تنبيه')) {
                        $map['notes'] = $colIdx;
                    }
                }
                continue; // Skip header row insertion into DB
            }
            if (empty(array_filter($row))) continue;

            $majorRaw = $map['major'] !== null ? trim($row[$map['major']] ?? '') : '';
            $levelRaw = $map['level'] !== null ? trim($row[$map['level']] ?? '') : '';
            $groupRaw = $map['group'] !== null ? trim($row[$map['group']] ?? '') : '';
            $subGroupRaw = $map['sub_group'] !== null ? trim($row[$map['sub_group']] ?? '') : '';
            $codeRaw = $map['code'] !== null ? trim($row[$map['code']] ?? '') : '';
            $typeRaw = $map['type'] !== null ? mb_strtolower(trim($row[$map['type']] ?? '')) : '';
            $subjectName = $map['subject'] !== null ? trim($row[$map['subject']] ?? '') : '';
            $title = $map['title'] !== null ? trim($row[$map['title']] ?? '') : '';
            $doctorName = $map['doctor'] !== null ? trim($row[$map['doctor']] ?? '') : '';
            $dayRaw = $map['day'] !== null ? trim($row[$map['day']] ?? '') : '';
            $startTime = $map['start_time'] !== null ? trim($row[$map['start_time']] ?? '') : '';
            $endTime = $map['end_time'] !== null ? trim($row[$map['end_time']] ?? '') : '';
            $timeCombined = $map['time'] !== null ? trim($row[$map['time']] ?? '') : '';
            $examDate = $map['exam_date'] !== null ? trim($row[$map['exam_date']] ?? '') : '';
            $hall = $map['hall'] !== null ? trim($row[$map['hall']] ?? '') : '';
            $notes = $map['notes'] !== null ? trim($row[$map['notes']] ?? '') : '';

            // Smart cell inspection heuristics if explicit column mapping missed a field:
            foreach ($row as $cellVal) {
                $cVal = trim((string)$cellVal);
                if (empty($cVal)) continue;
                $normCVal = $this->normalizeArabic($cVal);

                // SubGroup (Practical/Lab) heuristic match (e.g. عملي 1 / معمل 2 / P1 / Lab 1)
                if (empty($subGroupRaw) && (str_contains($normCVal, 'عملي') || str_contains($normCVal, 'مجموع عملي') || preg_match('/^p\d+$/i', $cVal) || preg_match('/^lab\s*\d+$/i', $cVal))) {
                    $subGroupRaw = $cVal;
                }

                // Group heuristic match (e.g. قروب 1 / مجموعة 2 / شعبة A / Group 1 / G1)
                if (empty($groupRaw) && $cVal !== $subGroupRaw && (str_contains($normCVal, 'قروب') || str_contains($normCVal, 'جروب') || str_contains($normCVal, 'مجموع') || str_contains($normCVal, 'شعب') || str_contains($normCVal, 'group') || preg_match('/^g\d+$/i', $cVal))) {
                    $groupRaw = $cVal;
                }

                // Section Code heuristic match (e.g. IT1_G1 / CS2_G2 / NET3_G1_P1)
                if (empty($codeRaw) && preg_match('/^[a-zA-Z0-9_\-]{4,18}$/', $cVal) && str_contains($cVal, '_')) {
                    $codeRaw = strtoupper($cVal);
                }

                // Doctor heuristic match (e.g. د. محمدعلي / أ.د أحمد / دكتور علي / أستاذ عبدالله)
                if (empty($doctorName) && (str_contains($normCVal, 'د.') || str_contains($normCVal, 'دكتور') || str_contains($normCVal, 'أ.د') || str_contains($normCVal, 'استاذ') || str_contains($normCVal, 'أستاذ') || str_contains($normCVal, 'مدرس'))) {
                    $doctorName = $cVal;
                }

                // Hall heuristic match (e.g. قاعة 101 / معمل 2 / مختبر الشبكات / قاعه)
                if (empty($hall) && (str_contains($normCVal, 'قاعه') || str_contains($normCVal, 'قاعة') || str_contains($normCVal, 'معمل') || str_contains($normCVal, 'مختبر') || str_contains($normCVal, 'hall') || str_contains($normCVal, 'lab'))) {
                    $hall = $cVal;
                }

                // Day heuristic match
                if (empty($dayRaw)) {
                    foreach (array_keys($daysMap) as $dKey) {
                        if (str_contains($normCVal, $dKey)) {
                            $dayRaw = $cVal;
                            break;
                        }
                    }
                }
            }

            // Fallback for empty Subject: find first cell that is not doctor, hall, day, major, level, group, subGroup, code
            if (empty($subjectName)) {
                foreach ($row as $cellVal) {
                    $cVal = trim((string)$cellVal);
                    if (empty($cVal)) continue;
                    if ($cVal === $doctorName || $cVal === $hall || $cVal === $dayRaw || $cVal === $majorRaw || $cVal === $levelRaw || $cVal === $groupRaw || $cVal === $subGroupRaw || $cVal === $codeRaw) continue;
                    if (strlen($cVal) >= 3 && !is_numeric($cVal)) {
                        $subjectName = $cVal;
                        break;
                    }
                }
            }

            if (empty($subjectName)) continue;
            if (empty($title)) $title = 'جدول دراسي';

            // Split combined time column e.g. "08:00 - 10:00" or "8:00 الى 10:00"
            if (empty($startTime) && !empty($timeCombined)) {
                $parts = preg_split('/[\-\—\–\|]|الى|إلى|to/u', $timeCombined);
                if (count($parts) >= 2) {
                    $startTime = trim($parts[0]);
                    $endTime = trim($parts[1]);
                } else {
                    $startTime = trim($parts[0]);
                }
            }

            // Intelligent Major Matching
            $matchedMajorId = null;
            $normMajorRaw = $this->normalizeArabic($majorRaw);
            foreach ($majors as $m) {
                $normMName = $this->normalizeArabic($m->name);
                if (!empty($normMajorRaw) && ($m->id == $majorRaw || mb_stripos($normMName, $normMajorRaw) !== false || mb_stripos($normMajorRaw, $normMName) !== false)) {
                    $matchedMajorId = $m->id;
                    break;
                }
            }

            if (!$matchedMajorId && !empty($normMajorRaw)) {
                $majorAliasMap = [
                    'تقنيه' => ['تقنية المعلومات', 'IT', 'information technology'],
                    'معلومات' => ['تقنية المعلومات', 'IT', 'نظم المعلومات', 'IS'],
                    'it' => ['تقنية المعلومات', 'IT'],
                    'حاسوب' => ['علوم الحاسوب', 'CS', 'computer science'],
                    'حاسب' => ['علوم الحاسوب', 'CS'],
                    'cs' => ['علوم الحاسوب', 'CS'],
                    'شبكات' => ['شبكات الحاسوب', 'Networking', 'network'],
                    'network' => ['شبكات الحاسوب', 'Networking'],
                    'امن' => ['الأمن السيبراني', 'Cyber Security', 'cyber'],
                    'cyber' => ['الأمن السيبراني', 'Cyber Security'],
                    'ذكاء' => ['الذكاء الاصطناعي', 'AI', 'artificial intelligence'],
                    'ai' => ['الذكاء الاصطناعي', 'AI'],
                    'نظم' => ['نظم المعلومات', 'IS', 'information systems'],
                    'is' => ['نظم المعلومات', 'IS'],
                ];
                foreach ($majorAliasMap as $kw => $aliases) {
                    if (str_contains($normMajorRaw, $kw)) {
                        foreach ($majors as $m) {
                            $normMName = $this->normalizeArabic($m->name);
                            foreach ($aliases as $alias) {
                                $normAlias = $this->normalizeArabic($alias);
                                if (str_contains($normMName, $normAlias) || str_contains($normAlias, $normMName)) {
                                    $matchedMajorId = $m->id;
                                    break 3;
                                }
                            }
                        }
                    }
                }
            }

            if (!$matchedMajorId && !empty($majorRaw)) {
                try {
                    $newMajorId = Database::insert('majors', [
                        'name' => $majorRaw,
                        'code' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $majorRaw) ?: 'M' . time(), 0, 10)),
                        'is_active' => 1
                    ]);
                    $matchedMajorId = $newMajorId;
                    $majors = Major::getActive();
                } catch (\Throwable $e) {
                    if (!empty($majors)) $matchedMajorId = $majors[0]->id;
                }
            } elseif (!$matchedMajorId && !empty($majors)) {
                $matchedMajorId = $majors[0]->id;
            }

            // Intelligent Level Matching
            $matchedLevelId = null;
            $normLevelRaw = $this->normalizeArabic($levelRaw);
            foreach ($levels as $l) {
                $normLName = $this->normalizeArabic($l->name);
                if (!empty($normLevelRaw) && ($l->id == $levelRaw || $l->level_number == $levelRaw || mb_stripos($normLName, $normLevelRaw) !== false || mb_stripos($normLevelRaw, (string)$l->level_number) !== false)) {
                    $matchedLevelId = $l->id;
                    break;
                }
            }

            if (!$matchedLevelId && !empty($normLevelRaw)) {
                $levelAliasMap = [
                    1 => ['اول', 'الأول', 'الاول', '1', '1st', 'first', 'l1', 'level 1', 'مستوى 1', 'سنة 1'],
                    2 => ['ثاني', 'الثاني', '2', '2nd', 'second', 'l2', 'level 2', 'مستوى 2', 'سنة 2'],
                    3 => ['ثالث', 'الثالث', '3', '3rd', 'third', 'l3', 'level 3', 'مستوى 3', 'سنة 3'],
                    4 => ['رابع', 'الرابع', '4', '4th', 'fourth', 'l4', 'level 4', 'مستوى 4', 'سنة 4'],
                    5 => ['خامس', 'الخامس', '5', '5th', 'fifth', 'l5', 'level 5', 'مستوى 5', 'سنة 5'],
                ];
                foreach ($levels as $l) {
                    $num = (int)$l->level_number;
                    if (isset($levelAliasMap[$num])) {
                        foreach ($levelAliasMap[$num] as $kw) {
                            $normKw = $this->normalizeArabic($kw);
                            if (str_contains($normLevelRaw, $normKw)) {
                                $matchedLevelId = $l->id;
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!$matchedLevelId && !empty($levelRaw)) {
                try {
                    preg_match('/(\d+)/', $levelRaw, $numM);
                    $lvlNum = isset($numM[1]) ? (int)$numM[1] : (count($levels) + 1);
                    $newLevelId = Database::insert('levels', [
                        'name' => $levelRaw,
                        'level_number' => $lvlNum,
                        'is_active' => 1
                    ]);
                    $matchedLevelId = $newLevelId;
                    $levels = Level::getActive();
                } catch (\Throwable $e) {
                    if (!empty($levels)) $matchedLevelId = $levels[0]->id;
                }
            } elseif (!$matchedLevelId && !empty($levels)) {
                $matchedLevelId = $levels[0]->id;
            }

            $lNum = '1';
            foreach ($levels as $l) {
                if ($l->id == $matchedLevelId) {
                    $lNum = (string)$l->level_number;
                    break;
                }
            }

            // 1. Format group_name e.g. "مستوى 1 - قروب 1" or "مستوى 2 - قروب 2"
            $cleanGroup = !empty($groupRaw) ? $groupRaw : 'قروب 1';
            if (preg_match('/(\d+)/', $this->normalizeArabic($cleanGroup), $gMatch)) {
                $groupNum = $gMatch[1];
                $formattedGroupName = "مستوى {$lNum} - قروب {$groupNum}";
            } else {
                $formattedGroupName = "مستوى {$lNum} - {$cleanGroup}";
            }

            // 2. Auto Generate section_code (e.g. IT1_G1, CS2_G1, NET3_G1, CYBER4_G1...)
            $sectionCode = !empty($codeRaw) ? strtoupper($codeRaw) : '';
            if (empty($sectionCode)) {
                $mCode = 'IT';
                foreach ($majors as $m) {
                    if ($m->id == $matchedMajorId) {
                        $mCode = get_major_badge_code($m->name);
                        break;
                    }
                }

                $gNum = 'G1';
                if (preg_match('/(\d+)/', $this->normalizeArabic($cleanGroup), $gM)) {
                    $gNum = 'G' . $gM[1];
                } elseif (preg_match('/([a-zA-Z])/', $cleanGroup, $gM)) {
                    $gNum = 'G' . strtoupper($gM[1]);
                }

                $pNum = '';
                if (!empty($subGroupRaw)) {
                    $normSubG = $this->normalizeArabic($subGroupRaw);
                    if (preg_match('/(\d+)/', $normSubG, $pM)) {
                        $pNum = '_P' . $pM[1];
                    } elseif (preg_match('/([a-zA-Z])/', $subGroupRaw, $pM)) {
                        $pNum = '_P' . strtoupper($pM[1]);
                    }
                }

                $sectionCode = "{$mCode}{$lNum}_{$gNum}{$pNum}";
            }

            $type = (str_contains($typeRaw, 'امتحان') || str_contains($typeRaw, 'اختبار') || str_contains($typeRaw, 'exam')) ? 'exam' : 'lecture';
            $normDay = $this->normalizeArabic($dayRaw);
            $dayOfWeek = null;
            foreach ($daysMap as $dK => $dV) {
                if (str_contains($normDay, $dK)) {
                    $dayOfWeek = $dV;
                    break;
                }
            }

            try {
                Database::insert('schedules', [
                    'major_id' => $matchedMajorId,
                    'level_id' => $matchedLevelId,
                    'group_name' => $formattedGroupName,
                    'sub_group_name' => !empty($subGroupRaw) ? $subGroupRaw : null,
                    'section_code' => $sectionCode,
                    'title' => $title,
                    'type' => $type,
                    'subject_name' => $subjectName,
                    'doctor_name' => $doctorName,
                    'day_of_week' => $type === 'lecture' ? $dayOfWeek : null,
                    'start_time' => $type === 'lecture' && !empty($startTime) ? $startTime : null,
                    'end_time' => $type === 'lecture' && !empty($endTime) ? $endTime : null,
                    'exam_date' => $type === 'exam' && !empty($examDate) ? date('Y-m-d H:i:s', strtotime($examDate)) : null,
                    'location_hall' => $hall,
                    'notes' => $notes,
                    'created_by' => $userId,
                ]);
                $insertedCount++;
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        log_activity('استيراد جدول كامل من ملف Excel الذكي', 'schedules', null, "عدد العناصر المضافة: $insertedCount");

        flash('success', "🎉 تم استيراد وتوزيع $insertedCount موعد بنجاح وبدقة عالية مع قراءة اسم الدكتور والقاعة والتوقيت!");
        redirect(url('/admin/schedule'));
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            try {
                $schedule = Database::fetch("SELECT file_path FROM schedules WHERE id = ?", [$id]);
                if (!empty($schedule->file_path)) {
                    $this->deleteFile($schedule->file_path);
                }
            } catch (\Throwable $e) {
                // Ignore if file_path query fails
            }
            Database::raw("DELETE FROM schedules WHERE id = ?", [$id]);
            flash('success', 'تم حذف العنصر من الجدول بنجاح');
        }
        redirect(url('/admin/schedule'));
    }
}
