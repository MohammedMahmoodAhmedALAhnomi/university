<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Major;
use App\Models\Level;
use App\Models\Semester;

class SearchController extends Controller
{
    public function index(): void
    {
        $query = trim($this->getParam('q', ''));
        $filters = [
            'major_id' => $this->getParam('major_id', ''),
            'level_id' => $this->getParam('level_id', ''),
            'semester_id' => $this->getParam('semester_id', ''),
        ];
        $results = [];

        if (!empty($query)) {
            $results = Course::search($query, $filters);
        }

        $this->view('front/search', [
            'query' => $query,
            'results' => $results,
            'majors' => Major::getActive(),
            'levels' => Level::all(),
            'semesters' => Semester::all(),
        ]);
    }
}
