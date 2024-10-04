<?php

namespace YourVendorName\YourPackageName\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TextEditor extends Component
{
    public string $key_ = '';
    public string $mode = 'plain'; // default mode
    public string $text = '';

    public function mount(string $key, string $mode, string $text)
    {
        $this->key_ = $key;
        $this->mode = $mode;

        // Load existing text from the database
        $dbtext = DB::table('texts')->where('key', $this->key_)->first();
        $this->text = $dbtext->value ?? $text;
    }

    public function save()
    {
        DB::table('texts')->updateOrInsert(['key' => $this->key_], ['value' => $this->text]);

        // Dispatch event to notify that the text has been updated
        $this->dispatch('text-updated', ['key' => $this->key_, 'text' => $this->text]);
        $this->reset(); // Reset the form fields
    }

    public function render()
    {
        return view('joydeep-bhowmik/editable-text::livewire.text-editor');
    }
}
