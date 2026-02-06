<x-guest-layout>

    <!-- Logo + Nome -->
    <div class="text-center space-y-3">
        <div class="inline-block p-1.5 rounded-full bg-gradient-to-br from-indigo-500/30 to-purple-500/30 backdrop-blur-sm border border-indigo-500/20 mx-auto">
            <img 
                src="{{ asset('images/logo-ninho.png') }}" 
                alt="Sistema Ninho" 
                class="h-16 w-16 rounded-full object-cover shadow-lg"
                onerror="this.style.display='none';"
            >
        </div>

        <h1 class="text-4xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
            Ninho
        </h1>
        <p class="text-base text-gray-400 font-medium">Gestão inteligente</p>
    </div>

    <!-- Card principal (glassmorphism + glow) -->
    <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl shadow-indigo-950/40 p-8 md:p-10 transition-all duration-300 mt-8">
        <div class="space-y-8">

            <x-auth-session-status class="text-center text-green-400 text-sm font-medium" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-7">
                @csrf

                <!-- Email -->
                <div class="relative group">
                    <x-text-input
                        id="email"
                        class="peer w-full bg-gray-800/50 border border-gray-600 rounded-xl px-5 py-4 text-white placeholder-transparent focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 focus:bg-gray-800/70 transition-all duration-300"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required autofocus autocomplete="username"
                        placeholder=" "
                    />
                    <x-input-label
                        for="email"
                        :value="__('E-mail')"
                        class="absolute left-5 top-4 text-gray-400 text-sm transition-all duration-300 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-indigo-400 peer-focus:bg-gray-900/80 peer-focus:px-2 peer-focus:rounded-md"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-400" />
                </div>

                <!-- Senha -->
                <div class="relative group">
                    <x-text-input
                        id="password"
                        class="peer w-full bg-gray-800/50 border border-gray-600 rounded-xl px-5 py-4 text-white placeholder-transparent focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 focus:bg-gray-800/70 transition-all duration-300"
                        type="password"
                        name="password"
                        required autocomplete="current-password"
                        placeholder=" "
                    />
                    <x-input-label
                        for="password"
                        :value="__('Senha')"
                        class="absolute left-5 top-4 text-gray-400 text-sm transition-all duration-300 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-indigo-400 peer-focus:bg-gray-900/80 peer-focus:px-2 peer-focus:rounded-md"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-400" />
                </div>

                <!-- Lembrar-me + Esqueceu senha -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-300 hover:text-white transition">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-600 text-indigo-500 focus:ring-indigo-500/30 bg-gray-800">
                        <span class="ml-2">{{ __('Lembrar-me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-400 hover:text-indigo-300 transition font-medium">
                            {{ __('Esqueceu a senha?') }}
                        </a>
                    @endif
                </div>

                <!-- Botão Entrar -->
                <x-primary-button class="w-full py-3.5 text-lg font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-900/40 hover:shadow-indigo-700/50 transition-all duration-300 rounded-xl">
                    {{ __('Entrar') }}
                </x-primary-button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-gray-500 mt-8">
        © {{ date('Y') }} Sistema Ninho • Todos os direitos reservados
    </p>

</x-guest-layout>