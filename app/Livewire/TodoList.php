<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Rule('required|min:3|max:50')]
    public string $name;

    public $search;

    public $editingTodoID;

    #[Rule('required|min:3|max:50')]
    public $editingTodoName;

    public function create() {
        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Created.');
        $this->alert('success', 'Created.');

        $this->resetPage();
    }

    public function delete($todoID) {
        Todo::find($todoID)->delete();
        session()->flash('success', 'Deleted.');
        $this->alert('success', 'Deleted.');
    }

    public function toggle($todoID) {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID) {
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    public function cancelEdit() {
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update() {
        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoID)->update(
            [
                'name' => $this->editingTodoName
            ]
        );

        $this->cancelEdit();
        session()->flash('success', 'Updated.');
        $this->alert('success', 'Updated.');
    }

    public function render()
    {
       return view('livewire.todo-list', [
        'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
       ]);
    }
}
