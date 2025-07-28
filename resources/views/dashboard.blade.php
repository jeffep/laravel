<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div>
        @if (session('status'))
            <p class="success-message">{{ session('status') }}</p>
        @endif
        @yield('control-content')
    </div>
</x-app-layout>
