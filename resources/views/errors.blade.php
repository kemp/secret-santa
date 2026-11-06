@if ($errors->any())
    <div class="alert alert-error mb-4" role="alert">
        @include('icon', ['name' => 'alert', 'class' => 'mt-0.5 size-5'])
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
