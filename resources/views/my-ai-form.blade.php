<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My AI App</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-amber-100">
        <div class="container mx-auto mt-6 max-w-2xl px-4">
            <img src="{{ asset('images/my-ai-form-png.png') }}"
            alt="My AI Form Logo"
            class="w-48 mx-auto mb-4">
            <h1 class="text-4xl font-extrabold tracking-wide text-blue-600 drop-shadow-md mb-6">MTG Commander Deck Generator</h1>

            <form method="POST" action="{{ route('ai.generate') }}">
                @csrf

                <label for="title" class="block font-medium">Description of Commander deck you would like to make:</label>
                <input type="text" name="title" id="title"
                    value="{{ old('title', $title ?? '') }}"
                    class="border w-full p-2 mt-1" required>
                @error('title') <div class="text-red-600 mt-1">{{ $message }}</div>
                @enderror

                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 mt-4 rounded">Generate</button>
            </form>

            @error('error') <div class="text-red-600 mt-4">{{ $message }}</div>
            @enderror

            @isset($output)
                <div class="mt-6">
                    <h2 class="text-xl font-semibold mb-2">Here's a deck idea for you:</h2>
                    <textarea class="border w-full p-3 h-64 whitespace-pre-wrap">{{ $output }}</textarea>
                </div>
            @endisset
        </div>
    </body>
</html>