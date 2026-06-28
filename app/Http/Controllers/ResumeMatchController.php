<?php

namespace App\Http\Controllers;

use App\Services\ResumeMatchService;
use Illuminate\Http\Request;

class ResumeMatchController extends Controller
{
    protected $resumeMatch;

    public function __construct(ResumeMatchService $resumeMatch)
    {
        $this->resumeMatch = $resumeMatch;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf|max:2048',
            'job_description' => 'required|string|min:50',
        ]);

        // Get the real temp path directly from the uploaded file
        $file = $request->file('resume');
        $fullPath = $file->getRealPath();

        // Extract text and calculate match
        $resumeText = $this->resumeMatch->extractTextFromPdf($fullPath);
        $result = $this->resumeMatch->calculateMatch($resumeText, $request->job_description);

        return response()->json($result);
    }
}