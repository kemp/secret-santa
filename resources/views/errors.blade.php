@if ($errors->any())
    <div class="alert-error mb-4" role="alert">
        <span aria-hidden="true">⚠️</span>
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
