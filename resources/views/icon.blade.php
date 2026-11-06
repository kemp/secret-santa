{{--
    Inline line icons, all drawn on a 24px grid with the same 1.75 stroke.
    Usage: @include('icon', ['name' => 'gift', 'class' => 'size-5'])
--}}
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
     stroke-linecap="round" stroke-linejoin="round" class="{{ $class ?? 'size-5' }} shrink-0" aria-hidden="true" focusable="false">
    @switch($name)
        @case('gift')
            <rect x="3" y="8" width="18" height="4" rx="1"/>
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7M12 8v13"/>
            <path d="M12 8S10.5 3.5 8 3.5a2.25 2.25 0 0 0 0 4.5M12 8s1.5-4.5 4-4.5a2.25 2.25 0 0 1 0 4.5"/>
            @break
        @case('check')
            <circle cx="12" cy="12" r="9"/>
            <path d="m8.5 12.25 2.5 2.5 4.75-5"/>
            @break
        @case('alert')
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7.75v4.75M12 16.25h.01"/>
            @break
        @case('lock')
            <rect x="5" y="10.5" width="14" height="10" rx="2"/>
            <path d="M8.5 10.5V7a3.5 3.5 0 0 1 7 0v3.5"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3.25 2"/>
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="15.5" rx="2"/>
            <path d="M3.5 10h17M8 3v4M16 3v4"/>
            @break
        @case('star')
            <path d="m12 3.5 2.6 5.3 5.9.85-4.25 4.15 1 5.85L12 16.9l-5.25 2.75 1-5.85L3.5 9.65l5.9-.85z"/>
            @break
        @case('tree')
            <path d="M12 3 7.5 9h2.75L6 14.5h3L5 20h14l-4-5.5h3L13.75 9h2.75z"/>
            <path d="M12 20v1.5"/>
            @break
        @case('swap')
            <path d="M4 12h16M8 8l-4 4 4 4M16 8l4 4-4 4"/>
            @break
        @case('close')
            <path d="M6.5 6.5l11 11M17.5 6.5l-11 11"/>
            @break
        @case('up')
            <path d="m6.5 14.5 5.5-5.5 5.5 5.5"/>
            @break
        @case('down')
            <path d="m6.5 9.5 5.5 5.5 5.5-5.5"/>
            @break
        @case('left')
            <path d="M19 12H5M11 6l-6 6 6 6"/>
            @break
        @case('right')
            <path d="M5 12h14M13 6l6 6-6 6"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('copy')
            <rect x="8.5" y="8.5" width="12" height="12" rx="2"/>
            <path d="M15.5 8.5V5.5a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/>
            @break
        @case('mail')
            <rect x="3" y="5.5" width="18" height="13" rx="2"/>
            <path d="m3.5 7 8.5 6 8.5-6"/>
            @break
    @endswitch
</svg>
