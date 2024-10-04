<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use function Livewire\Volt\{state, mount, on};

state(['text', 'key_', 'mode']);

$loadEditor = function (string $key, string $mode, string $text) {
    $this->key_ = $key;
    $this->mode = $mode;

    $dbtext = DB::table('texts')
        ->where('key', $this->key_)
        ->first();

    $this->text = $dbtext->value ?? $text;
};

$save = function () {
    $affected = DB::table('texts')->updateOrInsert(['key' => $this->key_], ['value' => $this->text]);

    $updatedText = DB::table('texts')
        ->where('key', $this->key_)
        ->first();

    $this->dispatch('close-text-modal');
    $this->dispatch('text-updated', key: $this->key_, text: $updatedText->value);
    $this->reset();
};
?>
<div class="fixed inset-0 z-50 grid place-items-center text-sm font-normal normal-case backdrop-blur-3xl"
    style="display: none" x-data="{
        show: false,
        handleEvent(e) {
    
            const { key, mode, text } = e.detail;
    
            this.show = true;
    
            $wire.loadEditor(key, mode, text);
        }
    }" x-on:open-text-editor.window="handleEvent"
    x-on:close-text-modal.window="show=false" x-show="show">

    <div class="block max-h-screen w-full max-w-md overflow-y-auto rounded-md border bg-white text-black shadow-md"
        x-show="show" x-transition @click.away="show = false">

        <div class="w-full" wire:loading wire:target='loadEditor'>
            <div class="grid place-items-center p-5">
                <svg class="h-8 w-8 animate-spin fill-blue-600 text-gray-200 dark:text-gray-600" aria-hidden="true"
                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill" />
                </svg>
            </div>
        </div>
        <div class="space-y-5" wire:loading.remove wire:target='loadEditor'>
            <div class="sticky top-0 z-10 flex flex-wrap items-center gap-2 bg-white p-5">
                <h2 class="text-xl font-bold">Edit text
                </h2>
                <span class="ml-auto text-xs opacity-50">{{ $key_ }}</span>
            </div>
            <div class="px-5">

                @if (trim($mode) === 'rich')
                    <div class="mt-2" wire:ignore>
                        <div x-data="{
                            init() {
                                const toolbarOptions = [
                                    ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                                    ['blockquote', 'code-block'],
                                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        
                                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        
                                    [{ 'color': [] }, { 'background': [] }], // dropdown with defaults from theme
                                    [{ 'font': [] }],
                                    [{ 'align': [] }],
                        
                                    ['clean'], // remove formatting button
                                    ['image']
                                ];
                                quill = new Quill(this.$refs.quillEditor, {
                                    modules: {
                                        toolbar: toolbarOptions
                                    },
                                    theme: 'snow'
                                });
                                quill.on('text-change', function() {
                                    $dispatch('input', quill.root.innerHTML);
                                });
                            }
                        }" x-ref="quillEditor" wire:model='text'>
                            {!! $text !!}
                        </div>
                    </div>
                @else
                    <textarea class="w-full rounded border-slate-300" wire:model='text'></textarea>
                @endif

            </div>
            <div class="flex w-fit items-center gap-4 p-5">
                <button class="inline-flex items-center gap-3 rounded-md bg-blue-800 px-5 py-2 font-bold text-white"
                    type="button" wire:click='save'>
                    <span wire:loading.remove>Save</span>
                    <span wire:loading>

                        <svg class="mr-2 inline h-4 w-4 animate-spin fill-blue-600 text-gray-200 dark:text-gray-600"
                            role="status" v-if="processing" viewBox="0 0 100 101" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>

                        Processing...
                    </span>
                </button>
                <button type="button" @click='show=false'>
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
