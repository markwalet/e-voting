<h2 class="font-title font-bold text-xl">Machtigingen</h2>

<p class="mb-4">
    Hieronder staat een overzicht van alle machtigingen. Alleen het bestuur kan
    machtigingen toevoegen en intrekken. Wijzigingen gaan direct in.
</p>

<form action="{{ route('admin.auths.remove') }}" method="POST" id="remove-item">
    @csrf
</form>

<table>
    <thead>
        <tr>
            <th>Afgegeven door</th>
            <th>Gemachtigde gebruiker</th>
            <th>Acties</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($proxiedUsers as $item)
        <tr>
            <td>{{ $proxiedUsers->name }}</td>
            <td>{{ $proxiedUsers->authorises->name }}</td>
            <td>
                <button type="submit" name="user" value="{{ $proxiedUsers->id }}" form="remove-item">
                    Intrekken
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3">
                <div class="p-4 text-center text-brand-500">
                    Er zijn geen gemachtigde gebruikers
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<h3 class="font-title text-lg">Machtiging toevoegen</h3>

<form action="{{ route('admin.auth'" method="post">
    @csrf
</form>
