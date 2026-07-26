<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Major;
use App\Models\Course;
use App\Models\Level;
use App\Models\Announcement;
use App\Models\File;

class HomeController extends Controller
{
    public function index(): void
    {
        $majors = Major::getWithCourseCount();
        $announcements = Announcement::getPinned();
        $courseCount = Course::count();
        $fileCount = File::count();
        $majorCount = Major::count();
        $newFilesCount = File::getRecentCount(48);
        $levels = Level::getActive();

        $this->view('front/home', [
            'majors' => $majors,
            'announcements' => $announcements,
            'courseCount' => $courseCount,
            'fileCount' => $fileCount,
            'majorCount' => $majorCount,
            'newFilesCount' => $newFilesCount,
            'levels' => $levels,
        ]);
    }
}
