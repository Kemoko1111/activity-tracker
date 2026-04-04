<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Form --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Activity Logs</h3>
                <form action="{{ route('reports.generate') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @csrf
                    <div>
                        <label for="start_date" class="block text-xs font-medium text-gray-500 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date"
                               value="{{ $validated['start_date'] ?? \Carbon\Carbon::today()->subDays(7)->format('Y-m-d') }}"
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" required>
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-medium text-gray-500 mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date"
                               value="{{ $validated['end_date'] ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                               class="w-full rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" required>
                    </div>
                    <div>
                        <label for="activity_id" class="block text-xs font-medium text-gray-500 mb-1">Activity</label>
                        <select name="activity_id" id="activity_id" class="w-full rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="">All Activities</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->id }}" {{ isset($validated) && ($validated['activity_id'] ?? '') == $activity->id ? 'selected' : '' }}>
                                    {{ $activity->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="">All Statuses</option>
                            <option value="done" {{ isset($validated) && ($validated['status'] ?? '') === 'done' ? 'selected' : '' }}>Done</option>
                            <option value="pending" {{ isset($validated) && ($validated['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-xl hover:bg-amber-700 transition-colors shadow-sm">
                            Generate
                        </button>
                    </div>
                </form>
                @if($errors->any())
                    <div class="mt-3 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Results --}}
            @isset($summary)
                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['total_logs'] }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-1">Total Logs</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-600">{{ $summary['done_count'] }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-1">Done</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-amber-600">{{ $summary['pending_count'] }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-1">Pending</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-amber-700">{{ $summary['unique_days'] }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-1">Days</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-2xl font-bold text-amber-800">{{ $summary['unique_users'] }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-1">Users</p>
                    </div>
                </div>

                {{-- Export Button --}}
                <div class="flex justify-end">
                    <form action="{{ route('reports.export') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $validated['start_date'] }}">
                        <input type="hidden" name="end_date" value="{{ $validated['end_date'] }}">
                        <input type="hidden" name="activity_id" value="{{ $validated['activity_id'] ?? '' }}">
                        <input type="hidden" name="status" value="{{ $validated['status'] ?? '' }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors shadow-sm border border-gray-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export CSV
                        </button>
                    </form>
                </div>

                {{-- Detailed Logs by Date --}}
                @foreach($logsByDate as $dateKey => $dateLogs)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-sm font-semibold text-gray-700">
                                {{ \Carbon\Carbon::parse($dateKey)->format('l, F j, Y') }}
                                <span class="text-gray-400 font-normal ml-2">{{ $dateLogs->count() }} entries</span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="px-6 py-3">Activity</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Remark</th>
                                        <th class="px-6 py-3">Updated By</th>
                                        <th class="px-6 py-3">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($dateLogs as $log)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $log->activity->title }}</td>
                                            <td class="px-6 py-3">
                                                @if($log->status === 'done')
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Done</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-gray-600 max-w-xs truncate">{{ $log->remark ?? '—' }}</td>
                                            <td class="px-6 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white text-xs font-bold">
                                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-gray-700">{{ $log->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-3 text-gray-400 whitespace-nowrap">{{ $log->created_at->format('g:i:s A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                @if($logs->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-gray-500">No logs found for the selected criteria.</p>
                    </div>
                @endif
            @endisset
        </div>
    </div>
</x-app-layout>
