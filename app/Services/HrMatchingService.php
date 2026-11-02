<?php

namespace App\Services;

use App\Models\HrTalent;
use App\Models\HrClientNeed;
use App\Models\HrTalentMatch;
use Illuminate\Support\Facades\DB;

class HrMatchingService
{
    public function calculateMatchScore(HrTalent $talent, HrClientNeed $need): array
    {
        $score = 0;
        $details = [];

        $needSkillTags = $need->skill_tags_array;
        $needSkills = array_filter(array_map('trim', explode(',', $need->required_skills ?? '')));
        $allNeedSkills = array_unique(array_merge($needSkillTags, $needSkills));

        $talentSkillTags = $talent->skill_tags_array;
        $talentSkills = $talent->skills_array;
        $allTalentSkills = array_unique(array_merge($talentSkillTags, $talentSkills));

        $roleScore = 0;
        $talentRole = strtolower($talent->target_role ?? '');
        $needRole = strtolower($need->job_title ?? '');
        similar_text($talentRole, $needRole, $rolePct);
        if ($rolePct >= 70) {
            $roleScore = 40;
        } elseif ($rolePct >= 40) {
            $roleScore = 20;
        } else {
            $wordsTalent = explode(' ', $talentRole);
            $wordsNeed = explode(' ', $needRole);
            $intersect = array_intersect($wordsTalent, $wordsNeed);
            if (!empty($intersect)) {
                $roleScore = 10;
            }
        }
        $details['role_match_pct'] = round($rolePct, 1);
        $details['role_score'] = $roleScore;
        $score += $roleScore;

        $skillScore = 0;
        $matchedSkills = [];
        $lowerTalent = array_map('strtolower', $allTalentSkills);
        foreach ($allNeedSkills as $needSkill) {
            $needLower = strtolower(trim($needSkill));
            if ($needLower === '') continue;
            foreach ($lowerTalent as $tSkill) {
                if ($tSkill === '' || $needLower === '') continue;
                if ($needLower === $tSkill || str_contains($tSkill, $needLower) || str_contains($needLower, $tSkill)) {
                    $matchedSkills[] = $needSkill;
                    break;
                }
            }
        }
        $matchedSkills = array_unique($matchedSkills);
        $totalNeed = max(1, count($allNeedSkills));
        $skillPct = (count($matchedSkills) / $totalNeed) * 100;
        $skillScore = min(40, (count($matchedSkills) / $totalNeed) * 40);
        $details['skill_match_pct'] = round($skillPct, 1);
        $details['matched_skills'] = $matchedSkills;
        $details['skill_score'] = round($skillScore, 1);
        $score += $skillScore;

        $expScore = 0;
        $yearsNeed = 0;
        if (preg_match('/(\d+)/', $need->description ?? '', $m)) {
            $yearsNeed = (int)$m[1];
        }
        if ($talent->years_experience >= $yearsNeed) {
            $expScore = 10;
        } elseif ($talent->years_experience >= ($yearsNeed * 0.7)) {
            $expScore = 7;
        } elseif ($talent->years_experience >= 1) {
            $expScore = 3;
        }
        $details['exp_score'] = $expScore;
        $score += $expScore;

        $availScore = 0;
        switch ($talent->availability_status) {
            case 'available':
                $availScore = 10;
                break;
            case 'notice_period':
                $availScore = 6;
                break;
            case 'employed':
                $availScore = 2;
                break;
        }
        $details['availability_score'] = $availScore;
        $score += $availScore;

        $finalScore = min(100, round($score, 1));
        $details['total_score'] = $finalScore;

        return ['score' => $finalScore, 'details' => $details];
    }

    public function runMatchEngine(HrClientNeed $need, int $limit = 10): array
    {
        $statuses = match ($need->status) {
            default => ['ready_to_hire', 'in_process', 'passive'],
        };

        $talents = HrTalent::whereIn('status', $statuses)->get();
        $results = [];

        foreach ($talents as $talent) {
            $calc = $this->calculateMatchScore($talent, $need);
            $results[] = [
                'talent' => $talent,
                'score' => $calc['score'],
                'details' => $calc['details'],
            ];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        $results = array_slice($results, 0, $limit);

        DB::beginTransaction();
        try {
            HrTalentMatch::where('client_need_id', $need->id)->delete();
            foreach ($results as $r) {
                HrTalentMatch::create([
                    'talent_id' => $r['talent']->id,
                    'client_need_id' => $need->id,
                    'company_id' => $need->company_id,
                    'match_score' => $r['score'],
                    'match_details' => $r['details'],
                    'status' => 'suggested',
                    'matched_by' => auth()->id(),
                    'matched_at' => now(),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    public function getExistingMatches(HrClientNeed $need)
    {
        return HrTalentMatch::where('client_need_id', $need->id)
            ->with(['talent', 'company'])
            ->orderByDesc('match_score')
            ->get();
    }
}
