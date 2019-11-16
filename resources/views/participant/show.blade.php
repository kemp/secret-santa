@extends('layouts.app')

@section('main')

    <p>You've been invited to participate in a Secret Santa.</p>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('participant.confirm', $participant) }}" method="POST" class="my-4">
        <label for="wishlist">Do you have anything on your wishlist? You won't be able to update this later.</label>
        <textarea class="block w-full rounded border border-gray-300 my-2 py-2 px-4 placeholder-gray-800" placeholder="Enter wishlist items here... (optional)" name="wishlist" id="wishlist" cols="30" rows="10">{{ old('wishlist') }}</textarea>

        <input class="w-full rounded bg-teal-300 border border-teal-600 py-2 px-4 cursor-pointer" type="submit" value="Let's do this!">
    </form>

@endsection
