<div class="rounded shadow mb-4 p-4">
    {{-- Title --}}
    <div class="flex flex-row items-start mb-4">
        <h3 class="font-title font-normal mr-4 w-full text-xl">{{ $poll->title }}</h3>
        <div class="text-brand-600 flex-none">{{ $poll->status }}</div>
    </div>

    {{-- Content --}}
    {{ $slot }}
</div>
