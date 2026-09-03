<x-layouts.user-auth title="Request Received">
    <div class="text-center py-2">

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-ia-primary/10 mb-5">
            <svg class="w-8 h-8 text-ia-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
        </div>

        <h2 class="text-xl font-semibold text-ia-text mb-2">Request received!</h2>

        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            Thank you for verifying your email.<br>
            Your account is currently <span class="font-medium text-ia-text">pending review</span>.
        </p>

        {{-- Info box --}}
        <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-3 mb-6">
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full bg-ia-primary/20 text-ia-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                <p class="text-sm text-slate-600">Our team will review your registration.</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full bg-ia-primary/20 text-ia-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                <p class="text-sm text-slate-600">Once approved, you'll receive a welcome email with your desktop application download link.</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full bg-ia-primary/20 text-ia-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                <p class="text-sm text-slate-600">Install the app and sign in with your registered credentials.</p>
            </div>
        </div>

        <p class="text-xs text-slate-400">
            Questions? Contact us at
            <a href="mailto:support@ia-platform.com" class="text-ia-primary hover:underline">
                support@ia-platform.com
            </a>
        </p>

    </div>
</x-layouts.user-auth>
