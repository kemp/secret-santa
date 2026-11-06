@php
    $palette = ['bg-berry-500', 'bg-pine-600', 'bg-gold-500', 'bg-sky-600', 'bg-violet-500', 'bg-orange-500', 'bg-teal-600'];
    $colour = $palette[crc32(\Illuminate\Support\Str::lower($name)) % count($palette)];
@endphp
<span class="avatar {{ $colour }} {{ $class ?? '' }}" aria-hidden="true">{{ \Illuminate\Support\Str::of($name)->trim()->substr(0, 1)->upper() }}</span>
