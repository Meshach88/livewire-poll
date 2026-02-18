<?php

use App\Models\Option;
use App\Models\Poll;
use Livewire\Component;

new class extends Component {
    public string $title = '';

    public string $content = '';

    public $options = [''];

    protected $listeners = [
        "vote" => 'vote'
    ];

    protected $rules = [
        'title' => 'required',
        'content' => 'required|max:255',
        'options' => 'required|array|min:1|max:10',
        'options.*' => 'required|string|max:255'
    ];

    protected $messages = [
        'options.*' => 'This option is required'
    ];

    public function addOptions()
    {
        $this->options[] = '';
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function vote(Option $option)
    {
        $option->votes()->create();
        $this->dispatch('voteAdded');
    }

    public function save()
    {
        // $this->validate([
        //     'title' => 'required|max:255',
        //     'content' => 'required',
        // ]);
        $this->validate();
        $poll = Poll::create([
            'title' => $this->title,
        ])->options()->createMany(
            collect($this->options)
                ->map(fn($option) => ['name' => $option])
                ->all()
        );

        // foreach ($this->options as $optionName) {
        //     $poll->options()->create([
        //         "name" => $optionName
        //     ]);
        // }
        $this->reset(['title', 'options']);
        $this->dispatch('pollCreated');
    }
};
?>

<form wire:submit.prevent="save">
    <label>
        Title
        <input type="text" wire:model.live.debounce.500ms="title">
        @error('title') <span style="color: red;">{{ $message }}</span> @enderror
    </label>

    <label>
        Content
        <textarea wire:model="content" rows="5"></textarea>
        @error('content') <span style="color: red;">{{ $message }}</span> @enderror
    </label>

    <button class="mb-5 btn" wire:click.prevent="addOptions">Add Options</button>

    @foreach($options as $index=>$option)
    <div class="mb-10">
        <label for="">Option - {{ $index + 1 }}</label>
        <div class="flex gap-4">
            <input type="text" wire:model.live="options.{{ $index }}">
            <button class="btn" wire:click.prevent="removeOption({{ $index }})">Remove</button>
        </div>
        @error("options.{$index}") <span style="color: red;">{{ $message }}</span> @enderror
    </div>
    @endforeach

    <button type="submit">Save</button>
</form>