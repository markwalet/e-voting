<tr>
    <td class="py-2"><a href="{{ route('admin.users.show', compact('user')) }}">{{ $user->name }}</a></td>
    <td class="py-2">{{ $user->vote_label }}</td>
    <td class="py-2">
        {{ $user->is_present ? 'Ja' : 'Nee' }}
        @can('setPresent', $user)
        <button class="appearance-none ml-2 text-yellow-600" wire:click.prevent="setPresent({{ !$user->is_present }})">⇄</button>
        @endcan
    </td>
    @if ($user->proxyFor)
    <td class="py-2">
        Van <a href="{{ route('admin.users.show', ['user' => $user->proxyFor]) }}">{{ $user->proxyFor->name }}</a>
    </td>
    @elseif ($user->proxy)
    <td class="py-2">
        Aan <a href="{{ route('admin.users.show', ['user' => $user->proxy]) }}">{{ $user->proxy->name }}</a>
    </td>
    @else
    <td class="py-2">&mdash;</td>
    @endif
</tr>
