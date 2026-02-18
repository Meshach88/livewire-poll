<?php

use App\Models\Option;
use App\Models\Poll;
use Livewire\Component;

new class extends Component
{
    public $polls;
    protected $listeners = [
        'pollCreated' => 'mount',
        'voteAdded' => 'mount'
    ];
    public function mount()
    {
        $this->polls = Poll::with('options.votes')->latest()->get();
    }
    public function vote(Option $option)
    {
        // $option->votes()->create();

        $this->dispatch('vote', ['option' => $option]);

    }
};
?>

<div>
    @forelse($polls as $poll)
    <h2 class="text-2xl">{{$poll->title}}</h2>
    @foreach($poll->options as $option)
    <div class="m-3">
        <!-- <button class="btn mb-2" wire:click="vote({{ $option->id }})">Vote</button> -->
        <button class="btn mb-2" wire:click="vote({{ $option->id }})">Vote</button>
        {{ $option->name }} {{ $option->votes->count() }}
    </div>
    @endforeach

    @empty
    <div>
        <h2 class="text-muted text-2xl">No polls available</h2>
    </div>
    @endforelse
</div>