<div class="max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
        <a href="{{ route('admin.users') }}" wire:navigate
           class="hover:text-ia-primary transition">Users</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
        <span class="text-ia-text font-medium">Edit User</span>
    </div>

    {{-- User identity card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-ia-primary/10 flex items-center justify-center
                    text-ia-primary font-bold text-lg shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-ia-text">{{ $user->name }}</p>
            <p class="text-sm text-slate-400">{{ $user->email }}</p>
        </div>
        @php
            $badge = match($user->status) {
                'approved'    => 'bg-ia-success/10 text-ia-success',
                'disabled'    => 'bg-ia-warning/10 text-ia-warning',
                'deactivated',
                'rejected'    => 'bg-ia-error/10 text-ia-error',
                default       => 'bg-slate-100 text-slate-500',
            };
        @endphp
        <span class="ml-auto inline-block px-3 py-1 rounded-full text-xs font-semibold capitalize {{ $badge }}">
            {{ $user->status }}
        </span>
    </div>

    {{-- Edit form --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">

        <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-base font-semibold text-ia-text">Profile Details</h2>
            <p class="text-sm text-slate-400 mt-0.5">Update the user's name, organisation and designation.</p>
        </div>

        <form wire:submit="save" class="px-6 py-6 space-y-5">

            {{-- Full Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-ia-text mb-1.5">Full Name</label>
                <input wire:model="name" id="name" type="text" placeholder="Full Name"
                       class="w-full px-4 py-2.5 rounded-lg border text-sm bg-white
                              border-slate-300 focus:outline-none focus:ring-2 focus:ring-ia-primary/40
                              focus:border-ia-primary transition
                              @error('name') border-ia-error focus:ring-ia-error/30 @enderror">
                @error('name') <p class="mt-1 text-xs text-ia-error">{{ $message }}</p> @enderror
            </div>

            {{-- Organisation + Designation side by side --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="organization" class="block text-sm font-medium text-ia-text mb-1.5">Organisation</label>
                    <input wire:model="organization" id="organization" type="text" placeholder="Acme Corp"
                           class="w-full px-4 py-2.5 rounded-lg border text-sm bg-white
                                  border-slate-300 focus:outline-none focus:ring-2 focus:ring-ia-primary/40
                                  focus:border-ia-primary transition
                                  @error('organization') border-ia-error focus:ring-ia-error/30 @enderror">
                    @error('organization') <p class="mt-1 text-xs text-ia-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="designation" class="block text-sm font-medium text-ia-text mb-1.5">Designation</label>
                    <input wire:model="designation" id="designation" type="text" placeholder="Data Analyst"
                           class="w-full px-4 py-2.5 rounded-lg border text-sm bg-white
                                  border-slate-300 focus:outline-none focus:ring-2 focus:ring-ia-primary/40
                                  focus:border-ia-primary transition
                                  @error('designation') border-ia-error focus:ring-ia-error/30 @enderror">
                    @error('designation') <p class="mt-1 text-xs text-ia-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-ia-primary hover:bg-ia-primary-dark text-white
                               text-sm font-semibold transition focus:outline-none focus:ring-2
                               focus:ring-ia-primary/50 focus:ring-offset-2 disabled:opacity-60"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Save Changes</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>

                <a href="{{ route('admin.users') }}" wire:navigate
                   class="px-5 py-2.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50
                          text-sm font-medium text-slate-600 transition">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>
