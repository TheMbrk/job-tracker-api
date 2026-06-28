<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Services\WatsonNLUService;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    protected $watson;

    public function __construct(WatsonNLUService $watson)
    {
        $this->watson = $watson;
    }

    // GET /api/job-applications
    public function index()
    {
        return response()->json(JobApplication::latest()->get());
    }

    // POST /api/job-applications
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string',
            'position' => 'required|string',
            'job_description' => 'required|string',
            'job_url' => 'nullable|url',
        ]);

        // Analyze job description with Watson NLU
        $analysis = $this->watson->analyze($request->job_description);

        // Extract keywords as skills
        $skills = collect($analysis['keywords'] ?? [])
            ->pluck('text')
            ->toArray();

        // Calculate a simple match score based on keyword count
        $matchScore = min(count($skills) * 5, 100);

        $job = JobApplication::create([
            'company_name' => $request->company_name,
            'position' => $request->position,
            'job_description' => $request->job_description,
            'job_url' => $request->job_url,
            'status' => 'saved',
            'match_score' => $matchScore,
            'extracted_skills' => $skills,
            'watson_analysis' => $analysis,
        ]);

        return response()->json($job, 201);
    }

    // GET /api/job-applications/{id}
    public function show(JobApplication $jobApplication)
    {
        return response()->json($jobApplication);
    }

    // PUT /api/job-applications/{id}
    public function update(Request $request, JobApplication $jobApplication)
    {
        $request->validate([
            'status' => 'sometimes|in:saved,applied,interview,offer,rejected',
            'company_name' => 'sometimes|string',
            'position' => 'sometimes|string',
        ]);

        $jobApplication->update($request->all());

        return response()->json($jobApplication);
    }

    // DELETE /api/job-applications/{id}
    public function destroy(JobApplication $jobApplication)
    {
        $jobApplication->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}