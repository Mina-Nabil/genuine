<?php

namespace App\Livewire\Customers;

use App\Models\Payments\Title;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class TitlesIndex extends Component
{
    use AlertFrontEnd, WithPagination;

    public $page_title = '• Titles';

    //////edit title functions
    public $editTitleModal = false;
    public $editTitleId;
    public $editTitleName;
    public $editTitleLimit;
    public $editTitleDescription;

    public function openEditTitleModal($id)
    {
        $this->editTitleId = $id;
        $title = Title::findOrFail($id);
        $this->editTitleName = $title->title;
        $this->editTitleLimit = $title->limit;
        $this->editTitleDescription = $title->description;
        $this->editTitleModal = true;
    }

    public function closeEditTitleModal()
    {
        $this->editTitleModal = false;
        $this->reset(['editTitleId', 'editTitleName', 'editTitleLimit', 'editTitleDescription']);
    }

    public function updateTitle()
    {
        $this->validate([
            'editTitleName' => 'required|string|max:255',
            'editTitleLimit' => 'required|numeric',
            'editTitleDescription' => 'nullable|string|max:1000',
        ]);
        /** @var Title */
        $title = Title::findOrFail($this->editTitleId);
        $title->editTitle($this->editTitleName, $this->editTitleLimit, $this->editTitleDescription);

        $this->closeEditTitleModal();
        $this->alert('success', 'Title updated successfully.');
    }

    /////add title functions
    public $addTitleModal = false;
    public $newTitleName;
    public $newTitleLimit;
    public $newTitleDescription;

    public function openAddTitleModal()
    {
        $this->addTitleModal = true;
    }

    public function closeAddTitleModal()
    {
        $this->addTitleModal = false;
        $this->reset(['newTitleName', 'newTitleLimit', 'newTitleDescription']);
    }

    public function saveTitle()
    {
        $this->validate([
            'newTitleName' => 'required|string|max:255',
            'newTitleLimit' => 'required|numeric',
            'newTitleDescription' => 'nullable|string|max:1000',
        ]);

        Title::newTitle($this->newTitleName, $this->newTitleLimit, $this->newTitleDescription);

        $this->closeAddTitleModal();
        $this->alert('success', 'Title added successfully.');
    }

    
    public function render()
    {
        $titles = Title::orderBy('title')->paginate(10);
        return view('livewire.customers.titles-index', [
            'titles' => $titles
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'titles' => 'active']);
    }
}
