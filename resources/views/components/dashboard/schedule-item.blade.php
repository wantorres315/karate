{{-- resources/views/components/dashboard/schedule-item.blade.php --}}
@props(['session'])
<li class="flex justify-between items-center py-2">
    <span>
        {{ $session["time"] }} - {{ $session["patient"]->name ?? $session["patient"] }}

        @if(isset($session["role"]))
            @php
                $roles = $session["role"];
            @endphp
                (Consulta de {{ $roles }} {{ $session["type"] }})
        @else
            ({{ $session["type"] }})
        @endif
    </span>
</li>
