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
    <td class="py-2" colspan="4">
        <div class="notice notice--info">
            Er zijn geen leden die voldoen aan de zoekresultaten
        </div>
    </td>
</tr>
