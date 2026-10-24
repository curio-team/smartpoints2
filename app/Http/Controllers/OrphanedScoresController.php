<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Curio\SdClient\Facades\SdApi;

class OrphanedScoresController extends Controller
{
    public function index()
    {
        return view('orphaned-scores');
    }

    public function data()
    {
        [$orphanedGroups, $orphanedInA, $orphanedInB] = $this->findOrphans();

        $allOrphanedIds = $orphanedInA->keys()->merge($orphanedInB->keys())->unique();

        $users = User::whereIn('id', $allOrphanedIds)->get()->keyBy('id');

        $orphans = $allOrphanedIds->map(function ($studentId) use ($orphanedInA, $orphanedInB, $users) {
            return [
                'student_id' => $studentId,
                'name'       => $users->has($studentId) ? $users[$studentId]->name : '(onbekend)',
                'count_a'    => $orphanedInA->has($studentId) ? $orphanedInA[$studentId]->count_a : 0,
                'count_b'    => $orphanedInB->has($studentId) ? $orphanedInB[$studentId]->count_b : 0,
            ];
        })->sortBy('name')->values();

        return response()->json([
            'orphanedGroups' => $orphanedGroups->map(fn($g) => ['group_id' => $g->group_id, 'cohort_id' => $g->cohort_id]),
            'orphans'        => $orphans,
        ]);
    }

    public function bulkDestroyStudents(Request $request)
    {
        $request->validate([
            'student_ids'   => ['array'],
            'student_ids.*' => ['string'],
        ]);

        $ids = $request->input('student_ids', []);
        if (empty($ids)) {
            return redirect()->route('orphaned-scores.index');
        }

        // Only delete scores of students that are actually orphaned, never those of active students
        [, $orphanedInA, $orphanedInB] = $this->findOrphans();
        $ids = collect($ids)
            ->intersect($orphanedInA->keys()->merge($orphanedInB->keys()))
            ->unique()
            ->values();

        DB::table('student_scores_a')->whereIn('student_id', $ids)->delete();
        DB::table('student_scores_b')->whereIn('student_id', $ids)->delete();

        return redirect()->route('orphaned-scores.index')
            ->with('success', count($ids) . ' student(en) verwijderd.');
    }

    public function bulkDestroyGroups(Request $request)
    {
        $request->validate([
            'group_ids'   => ['array'],
            'group_ids.*' => ['integer'],
        ]);

        $ids = $request->input('group_ids', []);
        if (empty($ids)) {
            return redirect()->route('orphaned-scores.index');
        }

        // Only delete groups that no longer exist in the API
        [$orphanedGroups] = $this->findOrphans();
        $ids = collect($ids)
            ->map(fn($id) => (int) $id)
            ->intersect($orphanedGroups->pluck('group_id'))
            ->unique()
            ->values();

        Group::whereIn('group_id', $ids)->delete();

        return redirect()->route('orphaned-scores.index')
            ->with('success', count($ids) . ' groep(en) verwijderd.');
    }

    /**
     * Find the local groups that no longer exist in the API and the scores (per table, keyed by
     * student id) of students that are not in any active group.
     */
    private function findOrphans()
    {
        // One API call to get all group IDs — no per-group 404 calls needed
        $apiGroupIds = collect(SdApi::get('/groups'))->pluck('id');

        $localGroups = Group::all();

        // Groups in local DB whose API ID no longer exists
        $orphanedGroups = $localGroups
            ->filter(fn($group) => !$apiGroupIds->contains($group->group_id))
            ->values();

        // Active groups: only fetch user lists for groups that actually exist
        $activeStudentIds = collect();
        foreach ($localGroups->filter(fn($g) => $apiGroupIds->contains($g->group_id)) as $group) {
            $groupFromApi = SdApi::get('/groups/' . $group->group_id);
            if ($groupFromApi && isset($groupFromApi['users'])) {
                $activeStudentIds = $activeStudentIds->merge(
                    collect($groupFromApi['users'])->pluck('id')
                );
            }
        }
        $activeStudentIds = $activeStudentIds->unique();

        // Find orphaned student IDs: scores exist but student is not in any active group
        $orphanedInA = DB::table('student_scores_a')
            ->select('student_id', DB::raw('count(*) as count_a'))
            ->groupBy('student_id')
            ->get()
            ->filter(fn($row) => !$activeStudentIds->contains($row->student_id))
            ->keyBy('student_id');

        $orphanedInB = DB::table('student_scores_b')
            ->select('student_id', DB::raw('count(*) as count_b'))
            ->groupBy('student_id')
            ->get()
            ->filter(fn($row) => !$activeStudentIds->contains($row->student_id))
            ->keyBy('student_id');

        return [$orphanedGroups, $orphanedInA, $orphanedInB];
    }
}
