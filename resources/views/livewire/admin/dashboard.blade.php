<div>

    {{-- ===== Stats Row ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        {{-- Total Users --}}
        <div class="bg-ia-surface rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ia-primary/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-ia-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Total Users</p>
                <p class="text-3xl font-bold text-ia-text mt-0.5">{{ $totalUsers }}</p>
                <p class="text-xs text-slate-400 mt-1">All registered accounts</p>
            </div>
        </div>

        {{-- Rejected Users --}}
        <div class="bg-ia-surface rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ia-error/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-ia-error" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Rejected Users</p>
                <p class="text-3xl font-bold text-ia-text mt-0.5">{{ $rejectedUsers }}</p>
                <p class="text-xs text-slate-400 mt-1">Manually blocked</p>
            </div>
        </div>

        {{-- Approved Users --}}
        <div class="bg-ia-surface rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ia-success/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-ia-success" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Approved Users</p>
                <p class="text-3xl font-bold text-ia-text mt-0.5">{{ $approvedUsers }}</p>
                <p class="text-xs text-slate-400 mt-1">Welcome email sent</p>
            </div>
        </div>

        {{-- Active API Tokens --}}
        <div class="bg-ia-surface rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ia-warning/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-ia-warning" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Active API Tokens</p>
                <p class="text-3xl font-bold text-ia-text mt-0.5">{{ $activeTokens }}</p>
                <p class="text-xs text-slate-400 mt-1">Desktop app sessions</p>
            </div>
        </div>

    </div>

    {{-- ===== Lower panels ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Usage summary --}}
        <div class="xl:col-span-2 bg-ia-surface rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold text-ia-text">Usage Metrics</h2>
                <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">Last 30 days</span>
            </div>
            <div class="h-48 flex flex-col items-center justify-center rounded-xl bg-slate-50 border border-dashed border-slate-200">
                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                <p class="text-sm text-slate-400">Usage data will appear here</p>
                <p class="text-xs text-slate-300 mt-1">Connect the desktop app to start logging</p>
            </div>
        </div>

        {{-- Platform status --}}
        <div class="bg-ia-surface rounded-2xl border border-slate-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-ia-text mb-5">Platform Status</h2>
            <ul class="space-y-4">
                <li class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">API Endpoint</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-ia-success">
                        <span class="w-2 h-2 rounded-full bg-ia-success inline-block animate-pulse"></span>
                        Online
                    </span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Email Service</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-ia-warning">
                        <span class="w-2 h-2 rounded-full bg-ia-warning inline-block"></span>
                        Not configured
                    </span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Passport OAuth</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-ia-success">
                        <span class="w-2 h-2 rounded-full bg-ia-success inline-block"></span>
                        Active
                    </span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Database</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-ia-success">
                        <span class="w-2 h-2 rounded-full bg-ia-success inline-block"></span>
                        Connected
                    </span>
                </li>
            </ul>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 leading-relaxed">
                    Phase 1 — Auth & Admin foundation.<br>
                    Desktop accelerator integration pending.
                </p>
            </div>
        </div>

    </div>

</div>
