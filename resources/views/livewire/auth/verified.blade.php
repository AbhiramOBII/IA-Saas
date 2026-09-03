<div class="text-center py-2">

    {{-- Success icon --}}
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 mb-5">
        <svg class="w-8 h-8 text-ia-success" fill="none" viewBox="0 0 24 24"
             stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h2 class="text-xl font-semibold text-ia-text mb-1">You're approved!</h2>
    <p class="text-sm text-slate-500 mb-6">
        Your account is active. A welcome email with your download link has been sent.
    </p>

    {{-- Steps --}}
    <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-3 mb-6">
        @foreach([
            'Check your inbox for the welcome email.',
            'Download and install the Intelligent Accelerators desktop app.',
            'Sign in with your registered email and password.',
        ] as $i => $step)
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full bg-ia-primary/15 text-ia-primary flex items-center
                             justify-center text-xs font-bold shrink-0 mt-0.5">{{ $i + 1 }}</span>
                <p class="text-sm text-slate-600">{{ $step }}</p>
            </div>
        @endforeach
    </div>

    @if(config('app.download_url') && config('app.download_url') !== '#')
        <a href="{{ config('app.download_url') }}"
           class="inline-flex items-center gap-2 w-full justify-center
                  py-2.5 px-5 rounded-lg bg-ia-primary hover:bg-ia-primary-dark
                  text-white text-sm font-semibold transition mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download Desktop App
        </a>
    @endif

    <p class="text-xs text-slate-400">
        Need help? Contact
        <a href="mailto:support@emergentconsultinginc.com" class="text-ia-primary hover:underline">
            support@emergentconsultinginc.com
        </a>
    </p>

</div>
