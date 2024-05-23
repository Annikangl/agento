<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Controller;
use App\Models\ScrapperTask;
use Illuminate\Http\Request;

class ScrapperTaskController extends Controller
{
    /**
     * @throws \Exception
     */
    public function downloadLog(ScrapperTask $scrapperTask)
    {
        $file = base_path() . '/docker/scrappers/src/run-logs/' . $scrapperTask->task_log_path;

        if (!file_exists($file)) {
            throw new \Exception('Log file not found');
        }

        return response()->download($file);
    }
}
