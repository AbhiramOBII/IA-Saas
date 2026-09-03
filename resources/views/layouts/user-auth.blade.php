<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Intelligent Accelerators' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-white">

{{-- ═══════════════════════════════════════════════════════
     LEFT PANEL — Branding + illustration (hidden on mobile)
     ═══════════════════════════════════════════════════════ --}}
<div class="hidden lg:flex lg:w-[55%] xl:w-[60%] relative overflow-hidden flex-col
            bg-gradient-to-br from-ia-bg-dark via-[#1a2570] to-ia-primary-dark">

    {{-- ── Ambient glow blobs ────────────────────────────────── --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -right-32 w-[520px] h-[520px] rounded-full
                    bg-ia-accent/20 blur-[120px]"></div>
        <div class="absolute bottom-0 -left-24 w-[420px] h-[420px] rounded-full
                    bg-ia-primary/50 blur-[100px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                    w-[280px] h-[280px] rounded-full bg-ia-accent/10 blur-[80px]"></div>
    </div>

    {{-- ── Dot-grid texture ──────────────────────────────────── --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:radial-gradient(circle,#ffffff 1px,transparent 1px);background-size:24px 24px;"></div>

    {{-- ── Main content ──────────────────────────────────────── --}}
    <div class="relative z-10 flex flex-col h-full p-10 xl:p-14">

        {{-- Logo --}}
        <div>
            <img src="{{ asset('images/Logo-white.png') }}" alt="Intelligent Accelerators" class="h-10 w-auto">
        </div>

        <hr class="border-ia-primary/30 my-4">
        
        {{-- Headline --}}
        <div class="mt-auto mb-2">
           
            <h2 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-5">
                Bring Intelligence to<br>
                <span class="text-ia-accent">Every Data Workflow</span>
            </h2>
            <p class="text-white/55 text-sm leading-relaxed max-w-xs mb-10">
               Access purpose-built intelligent accelerators that help you process, analyse and transform enterprise data faster — securely from your desktop.            </p>

            {{-- Feature bullets --}}
            <ul class="space-y-3 mb-10">
                @foreach([
                    'Intelligent, task-specific accelerators',
                    'Secure local data processing',
                    'Centrally managed. Locally executed.',
                ] as $feature)
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-ia-accent shrink-0"></span>
                        <span class="text-white/75 text-sm">{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>


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
     RIGHT PANEL — Form area
     ═══════════════════════════════════════════════════════ --}}
<div class="flex-1 flex items-center justify-center min-h-screen
            overflow-y-auto py-10 px-6 lg:px-12 bg-white"
     style="background-image:radial-gradient(circle,#e2e8f0 1px,transparent 1px);background-size:20px 20px;">

    <div class="w-full max-w-md">

        {{-- Mobile logo (shown only when left panel is hidden) --}}
        <div class="mb-8 lg:hidden">
            <img src="{{ asset('images/Logo-white.png') }}" alt="Intelligent Accelerators"
                 class="h-8 w-auto"
                 onerror="this.style.display='none'">
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-2xl">
            {{ $slot }}
        </div>

    </div>
</div>

</body>
</html>
