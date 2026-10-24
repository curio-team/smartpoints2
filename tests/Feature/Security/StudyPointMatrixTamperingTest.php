<?php

namespace Tests\Feature\Security;

use App\Livewire\StudyPointMatrix;
use App\Models\StudentScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\FakesExternalApis;
use Tests\TestCase;

class StudyPointMatrixTamperingTest extends TestCase
{
    use FakesExternalApis;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFakeApis();
        $this->setUpGroupWithBlok();
    }

    private function matrix()
    {
        return Livewire::actingAs($this->makeUser('teacher-1', 'teacher'))
            ->withQueryParams(['group' => 10])
            ->test(StudyPointMatrix::class);
    }

    public function test_teacher_can_enter_and_clear_a_score(): void
    {
        $matrix = $this->matrix()
            ->set('students.0.feedbackmomenten.101', '7.5')
            ->assertHasNoErrors();

        $this->assertSame(75, StudentScore::firstWhere('student_id', 'student-1')->score);

        $matrix->set('students.0.feedbackmomenten.101', '');

        $this->assertDatabaseCount('student_scores_a', 0);
    }

    public static function invalidGrades(): array
    {
        return [
            'too high' => ['11'],
            'negative' => ['-1'],
            'not a number' => ['abc'],
        ];
    }

    #[DataProvider('invalidGrades')]
    public function test_grades_outside_the_valid_range_are_rejected(string $grade): void
    {
        $this->matrix()
            ->set('students.0.feedbackmomenten.101', $grade)
            ->assertHasErrors('students.0.feedbackmomenten.101');

        $this->assertDatabaseCount('student_scores_a', 0);
    }

    public function test_student_ids_cannot_be_changed_by_the_client(): void
    {
        $this->matrix()
            ->set('students.0.id', 'student-from-another-class')
            ->assertForbidden();
    }

    public function test_the_student_list_cannot_be_replaced_by_the_client(): void
    {
        $this->matrix()
            ->set('students', [['id' => 'student-from-another-class', 'name' => 'X', 'feedbackmomenten' => [101 => 1]]])
            ->assertForbidden();

        $this->assertDatabaseCount('student_scores_a', 0);
    }

    public function test_scores_for_feedbackmomenten_outside_the_blok_are_rejected(): void
    {
        $this->matrix()
            ->set('students.0.feedbackmomenten.999', '5')
            ->assertForbidden();

        $this->assertDatabaseCount('student_scores_a', 0);
    }

    public static function lockedProperties(): array
    {
        return [
            'blok' => ['blok', ['id' => 666]],
            'groups' => ['groups', []],
            'blokken' => ['blokken', []],
            'fbmsActive' => ['fbmsActive', []],
            'flood fill value' => ['floodFillValue', 1000],
            'flood fill subject' => ['floodFillSubject', ['id' => 101]],
        ];
    }

    #[DataProvider('lockedProperties')]
    public function test_server_state_cannot_be_changed_by_the_client(string $property, mixed $value): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        $this->matrix()->set($property, $value);
    }

    public function test_flood_fill_without_a_subject_does_nothing(): void
    {
        $this->matrix()
            ->call('doFloodFill')
            ->call('startFloodFill', 999)
            ->assertSet('floodFillSubject', null);

        $this->assertDatabaseCount('student_scores_a', 0);
    }

    public function test_blok_id_cannot_be_used_to_request_other_currapp_paths(): void
    {
        // Requests to CurrApp use our own API token, so the client must not be able to choose the path.
        $this->fakeCurrApp('secret', ['id' => 666, 'blok' => 'Secret', 'datum_eind' => '2999-01-01', 'currentWeek' => 1, 'vakken' => []]);

        $this->matrix()
            ->set('selectedBlokId', '../../../secret')
            ->assertNotFound();
    }

    public function test_unknown_groups_are_not_found(): void
    {
        Livewire::actingAs($this->makeUser('teacher-1', 'teacher'))
            ->withQueryParams(['group' => 12345])
            ->test(StudyPointMatrix::class)
            ->assertNotFound();
    }

    public function test_api_data_is_escaped_inside_alpine_expressions(): void
    {
        $this->fakeCurrApp('cohorts/1/uitvoer/-1', [
            'id' => 5,
            'blok' => 'Blok A',
            'datum_eind' => '2999-01-01',
            'currentWeek' => 3,
            'vakken' => [[
                'vak' => 'PRA',
                'uitvoer_id' => 7,
                'feedbackmomenten' => [
                    ['id' => 101, 'code' => "'-alert(1)-'", 'naam' => 'X', 'week' => '1||alert(2)', 'points' => 4],
                ],
            ]],
        ]);

        $this->matrix()
            ->assertSeeHtml("hoverColumn = '\\u0027-alert(1)-\\u0027'")
            ->assertSeeHtml("'1||alert(2)' >")
            ->assertDontSeeHtml("hoverColumn = '&#039;-alert(1)-&#039;'")
            ->assertDontSeeHtml("'!text-gray-400': 1||alert(2)");
    }
}
