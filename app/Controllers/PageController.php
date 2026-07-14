<?php

namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    public function about(): void
    {
        $this->view('front/about');
    }

    public function contact(): void
    {
        $this->view('front/contact');
    }
}
