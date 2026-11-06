@if(session()->has('success'))
    <div class="alert-success animate-pop-in" role="alert">
        <span aria-hidden="true">✅</span>
        <ul class="space-y-1">
            @foreach (\Illuminate\Support\Arr::wrap(session()->get('success')) as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
