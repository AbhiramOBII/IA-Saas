<div>
    <h2 class="text-xl font-semibold text-ia-text mb-1">Create your account</h2>
    <p class="text-sm text-slate-500 mb-6">Get started with Intelligent Accelerators</p>

    <form wire:submit="register" novalidate>

        {{-- Full Name --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-ia-text mb-1.5">Full Name</label>
            <input
                wire:model="name"
                id="name" type="text" autocomplete="name" placeholder="John Smith"
                class="w-full px-4 py-2.5 rounded-lg border text-sm text-ia-text bg-white
                       border-slate-300 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-ia-primary/40 focus:border-ia-primary
                       transition @error('name') border-ia-error focus:ring-ia-error/30 @enderror"
            >
            @error('name') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-ia-text mb-1.5">Email Address</label>
            <input
                wire:model="email"
                id="email" type="email" autocomplete="email" placeholder="you@example.com"
                class="w-full px-4 py-2.5 rounded-lg border text-sm text-ia-text bg-white
                       border-slate-300 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-ia-primary/40 focus:border-ia-primary
                       transition @error('email') border-ia-error focus:ring-ia-error/30 @enderror"
            >
            @error('email') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
        </div>

        {{-- Organisation & Designation (side by side on wider screens) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

            <div>
                <label for="organization" class="block text-sm font-medium text-ia-text mb-1.5">Organisation Name</label>
                <input
                    wire:model="organization"
                    id="organization" type="text" placeholder="Acme Corp"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm text-ia-text bg-white
                           border-slate-300 placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-ia-primary/40 focus:border-ia-primary
                           transition @error('organization') border-ia-error focus:ring-ia-error/30 @enderror"
                >
                @error('organization') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="designation" class="block text-sm font-medium text-ia-text mb-1.5">Designation</label>
                <input
                    wire:model="designation"
                    id="designation" type="text" placeholder="Data Analyst"
                    class="w-full px-4 py-2.5 rounded-lg border text-sm text-ia-text bg-white
                           border-slate-300 placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-ia-primary/40 focus:border-ia-primary
                           transition @error('designation') border-ia-error focus:ring-ia-error/30 @enderror"
                >
                @error('designation') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- Password --}}
        <div class="mb-4" x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-ia-text mb-1.5">Password</label>
            <div class="relative">
                <input
                    wire:model="password"
                    id="password" :type="show ? 'text' : 'password'" autocomplete="new-password"
                    placeholder="Min 8 chars, upper, lower, number"
                    class="w-full px-4 py-2.5 pr-10 rounded-lg border text-sm text-ia-text bg-white
                           border-slate-300 placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-ia-primary/40 focus:border-ia-primary
                           transition @error('password') border-ia-error focus:ring-ia-error/30 @enderror"
                >
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600"
                        tabindex="-1">
                    <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-5">
            <label for="password_confirmation" class="block text-sm font-medium text-ia-text mb-1.5">Confirm Password</label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation" type="password" autocomplete="new-password" placeholder="Re-enter password"
                class="w-full px-4 py-2.5 rounded-lg border text-sm text-ia-text bg-white
                       border-slate-300 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-ia-primary/40 focus:border-ia-primary
                       transition @error('password_confirmation') border-ia-error focus:ring-ia-error/30 @enderror"
            >
            @error('password_confirmation') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
        </div>

        {{-- Terms of Use --}}
        <div class="mb-6">
            <label class="flex items-start gap-3 cursor-pointer select-none group">
                <input
                    wire:model="terms"
                    type="checkbox"
                    class="mt-0.5 w-4 h-4 rounded border-slate-300 text-ia-primary
                           focus:ring-ia-primary/40 cursor-pointer shrink-0"
                >
                <span class="text-sm text-slate-600 leading-relaxed">
                    I have read and agree to the
                    <a href="#" class="text-ia-primary font-medium hover:underline">Terms of Use</a>
                    and
                    <a href="#" class="text-ia-primary font-medium hover:underline">Privacy Policy</a>
                </span>
            </label>
            @error('terms') <p class="mt-1.5 text-xs text-ia-error">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full py-2.5 px-4 rounded-lg bg-ia-primary hover:bg-ia-primary-dark text-white
                   text-sm font-semibold tracking-wide transition focus:outline-none
                   focus:ring-2 focus:ring-ia-primary/50 focus:ring-offset-2
                   disabled:opacity-60 disabled:cursor-not-allowed"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Create Account</span>
            <span wire:loading class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Sending OTP…
            </span>
        </button>

    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
        Login is handled through the Intelligent Accelerators desktop application.
    </p>
</div>
