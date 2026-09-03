<div>

    {{-- ── Page header ──────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-ia-text">Users</h2>
            <p class="text-sm text-slate-500 mt-0.5">Manage registered user accounts</p>
        </div>
    </div>

    {{-- ── Flash message ────────────────────────────────────────── --}}
    @if($flash)
        <div class="flex items-center gap-2 text-sm px-4 py-3 rounded-lg mb-5
                    {{ $flashType === 'success'
                        ? 'bg-ia-success/10 text-ia-success'
                        : 'bg-ia-error/10 text-ia-error' }}"
             x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                @if($flashType === 'success')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                @endif
            </svg>
            {{ $flash }}
        </div>
    @endif

    {{-- ── Filters + Search ─────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                 fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search"
                   type="search" placeholder="Search name, email or organisation…"
                   class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border border-slate-200 bg-white
                          text-ia-text placeholder-slate-400 focus:outline-none focus:ring-2
                          focus:ring-ia-primary/30 focus:border-ia-primary transition">
        </div>

        <div class="flex rounded-lg border border-slate-200 bg-white overflow-hidden text-sm shrink-0">
            @foreach(['all' => 'All', 'approved' => 'Approved', 'disabled' => 'Disabled', 'deactivated' => 'Deactivated'] as $val => $label)
                <button wire:click="$set('filter', '{{ $val }}')"
                        class="px-4 py-2 font-medium transition border-r last:border-r-0 border-slate-200
                               {{ $filter === $val ? 'bg-ia-primary text-white' : 'text-slate-500 hover:bg-slate-50' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-left">
                    <th class="px-5 py-3 font-medium text-slate-500">User</th>
                    <th class="px-5 py-3 font-medium text-slate-500">Email</th>
                    <th class="px-5 py-3 font-medium text-slate-500">Registered</th>
                    <th class="px-5 py-3 font-medium text-slate-500">Status</th>
                    <th class="px-5 py-3 font-medium text-slate-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($this->users as $user)
                    <tr class="hover:bg-slate-50/50 transition" wire:key="user-{{ $user->id }}">

                        {{-- User (name + org + designation) --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-ia-primary/10 flex items-center justify-center
                                            text-ia-primary font-semibold text-xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-ia-text truncate">{{ $user->name }}</p>
                                    @if($user->organization || $user->designation)
                                        <p class="text-xs text-slate-400 truncate">
                                            {{ collect([$user->designation, $user->organization])->filter()->implode(' · ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $user->email }}</td>

                        {{-- Registered --}}
                        <td class="px-5 py-3.5 text-slate-400 text-xs whitespace-nowrap">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        {{-- Status badge --}}
                        <td class="px-5 py-3.5">
                            @php
                                $badge = match($user->status) {
                                    'approved'    => 'bg-ia-success/10 text-ia-success',
                                    'disabled'    => 'bg-ia-warning/10 text-ia-warning',
                                    'deactivated' => 'bg-ia-error/10 text-ia-error',
                                    'rejected'    => 'bg-ia-error/10 text-ia-error',
                                    default       => 'bg-slate-100 text-slate-500',
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize {{ $badge }}">
                                {{ $user->status }}
                            </span>
                        </td>

                        {{-- Actions ──────────────────────────────── --}}
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">

                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   wire:navigate
                                   title="Edit user"
                                   class="p-1.5 rounded-lg text-slate-400 hover:text-ia-primary hover:bg-ia-primary/10 transition inline-flex">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </a>

                                {{-- Disable / Enable toggle --}}
                                @if($user->status === 'approved')
                                    <button wire:click="disable({{ $user->id }})"
                                            wire:confirm="Disable {{ $user->name }}? They will not be able to sign in."
                                            title="Disable account"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-ia-warning hover:bg-ia-warning/10 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                @elseif($user->status === 'disabled')
                                    <button wire:click="enable({{ $user->id }})"
                                            wire:confirm="Re-enable {{ $user->name }}?"
                                            title="Re-enable account"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-ia-success hover:bg-ia-success/10 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @elseif(in_array($user->status, ['deactivated', 'rejected']))
                                    <button wire:click="enable({{ $user->id }})"
                                            wire:confirm="Re-activate {{ $user->name }} and restore access?"
                                            title="Re-activate"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-ia-success hover:bg-ia-success/10 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Deactivate (only for non-deactivated) --}}
                                @if(!in_array($user->status, ['deactivated', 'rejected']))
                                    <button wire:click="deactivate({{ $user->id }})"
                                            wire:confirm="Permanently deactivate {{ $user->name }}'s account?"
                                            title="Deactivate account"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-ia-error hover:bg-ia-error/10 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                                        </svg>
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">
                            @if($search || $filter !== 'all')
                                No users match your current filters.
                            @else
                                No registered users yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->users->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $this->users->links() }}
            </div>
        @endif
    </div>

    {{-- Flash from redirect (e.g. after edit save) --}}
    @if(session('flash'))
        <div class="fixed bottom-5 right-5 flex items-center gap-2 bg-ia-success/10 text-ia-success
                    text-sm px-4 py-3 rounded-lg shadow-lg border border-ia-success/20 z-50"
             x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('flash') }}
        </div>
    @endif

</div>
