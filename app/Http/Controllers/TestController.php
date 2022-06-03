<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use Illuminate\Routing\Controller as BaseController;

class TestController extends BaseController
{
    public function index() {
        TestJob::dispatch();
    }
}
