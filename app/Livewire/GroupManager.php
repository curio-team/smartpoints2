<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;

class GroupManager extends Component
{

    public $groups;
    public $cohorts;

    public function mount()
    {
        $this->groups = collect(AmoAPI::get('groups'))
            ->filter(function($group) {
                return ($group['type'] == "class");
            });

        $this->cohorts = collect(json_decode(file_get_contents(config('app.currapp.api_url') . '/cohorts', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ]))));

        $this->groups = $this->groups->map(function ($groupFromApi){
            // $groupFromApi = (object) $groupFromApi;
            $groupFromDb = Group::where('group_id', $groupFromApi['id'])->first();

            $groupFromApi['cohort'] = -1;
            if($groupFromDb)
            {
                $cohortFromApi = $this->cohorts->firstWhere('id', $groupFromDb->cohort_id);
                if($cohortFromApi) $groupFromApi['cohort'] = $cohortFromApi->id;
            }

            return $groupFromApi;
        })->values();
    }

    public function render()
    {
        return view('livewire.group-manager');
    }

    public function save()
    {
        foreach($this->groups as $group)
        {
            if($group['cohort'] > 0)
            {
                Group::updateOrCreate(
                    ['group_id' => $group['id']],
                    ['cohort_id' => $group['cohort']],
                );
            }
            else
            {
                $group = Group::where('group_id', $group['id']);
                if($group) $group->delete();
            }
        }
    }
}
