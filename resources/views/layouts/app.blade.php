<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- CORREÇÃO: Usando @yield('title') para o título da página. Garante que o nome do app seja o fallback. --}}
        <title>@yield('title', config('app.name', 'Sistema Ninho'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        {{-- Adiciona folhas de estilo customizadas --}}
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{-- Inclui o menu de navegação (Onde ajustamos os botões) --}}
            @include('layouts.navigation')

            @hasSection('header')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main>
                {{-- Se a variável $slot existir (Breeze) --}}
                @isset($slot)
                    {{ $slot }}
                @endisset

                {{-- Para views com @section('content') --}}
                @yield('content')
            </main>
        </div>
        
        {{-- Adiciona scripts customizados no final do body --}}
        @stack('scripts')
    </body>
</html>