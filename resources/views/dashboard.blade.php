<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">&times;</button>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Total Activities --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Activities</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalActivities }}</p>
                        </div>
                    </div>
                </div>

                {{-- Done --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Completed</p>
                            <p class="text-2xl font-bold text-emerald-600">{{ $doneCount }}</p>
                        </div>
                    </div>
                    @if($totalActivities > 0)
                        <div class="mt-3 w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ round(($doneCount / $totalActivities) * 100) }}%"></div>
                        </div>
                    @endif
                </div>

                {{-- Pending --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Pending</p>
                            <p class="text-2xl font-bold text-amber-600">{{ $pendingCount }}</p>
                        </div>
                    </div>
                    @if($totalActivities > 0)
                        <div class="mt-3 w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ round(($pendingCount / $totalActivities) * 100) }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('daily.show', $today) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-amber-600 to-amber-800 text-white text-sm font-semibold rounded-xl hover:from-amber-700 hover:to-amber-900 transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Full Daily View
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('activities.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors shadow-sm border border-gray-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Activity
                    </a>
                @endif
            </div>

            {{-- Today's Activities --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900">Today's Activities</h3>
                    <p class="text-sm text-gray-500">Quick status update — click to update status and add remarks</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($activities as $activity)
                        @php
                            $logs = $latestLogs->get($activity->id);
                            $latestLog = $logs ? $logs->first() : null;
                            $status = $latestLog ? $latestLog->status : 'pending';
                        @endphp
                        <div class="p-4 sm:p-6 hover:bg-gray-50/50 transition-colors" x-data="{ showForm: false }">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                {{-- Status Badge --}}
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($status === 'done')
                                        <span class="shrink-0 w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        </span>
                                    @else
                                        <span class="shrink-0 w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </span>
                                    @endif

                                    <div class="min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $activity->title }}</h4>
                                        @if($activity->category)
                                            <span class="inline-block mt-0.5 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">{{ $activity->category }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Latest Log Info --}}
                                <div class="flex items-center gap-3 text-sm text-gray-500 sm:text-right">
                                    @if($latestLog)
                                        <div class="hidden sm:block">
                                            <span class="text-gray-700 font-medium">{{ $latestLog->user->name }}</span>
                                            <span class="block text-xs text-gray-400">{{ $latestLog->created_at->format('g:i A') }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">No updates yet</span>
                                    @endif

                                    <button @click="showForm = !showForm" class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Update
                                    </button>
                                </div>
                            </div>

                            {{-- Inline Update Form --}}
                            <div x-show="showForm" x-transition class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <form action="{{ route('activity-logs.store', $activity) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $today }}">
                                    <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" required>
                                        <option value="done" {{ $status === 'done' ? 'selected' : '' }}>✅ Done</option>
                                        <option value="pending" {{ $status !== 'done' ? 'selected' : '' }}>⏳ Pending</option>
                                    </select>
                                    <input type="text" name="remark" placeholder="Add a remark..." class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" maxlength="1000">
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
                                        Save
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-gray-500">No activities configured yet.</p>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('activities.create') }}" class="mt-3 inline-flex items-center text-amber-600 hover:text-amber-800 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Create your first activity
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
