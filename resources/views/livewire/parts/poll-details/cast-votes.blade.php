<h3 class="font-title text-xl mb-2 mt-4">Uitgebrachte stemmen</h3>

Leden konden stemmen van {{ $poll->started_at->format('d-m-Y H:i:s (T)') }} tot {{ $poll->ended_at->format('d-m-Y H:i:s (T)') }}.

<table class="w-full">
    <thead>
        <tr>
            <th>Datum en tijd</th>
            <th>Antwoord</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($results->votes as $vote)
        <tr>
            @foreach ($vote as $cell)
            <td>{{ $cell }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
