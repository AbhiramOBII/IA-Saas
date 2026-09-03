<div
    x-data="{
        digits: ['','','','','',''],
        get otp() { return this.digits.join('') },
        focus(i) { this.$refs['d'+i]?.focus() },
        onInput(i) {
            let val = this.digits[i];
            if (val.length > 1) { this.digits[i] = val.slice(-1) }
            if (this.digits[i] && i < 5) { this.focus(i+1) }
            $wire.set('otp', this.otp)
        },
        onKey(i, e) {
            if (e.key === 'Backspace' && !this.digits[i] && i > 0) {
                this.focus(i-1)
            }
        },
        onPaste(e) {
            let text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6)
            text.split('').forEach((c,i) => { if(i<6) this.digits[i]=c })
            $wire.set('otp', this.otp)
            this.focus(Math.min(text.length, 5))
            e.preventDefault()
        }
    }"
>
    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-ia-primary/10 mb-4">
            <svg class="w-7 h-7 text-ia-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-ia-text mb-1">Check your email</h2>
        <p class="text-sm text-slate-500">
            We sent a 6-digit code to<br>
            <span class="font-medium text-ia-text">{{ $this->maskedEmail }}</span>
        </p>
    </div>

    {{-- Resent notice --}}
    @if($resent)
        <div class="flex items-center gap-2 bg-ia-success/10 text-ia-success text-sm px-4 py-3 rounded-lg mb-4">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            A new code has been sent to your email.
        </div>
    @endif

    <form wire:submit="verify" novalidate>

        {{-- 6-digit boxes --}}
        <div class="flex gap-2.5 justify-center mb-2" @paste.window="onPaste($event)">
            @for($i = 0; $i < 6; $i++)
                <input
                    x-ref="d{{ $i }}"
                    x-model="digits[{{ $i }}]"
                    @input="onInput({{ $i }})"
                    @keydown="onKey({{ $i }}, $event)"
                    @focus="$el.select()"
                    type="text" inputmode="numeric" maxlength="2"
                    class="w-11 h-13 text-center text-xl font-bold text-ia-text rounded-xl border-2
                           border-slate-200 bg-white focus:outline-none focus:border-ia-primary
                           focus:ring-2 focus:ring-ia-primary/30 transition caret-ia-primary
                           @error('otp') border-ia-error focus:border-ia-error focus:ring-ia-error/30 @enderror"
                >
            @endfor
        </div>

        @error('otp')
            <p class="text-center text-xs text-ia-error mb-4">{{ $message }}</p>
        @else
            <p class="text-center text-xs text-slate-400 mb-4">Code expires in 10 minutes</p>
        @enderror

        {{-- DEV ONLY: show current OTP for testing --}}
        @if(app()->isLocal())
            @php $devOtp = \App\Models\EmailVerificationOtp::where('email', $email)->first(); @endphp
            @if($devOtp)
                <div class="flex items-center justify-center gap-2 mb-4 px-3 py-2
                            bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <p class="text-xs text-amber-700">
                        Dev OTP:
                        <span class="font-mono font-bold tracking-widest">{{ $devOtp->otp }}</span>
                    </p>
                </div>
            @endif
        @endif

        {{-- Verify button --}}
        <button
            type="submit"
            class="w-full py-2.5 px-4 rounded-lg bg-ia-primary hover:bg-ia-primary-dark text-white
                   text-sm font-semibold tracking-wide transition focus:outline-none
                   focus:ring-2 focus:ring-ia-primary/50 focus:ring-offset-2
                   disabled:opacity-60 disabled:cursor-not-allowed"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Verify Email</span>
            <span wire:loading class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Verifying…
            </span>
        </button>

    </form>

    {{-- Resend --}}
    <p class="mt-5 text-center text-sm text-slate-500">
        Didn't receive it?
        <button
            wire:click="resend"
            wire:loading.attr="disabled"
            type="button"
            class="text-ia-primary font-medium hover:underline disabled:opacity-50"
        >
            Resend code
        </button>
    </p>

    <p class="mt-3 text-center text-sm text-slate-500">
        <a href="{{ route('register') }}" wire:navigate class="hover:underline text-slate-400">← Back to registration</a>
    </p>
</div>
