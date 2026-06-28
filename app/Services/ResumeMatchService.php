<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class ResumeMatchService
{
    protected $watson;

    public function __construct(WatsonNLUService $watson)
    {
        $this->watson = $watson;
    }

    public function extractTextFromPdf(string $filePath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }

    public function calculateMatch(string $resumeText, string $jobDescription): array
    {
        $resumeAnalysis = $this->watson->analyze($resumeText);
        $jobAnalysis = $this->watson->analyze($jobDescription);

        $resumeSkills = collect($resumeAnalysis['keywords'] ?? [])
            ->pluck('text')
            ->map(fn($s) => strtolower($s))
            ->toArray();

        $jobSkills = collect($jobAnalysis['keywords'] ?? [])
            ->pluck('text')
            ->map(fn($s) => strtolower($s))
            ->toArray();

        // Exact matches
        $exactMatched = array_intersect($jobSkills, $resumeSkills);

        // Partial matches — check if any word in job skill appears in resume skills
        $partialMatched = [];
        foreach ($jobSkills as $jobSkill) {
            if (in_array($jobSkill, $exactMatched)) continue;
            $jobWords = explode(' ', $jobSkill);
            foreach ($resumeSkills as $resumeSkill) {
                foreach ($jobWords as $word) {
                    if (strlen($word) > 3 && str_contains($resumeSkill, $word)) {
                        $partialMatched[] = $jobSkill;
                        break 2;
                    }
                }
            }
        }

        $allMatched = array_unique(array_merge(array_values($exactMatched), $partialMatched));
        $missing = array_diff($jobSkills, $allMatched);

        $score = count($jobSkills) > 0
            ? round((count($allMatched) / count($jobSkills)) * 100)
            : 0;

        return [
            'match_score' => $score,
            'matched_skills' => $allMatched,
            'missing_skills' => array_values($missing),
            'resume_skills' => $resumeSkills,
            'job_skills' => $jobSkills,
            'recommendation' => $this->getRecommendation($score),
        ];
    }

    private function getRecommendation(int $score): string
    {
        if ($score >= 80) return 'Excellent match! Apply immediately.';
        if ($score >= 60) return 'Good match. Tailor your resume to highlight missing skills.';
        if ($score >= 40) return 'Moderate match. Consider upskilling before applying.';
        return 'Low match. Focus on building the missing skills first.';
    }
}