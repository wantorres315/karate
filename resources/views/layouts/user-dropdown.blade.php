<x-dropdown>
    <x-slot name="trigger">
@php
    $firstProfile = auth()->user()->profiles()->orderBy('number_kak')->first();
    $photo = $firstProfile?->photo ?? asset('assets/avatars/default.png');
@endphp

<img src="{{ $photo }}"
     alt="{{ auth()->user()->name }}"
     onerror="this.onerror=null; this.src='{{ asset('assets/avatars/default.png') }}'"
     class="h-9 w-9 rounded-full object-cover" />
    
    </x-slot>

    <x-slot name="content">
        <x-responsive-nav-link :href="route('profile.edit')">
            {{ __('Profile') }}
        </x-responsive-nav-link>

        @role('admin')
        <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                {{ __('Perfis de Usuário') }}
        </x-responsive-nav-link>
        @endrole
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-responsive-nav-link>
        </form>
    </x-slot>
</x-dropdown>
