<?php

namespace App\Livewire;

use App\Traits\SendsNotifications;
use Livewire\Component;

class SchoolWeekManager extends Component
{
    use SendsNotifications;

    public $weeks;
    public $year_start;
    public $year_end;
    public $week_number;
    public $date_of_monday;

    public function render()
    {
        return view('livewire.school-week-manager');
    }

    public function mount()
    {
        $this->resetFields(true);
    }

    public function resetFields(bool $silent = false)
    {
        $this->year_start = now()->year;
        $this->year_end = now()->year + 1;
        $this->week_number = 1;
        $this->date_of_monday = now()->startOfWeek()->toDateString();
        $this->weeks = self::getWeeks();

        if (!$silent) {
            $this->sendNotification(__('Reset'));
        }
    }

    public function addWeek()
    {
        $this->validate([
            'year_start' => 'required|integer',
            'year_end' => 'required|integer',
            'week_number' => 'required|integer',
            'date_of_monday' => 'required|date',
        ]);

        $existingWeek = \App\Models\SchoolWeek::where('year_start', $this->year_start)
            ->where('year_end', $this->year_end)
            ->where('week_number', $this->week_number)
            ->first();

        if ($existingWeek) {
            $this->addError('week_number', __('This week already exists.'));
            return;
        }

        \App\Models\SchoolWeek::create([
            'year_start' => $this->year_start,
            'year_end' => $this->year_end,
            'week_number' => $this->week_number,
            'date_of_monday' => $this->date_of_monday,
        ]);

        $this->resetFields(true);
        $this->sendNotification(__('Created'), 'success');
    }

    public function deleteWeek($id)
    {
        \App\Models\SchoolWeek::find($id)->delete();
        $this->resetFields(true);
        $this->sendNotification(__('Deleted'), 'warning');
    }

    public function updateWeek()
    {
        // TODO: Send toast
    }

    public function updatedWeeks($value, $key)
    {
        $parts = explode('.', $key);
        $weekKey = $parts[0];
        $property = $parts[count($parts) - 1];
        $week = $this->weeks[$weekKey];

        $this->validateOnly($key, [
            $property => 'required',
        ]);

        \App\Models\SchoolWeek::find($week['id'])->update([
            $property => $value,
        ]);
        $this->sendNotification(__('Updated'));
    }

    public static function getWeeks(){
        return \App\Models\SchoolWeek::all()
            ->map(function($week) {
                $arr = $week->toArray();
                $arr['date_of_monday'] = $week->date_of_monday->toDateString();
                return $arr;
            })
            ->sortBy('week_number')
            ->values();
    }
}
