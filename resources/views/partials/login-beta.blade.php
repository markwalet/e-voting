<div class="p-4 rounded border border-red-600 my-4">
    <strong class="text-red-600 font-bold block">Applicatie in beta-modus</strong>
    Je kan inloggen met de volgende test-accounts:

    <ul class="list-inside list-disc">
        @foreach (App\Models\User::where('email', 'like', '%@beta.example.com')->get() as $user)
        <li><code>{{ $user->email }}</code> - {{ $user->name }}</li>
        @endforeach
        <ul>
</div>
