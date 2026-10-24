<?php

namespace Tests\Feature\Security;

use App\Livewire\GroupManager;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\Concerns\FakesExternalApis;
use Tests\TestCase;

class GroupManagerTamperingTest extends TestCase
{
    use FakesExternalApis;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFakeApis();
        $this->fakeSdApi('/groups', [
            ['id' => 10, 'name' => 'Klas 1A', 'type' => 'class'],
            ['id' => 11, 'name' => 'Klas 1B', 'type' => 'class'],
        ]);
        $this->fakeCurrApp('cohorts', [
            ['id' => 1, 'naam' => 'Cohort 2025'],
            ['id' => 2, 'naam' => 'Cohort 2026'],
        ]);
        Group::create(['group_id' => 11, 'cohort_id' => 2]);
    }

    private function manager()
    {
        return Livewire::actingAs($this->makeUser('teacher-1', 'teacher'))
            ->test(GroupManager::class);
    }

    public function test_teacher_can_link_and_unlink_cohorts(): void
    {
        $this->manager()
            ->assertSet('cohortSelections', [10 => -1, 11 => 2])
            ->set('cohortSelections.10', '1')
            ->set('cohortSelections.11', '-1')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('groups', ['group_id' => 10, 'cohort_id' => 1]);
        $this->assertDatabaseMissing('groups', ['group_id' => 11]);
    }

    public function test_unknown_cohorts_are_rejected(): void
    {
        $this->manager()
            ->set('cohortSelections.10', '999')
            ->call('save')
            ->assertHasErrors('cohortSelections.10');

        $this->assertDatabaseMissing('groups', ['group_id' => 10]);
        $this->assertDatabaseHas('groups', ['group_id' => 11, 'cohort_id' => 2]);
    }

    public function test_selections_for_groups_not_listed_are_ignored(): void
    {
        $this->manager()
            ->set('cohortSelections.12345', '1')
            ->call('save');

        $this->assertDatabaseMissing('groups', ['group_id' => 12345]);
    }

    public function test_group_list_cannot_be_changed_by_the_client(): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        $this->manager()->set('groups', [['id' => 12345, 'name' => 'X', 'type' => 'class']]);
    }

    public function test_cohort_list_cannot_be_changed_by_the_client(): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        $this->manager()->set('cohorts', [['id' => 999, 'naam' => 'X']]);
    }
}
