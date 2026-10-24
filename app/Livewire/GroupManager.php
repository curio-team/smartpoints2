<?php

namespace App\Livewire;

use App\Models\Group;
use Curio\SdClient\Facades\SdApi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class GroupManager extends Component
{
    #[Locked]
    public $groups;

    #[Locked]
    public $cohorts;

    // The selected cohort per group id. This is the only state the client may change.
    public $cohortSelections = [];

    public function boot()
    {
        // Runs on every request, including Livewire updates which skip non-persistent route middleware.
        abort_unless(Auth::user()?->isTeacher(), 403);
    }

    public function mount()
    {
        $this->groups = collect(SdApi::get('groups'))
            ->filter(function ($group) {
                return $group['type'] == 'class';
            })
            ->values();

        $this->cohorts = collect(json_decode(file_get_contents(config('app.currapp.api_url').'/cohorts', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer '.config('app.currapp.api_token'),
            ],
        ]))));

        foreach ($this->groups as $groupFromApi) {
            $groupFromDb = Group::where('group_id', $groupFromApi['id'])->first();

            $this->cohortSelections[$groupFromApi['id']] = -1;

            if ($groupFromDb) {
                $cohortFromApi = $this->cohorts->firstWhere('id', $groupFromDb->cohort_id);

                if ($cohortFromApi) {
                    $this->cohortSelections[$groupFromApi['id']] = $cohortFromApi->id;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.group-manager');
    }

    public function save()
    {
        $this->validate([
            'cohortSelections' => ['array'],
            'cohortSelections.*' => ['integer', Rule::in(collect($this->cohorts)->pluck('id')->push(-1))],
        ]);

        foreach ($this->groups as $group) {
            $cohortId = (int) ($this->cohortSelections[$group['id']] ?? -1);

            if ($cohortId > 0) {
                Group::updateOrCreate(
                    ['group_id' => $group['id']],
                    ['cohort_id' => $cohortId],
                );
            } else {
                Group::where('group_id', $group['id'])->delete();
            }
        }
    }
}
