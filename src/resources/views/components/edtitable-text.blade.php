@props(['key', 'text' => '', 'mode' => 'simple'])

@php
    $isAdmin = auth()?->user()?->isAdmin();
    $dbtext = DB::table('texts')->where('key', $key)->first();
    $text = $dbtext->value ?? $text;
@endphp

<span x-data="{
    text: `{{ $text ?? $slot }}`,
    handleEvent(e) {
        console.log(e.detail)
        const { text, key } = e.detail;
        if (key === `{{ $key }}`.trim()) {
            this.text = text;
        }
    },
    openEditor() {
        $dispatch('open-text-editor', {
            key: `{{ $key }}`,
            text: `{{ $text }}`,
            mode: `{{ $mode }}`
        });
    }
}" x-on:text-updated.window="handleEvent">
    <span x-html="text"> {!! $text ?? $slot !!}</span>
    @if ($isAdmin)
        <button class="rounded-full border-2 border-white bg-black p-1 text-white focus:ring-2" type="button"
            @click='openEditor'>
            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M15.7279 9.57627L14.3137 8.16206L5 17.4758V18.89H6.41421L15.7279 9.57627ZM17.1421 8.16206L18.5563 6.74785L17.1421 5.33363L15.7279 6.74785L17.1421 8.16206ZM7.24264 20.89H3V16.6473L16.435 3.21231C16.8256 2.82179 17.4587 2.82179 17.8492 3.21231L20.6777 6.04074C21.0682 6.43126 21.0682 7.06443 20.6777 7.45495L7.24264 20.89Z">
                </path>
            </svg>
        </button>
    @endif
</span>
