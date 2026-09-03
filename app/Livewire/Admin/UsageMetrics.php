<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class UsageMetrics extends Component
{
    public function render()
    {
        return view('livewire.admin.usage-metrics')
            ->layout('layouts.admin', ['title' => 'Usage Metrics']);
    }
}
