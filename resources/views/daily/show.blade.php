<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daily Activity View
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('daily.show', $prevDate) }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-500" title="Previous day">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="text-sm font-medium text-gray-700 px-3 py-1 bg-gray-100 rounded-lg">
                    {{ \Carbon\Carbon::parse($date)->format('l, M j, Y') }}
                </span>
                @if(!$isToday)
                    <a href="{{ route('daily.show', $nextDate) }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-500" title="Next day">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
                @if(!$isToday)
                    <a href="{{ route('daily.show') }}" class="text-xs text-amber-600 hover:text-amber-800 font-medium ml-2">Today →</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">&times;</button>
                </div>
            @endif

            {{-- Shift Handover Summary --}}
            @php
                $totalForDay = $activities->count();
                $doneForDay = 0;
                $pendingForDay = 0;
                foreach ($activities as $a) {
                    $actLogs = $logs->get($a->id);
                    if ($actLogs && $actLogs->first() && $actLogs->first()->status === 'done') {
                        $doneForDay++;
                    } else {
                        $pendingForDay++;
                    }
                }
            @endphp
            <div class="bg-gradient-to-r from-amber-700 to-amber-900 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Shift Handover Summary</h3>
                        <p class="text-amber-200 text-sm mt-1">{{ $isToday ? 'Current status for today' : 'Archived view for ' . \Carbon\Carbon::parse($date)->format('M j') }}</p>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-white">{{ $doneForDay }}</p>
                            <p class="text-xs text-amber-200 uppercase tracking-wider font-semibold">Done</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-white">{{ $pendingForDay }}</p>
                            <p class="text-xs text-amber-200 uppercase tracking-wider font-semibold">Pending</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-white">{{ $totalForDay }}</p>
                            <p class="text-xs text-amber-200 uppercase tracking-wider font-semibold">Total</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activities List --}}
            @forelse($activities as $activity)
                @php
                    $activityLogs = $logs->get($activity->id, collect());
                    $latestLog = $activityLogs->first();
                    $currentStatus = $latestLog ? $latestLog->status : 'pending';
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ expanded: false, showForm: false }">
                    {{-- Activity Header --}}
                    <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-3 cursor-pointer" @click="expanded = !expanded">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if($currentStatus === 'done')
                                <span class="shrink-0 w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                            @else
                                <span class="shrink-0 w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            @endif

                            <div class="min-w-0">
                                <h4 class="font-semibold text-gray-900 truncate">{{ $activity->title }}</h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($activity->category)
                                        <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">{{ $activity->category }}</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $activityLogs->count() }} update(s)</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3">
                            @if($latestLog)
                                <div class="text-sm text-right hidden sm:block">
                                    <span class="text-gray-700 font-medium">{{ $latestLog->user->name }}</span>
                                    <span class="block text-xs text-gray-400">{{ $latestLog->created_at->format('g:i A') }}</span>
                                </div>
                            @endif

                            @if($currentStatus === 'done')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">Done</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                            @endif

                            <button @click.stop="showForm = !showForm" class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Update
                            </button>

                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Inline Update Form --}}
                    <div x-show="showForm" x-transition class="px-4 sm:px-6 pb-4">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <form action="{{ route('activity-logs.store', $activity) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                                @csrf
                                <input type="hidden" name="date" value="{{ $date }}">
                                <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" required>
                                    <option value="done" {{ $currentStatus === 'done' ? 'selected' : '' }}>✅ Done</option>
                                    <option value="pending" {{ $currentStatus !== 'done' ? 'selected' : '' }}>⏳ Pending</option>
                                </select>
                                <input type="text" name="remark" placeholder="Add a remark..." class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" maxlength="1000">
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">Save</button>
                            </form>
                        </div>
                    </div>

                    {{-- Log History (Expandable) --}}
                    <div x-show="expanded" x-transition class="border-t border-gray-100">
                        @if($activityLogs->count() > 0)
                            <div class="divide-y divide-gray-50">
                                @foreach($activityLogs as $log)
                                    <div class="px-4 sm:px-6 py-3 flex items-start gap-3 text-sm {{ $loop->first ? 'bg-gray-50/50' : '' }}">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white text-xs font-bold shrink-0 mt-0.5">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900">{{ $log->user->name }}</span>
                                                @if($log->status === 'done')
                                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700">Done</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                                                @endif
                                                <span class="text-xs text-gray-400">{{ $log->created_at->format('g:i:s A') }}</span>
                                            </div>
                                            @if($log->remark)
                                                <p class="text-gray-600 mt-1">{{ $log->remark }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-4 text-center text-sm text-gray-400">No updates recorded for this day.</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-gray-500">No active activities found.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
