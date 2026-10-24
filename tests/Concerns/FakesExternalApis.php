<?php

namespace Tests\Concerns;

use App\Models\Group;
use App\Models\User;
use Curio\SdClient\Facades\SdApi;
use Illuminate\Support\Facades\File;

/**
 * Fakes the SD API (through the SdApi facade) and the CurrApp API (by pointing its url to a
 * directory of fixture files, since it is read with file_get_contents).
 */
trait FakesExternalApis
{
    protected string $currAppPath;

    protected array $sdApiResponses = [];

    protected function setUpFakeApis(): void
    {
        // In debug mode a missing blok is dd()'d, which would kill the test run.
        config(['app.debug' => false]);

        $this->currAppPath = storage_path('framework/testing/currapp-'.uniqid());
        config(['app.currapp.api_url' => $this->currAppPath]);

        SdApi::shouldReceive('get')->andReturnUsing(
            fn ($endpoint) => collect($this->sdApiResponses[ltrim($endpoint, '/')] ?? [])
        );

        $this->beforeApplicationDestroyed(fn () => File::deleteDirectory($this->currAppPath));
    }

    protected function fakeSdApi(string $endpoint, array $response): void
    {
        $this->sdApiResponses[ltrim($endpoint, '/')] = $response;
    }

    protected function fakeCurrApp(string $path, mixed $response): void
    {
        $file = $this->currAppPath.'/'.$path;

        File::ensureDirectoryExists(dirname($file));
        File::put($file, json_encode($response));
    }

    protected function makeUser(string $id, string $type, string $name = 'Test User'): User
    {
        return User::forceCreate([
            'id' => $id,
            'name' => $name,
            'email' => $id.'@example.com',
            'type' => $type,
        ]);
    }

    /**
     * Sets up group 10 (cohort 1) with two students and a current blok with feedbackmomenten 101 and 102.
     */
    protected function setUpGroupWithBlok(array $feedbackmomentOverrides = []): void
    {
        Group::create(['group_id' => 10, 'cohort_id' => 1]);

        $this->fakeSdApi('/groups', [
            ['id' => 10, 'name' => 'Klas 1A', 'type' => 'class', 'date_end' => null],
        ]);
        $this->fakeSdApi('/groups/10', [
            'id' => 10,
            'name' => 'Klas 1A',
            'type' => 'class',
            'users' => [
                ['id' => 'student-1', 'name' => 'Anna de Vries'],
                ['id' => 'student-2', 'name' => 'Bram Jansen'],
            ],
        ]);

        $this->fakeCurrApp('cohorts/1/uitvoeren', [
            ['id' => 5, 'blok' => 'Blok A'],
        ]);
        $this->fakeCurrApp('cohorts/1/uitvoer/-1', [
            'id' => 5,
            'blok' => 'Blok A',
            'datum_eind' => '2999-01-01',
            'currentWeek' => 3,
            'vakken' => [
                [
                    'vak' => 'PRA',
                    'uitvoer_id' => 7,
                    'feedbackmomenten' => [
                        array_merge(['id' => 101, 'code' => 'FOPE', 'naam' => 'Gamecomputer', 'week' => 2, 'points' => 4], $feedbackmomentOverrides),
                        ['id' => 102, 'code' => 'FTDU', 'naam' => 'Web-app', 'week' => 4, 'points' => 6],
                    ],
                ],
            ],
        ]);
    }
}
