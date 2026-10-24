<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\FakesExternalApis;
use Tests\TestCase;

class StudentPageTest extends TestCase
{
    use FakesExternalApis;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFakeApis();
        $this->setUpGroupWithBlok();
        $this->fakeSdApi('/me', [
            'id' => 'student-1',
            'name' => 'Anna de Vries',
            'groups' => [['id' => 10, 'type' => 'class']],
        ]);
    }

    public function test_student_sees_their_own_grades(): void
    {
        $this->actingAs($this->makeUser('student-1', 'student'))
            ->get('/student')
            ->assertOk()
            ->assertSee('Anna de Vries');
    }

    public function test_specialisatie_filter_cannot_inject_javascript(): void
    {
        $payload = "');alert(document.cookie);('";

        $this->actingAs($this->makeUser('student-1', 'student'))
            ->get('/student?specialisatie='.urlencode($payload))
            ->assertOk()
            ->assertDontSee('alert(document.cookie)', false)
            ->assertSee("\$persist('')", false);
    }

    public function test_valid_specialisatie_filter_is_kept(): void
    {
        $this->actingAs($this->makeUser('student-1', 'student'))
            ->get('/student?specialisatie=web')
            ->assertOk()
            ->assertSee("\$persist('web')", false);
    }

    public function test_alpine_is_not_loaded_from_a_third_party_cdn(): void
    {
        $this->actingAs($this->makeUser('student-1', 'student'))
            ->get('/student')
            ->assertOk()
            ->assertDontSee('cdn.jsdelivr.net', false)
            ->assertSee('livewire.js', false);
    }

    public function test_students_cannot_see_other_students(): void
    {
        $this->actingAs($this->makeUser('student-1', 'student'))
            ->get('/student/student-2')
            ->assertRedirect(route('student.home'));
    }
}
