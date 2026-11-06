@if(session()->has('success'))
    <div class="alert alert-success" role="alert">
        @include('icon', ['name' => 'check', 'class' => 'mt-0.5 size-5'])
        <ul class="space-y-1">
            @foreach (\Illuminate\Support\Arr::wrap(session()->get('success')) as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
