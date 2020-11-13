@include('errors', ['errors' => $errors])

<form action="{{ $route }}" method="POST">

    <p><label for="name">Name:</label></p>
    <input
        class="name placeholder-gray-800 w-full block rounded border border-gray-300 py-2 px-4 my-2 @error('name') border-red-600 @enderror"
        type="text"
        name="name"
        id="name"
        placeholder="Your Name"
        autocomplete="name"
        required
        value="{{ old('name') }}"
    >

    <p class="mt-2"><label for="email">Email:</label></p>
    <input
        class="email placeholder-gray-800 w-full block rounded border border-gray-300 py-2 px-4 my-2 @error('email') border-red-600 @enderror"
        type="email"
        name="email"
        id="email"
        placeholder="Your Email"
        autocomplete="email"
        required
        value="{{ old('email') }}"
    >

    <p class="mt-2"><label for="wishlist">Wishlist:</label></p>
    <textarea
        class="block w-full rounded border border-gray-300 my-2 py-2 px-4 placeholder-gray-800 @error('wishlist') border-red-600 @enderror"
        placeholder="Enter wishlist items here... (optional)"
        name="wishlist"
        id="wishlist"
        cols="30"
        rows="10">{{ old('wishlist') }}</textarea>

    <p class="font-medium">Tips for your wishlist:</p>

    <ul class="mb-4 list-disc">
        <li>If you include clothing, include your clothing size</li>
        <li>If you want a specific item, include a link</li>
        <li>Include options at multiple price points</li>
        <li>If you don't have your wishlist ready, skip it for now and share with the whole group later.</li>
    </ul>

    <input
        class="w-full rounded bg-teal-300 border border-teal-600 py-2 px-4 font-bold cursor-pointer"
        type="submit"
        value="{{ $submitText }}"
    >
</form>
