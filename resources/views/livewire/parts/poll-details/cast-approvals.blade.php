<h3 class="font-title text-xl mb-2 mt-4">Uitgebrachte oordelen</h3>

<table class="w-full">
    <thead>
        <tr>
            <th>Datum en tijd</th>
            <th>Naam</th>
            <th>Antwoord</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($judgement->approvals as $vote)
        <tr>
            @foreach ($vote as $cell)
            <td>{{ $cell }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
