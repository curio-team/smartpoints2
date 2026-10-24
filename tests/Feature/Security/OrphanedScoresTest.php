<?php

namespace Tests\Feature\Security;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\FakesExternalApis;
use Tests\TestCase;

class OrphanedScoresTest extends TestCase
{
    use FakesExternalApis;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFakeApis();

        // Group 10 still exists in the API (with student-1), group 99 does not anymore.
        Group::create(['group_id' => 10, 'cohort_id' => 1]);
        Group::create(['group_id' => 99, 'cohort_id' => 1]);
        $this->fakeSdApi('/groups', [['id' => 10, 'name' => 'Klas 1A', 'type' => 'class']]);
        $this->fakeSdApi('/groups/10', ['id' => 10, 'users' => [['id' => 'student-1', 'name' => 'Anna']]]);

        foreach (['student-1', 'orphan-1'] as $studentId) {
            DB::table('student_scores_a')->insert([
                'student_id' => $studentId, 'feedbackmoment_id' => 101, 'score' => 50, 'teacher_id' => 'teacher-1',
            ]);
            DB::table('student_scores_b')->insert([
                'student_id' => $studentId, 'subject_id' => '7', 'score' => 50, 'teacher_id' => 'teacher-1',
            ]);
        }

        $this->actingAs($this->makeUser('teacher-1', 'teacher'));
    }

    public function test_data_lists_only_orphans(): void
    {
        $this->getJson('/orphaned-scores/data')
            ->assertOk()
            ->assertJsonPath('orphanedGroups', [['group_id' => 99, 'cohort_id' => 1]])
            ->assertJsonPath('orphans.0.student_id', 'orphan-1')
            ->assertJsonCount(1, 'orphans');
    }

    public function test_bulk_destroy_only_deletes_scores_of_orphaned_students(): void
    {
        $this->post('/orphaned-scores/bulk-destroy', ['student_ids' => ['orphan-1', 'student-1']])
            ->assertRedirect(route('orphaned-scores.index'))
            ->assertSessionHas('success', '1 student(en) verwijderd.');

        $this->assertDatabaseMissing('student_scores_a', ['student_id' => 'orphan-1']);
        $this->assertDatabaseMissing('student_scores_b', ['student_id' => 'orphan-1']);
        $this->assertDatabaseHas('student_scores_a', ['student_id' => 'student-1']);
        $this->assertDatabaseHas('student_scores_b', ['student_id' => 'student-1']);
    }

    public function test_bulk_destroy_only_deletes_groups_that_no_longer_exist(): void
    {
        $this->post('/orphaned-groups/bulk-destroy', ['group_ids' => [99, 10]])
            ->assertRedirect(route('orphaned-scores.index'))
            ->assertSessionHas('success', '1 groep(en) verwijderd.');

        $this->assertDatabaseMissing('groups', ['group_id' => 99]);
        $this->assertDatabaseHas('groups', ['group_id' => 10]);
    }

    public function test_bulk_destroy_rejects_malformed_input(): void
    {
        $this->post('/orphaned-scores/bulk-destroy', ['student_ids' => 'orphan-1'])
            ->assertSessionHasErrors('student_ids');
        $this->post('/orphaned-groups/bulk-destroy', ['group_ids' => ['abc']])
            ->assertSessionHasErrors('group_ids.0');

        $this->assertDatabaseCount('student_scores_a', 2);
        $this->assertDatabaseCount('groups', 2);
    }
}
