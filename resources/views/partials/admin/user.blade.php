@php
$present = $user->is_present ? 'Ja' : 'Nee';
$presentAction = route('admin.users.present', compact('user'));
$newState = $user->is_present ? 'no' : 'yes';

$proxy = '–';
if ($user->proxy) {
    $proxy = sprintf(
        'Afgegeven aan %s (%s)',
        $user->proxy->name,
        $user->proxy->is_present ? 'aanwezig' : 'afwezig'
    );
} elseif ($user->proxyFor) {
    $proxy = sprintf(
        'Gemachtigd door %s (%s)',
        $user->proxyFor->name,
        $user->proxyFor->vote_label
    );
}

@endphp
<tr>
    <td class="py-2"><a href="{{ route('admin.users.show', compact('user')) }}">{{ $user->name }}</a></td>
    <td class="py-2">{{ $user->vote_label }}</td>
    <td class="py-2">
        {{ $present }}
        <button
            class="appearance-none ml-2"
            name="present"
            value="{{ $newState }}"
            form="user-action"
            formaction="{{ $presentAction }}"
        >wissel</button>
    </td>
    <td class="py-2">{{ $proxy }}</td>
</tr>
