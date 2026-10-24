<?php

namespace Tests\Feature\Security;

use App\Http\Middleware\IsTeacher;
use App\Livewire\GroupManager;
use App\Livewire\StudyPointMatrix;
use App\Models\Group;
use App\Models\StudentScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\FakesExternalApis;
use Tests\TestCase;

class TeacherAuthorizationTest extends TestCase
{
    use FakesExternalApis;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFakeApis();
    }

    public static function teacherOnlyGetRoutes(): array
    {
        return [
            'home' => ['/'],
            'group matrix' => ['/groups/10'],
            'group manager' => ['/groups'],
            'other student' => ['/student/student-2'],
            'orphaned scores' => ['/orphaned-scores'],
            'orphaned scores data' => ['/orphaned-scores/data'],
        ];
    }

    #[DataProvider('teacherOnlyGetRoutes')]
    public function test_students_cannot_open_teacher_pages(string $uri): void
    {
        $student = $this->makeUser('student-1', 'student');

        $this->actingAs($student)
            ->get($uri)
            ->assertRedirect(route('student.home'));
    }

    public function test_students_cannot_bulk_delete_scores_or_groups(): void
    {
        $student = $this->makeUser('student-1', 'student');
        Group::create(['group_id' => 99, 'cohort_id' => 1]);
        DB::table('student_scores_a')->insert([
            'student_id' => 'student-2', 'feedbackmoment_id' => 101, 'score' => 50, 'teacher_id' => 'teacher-1',
        ]);

        $this->actingAs($student)
            ->post('/orphaned-scores/bulk-destroy', ['student_ids' => ['student-2']])
            ->assertRedirect(route('student.home'));
        $this->actingAs($student)
            ->post('/orphaned-groups/bulk-destroy', ['group_ids' => [99]])
            ->assertRedirect(route('student.home'));

        $this->assertDatabaseCount('student_scores_a', 1);
        $this->assertDatabaseCount('groups', 1);
    }

    public function test_teacher_middleware_is_applied_to_livewire_update_requests(): void
    {
        $this->assertContains(IsTeacher::class, Livewire::getPersistentMiddleware());
    }

    public function test_students_cannot_use_the_study_point_matrix_component(): void
    {
        $this->setUpGroupWithBlok();
        $student = $this->makeUser('student-1', 'student');

        Livewire::actingAs($student)
            ->withQueryParams(['group' => 10])
            ->test(StudyPointMatrix::class)
            ->assertForbidden();
    }

    public function test_students_cannot_use_the_group_manager_component(): void
    {
        $this->fakeCurrApp('cohorts', []);
        $student = $this->makeUser('student-1', 'student');

        Livewire::actingAs($student)
            ->test(GroupManager::class)
            ->assertForbidden();
    }

    public function test_a_student_cannot_replay_a_teacher_snapshot_to_change_scores(): void
    {
        $this->setUpGroupWithBlok();
        $teacher = $this->makeUser('teacher-1', 'teacher');
        $student = $this->makeUser('student-1', 'student');

        // Livewire snapshots are not bound to a session, so one taken from a teacher's page is valid for anyone.
        $html = $this->actingAs($teacher)->get('/?group=10')->assertOk()->getContent();
        preg_match('/wire:snapshot="([^"]+)"/', $html, $matches);
        $snapshot = html_entity_decode($matches[1], ENT_QUOTES);

        $this->sendLivewireUpdate($snapshot, ['students.0.feedbackmomenten.101' => '1'])
            ->assertStatus(200);
        $this->assertSame(10, StudentScore::firstWhere('student_id', 'student-1')->score);

        $this->actingAs($student);
        $response = $this->sendLivewireUpdate($snapshot, ['students.0.feedbackmomenten.101' => '10']);

        $this->assertContains($response->getStatusCode(), [302, 403]);
        $this->assertSame(10, StudentScore::firstWhere('student_id', 'student-1')->score);
    }

    private function sendLivewireUpdate(string $snapshot, array $updates)
    {
        return $this->withHeader('X-Livewire', 'true')
            ->postJson(app('livewire')->getUpdateUri(), [
                'components' => [
                    ['snapshot' => $snapshot, 'updates' => $updates, 'calls' => []],
                ],
            ]);
    }
}
