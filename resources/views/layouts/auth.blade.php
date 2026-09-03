<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Login' }} — IA Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-white">

{{-- ═══════════════════════════════════════════════════════
     LEFT PANEL — Admin branding
     ═══════════════════════════════════════════════════════ --}}
<div class="hidden lg:flex lg:w-[55%] xl:w-[60%] relative overflow-hidden flex-col
            bg-gradient-to-br from-ia-bg-dark via-[#1a2570] to-ia-primary-dark">

    {{-- Ambient glow blobs --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -right-32 w-[520px] h-[520px] rounded-full
                    bg-ia-accent/20 blur-[120px]"></div>
        <div class="absolute bottom-0 -left-24 w-[420px] h-[420px] rounded-full
                    bg-ia-primary/50 blur-[100px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                    w-[280px] h-[280px] rounded-full bg-ia-accent/10 blur-[80px]"></div>
    </div>

    {{-- Dot-grid texture --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:radial-gradient(circle,#ffffff 1px,transparent 1px);background-size:24px 24px;"></div>

    {{-- Main content --}}
    <div class="relative z-10 flex flex-col h-full p-10 xl:p-14">

        {{-- Logo — centered in available space --}}
        <div class="my-auto">
            <img src="{{ asset('images/Logo-white.png') }}" alt="Intelligent Accelerators" class="h-16 w-auto">
        </div>

        {{-- Admin access notice --}}
        <div class="flex items-start gap-3 bg-white/6 border border-white/10 rounded-xl px-4 py-3.5 max-w-sm">
            <svg class="w-4 h-4 text-ia-accent shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <p class="text-white/60 text-xs leading-relaxed">
                This portal is restricted to authorised administrators only.
            </p>
        </div>

        {{-- Footer --}}
        <p class="text-white/25 text-xs mt-8">
            &copy; {{ date('Y') }}
            <a href="https://emergentconsultinginc.com/" target="_blank" rel="noopener"
               class="hover:text-white/50 transition underline underline-offset-2">
                Emergent Consulting Inc.
            </a>
            All rights reserved.
        </p>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     RIGHT PANEL — Login form
     ═══════════════════════════════════════════════════════ --}}
<div class="flex-1 flex items-center justify-center min-h-screen
            overflow-y-auto py-10 px-6 lg:px-12 bg-white"
     style="background-image:radial-gradient(circle,#e2e8f0 1px,transparent 1px);background-size:20px 20px;">

    <div class="w-full max-w-md">

        {{-- Mobile logo --}}
        <div class="mb-8 lg:hidden">
            <img src="{{ asset('images/Logo-color-01.png') }}" alt="Intelligent Accelerators"
                 class="h-8 w-auto" onerror="this.style.display='none'">
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            {{ $slot }}
        </div>

    </div>
</div>

</body>
</html>
