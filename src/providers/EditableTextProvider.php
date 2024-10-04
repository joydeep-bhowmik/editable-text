<?php

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class EditableTextProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'joydeep-bhowmik/editable-text');

        $this->publishes([
            __DIR__ . '/../migrations/create_texts_table.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_texts_table.php'),
        ], 'editable-text-migrations');

        Livewire::component('text-editor', \App\Http\Livewire\TextEditor::class);
    }
}
