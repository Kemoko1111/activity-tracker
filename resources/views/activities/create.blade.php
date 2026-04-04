<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Activity</h2>
            <a href="{{ route('activities.index') }}" class="text-sm text-amber-600 hover:text-amber-800 font-medium">← Back to Activities</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <form action="{{ route('activities.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Activity Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="w-full rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="e.g., Daily SMS count vs logs">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500"
                                  placeholder="Optional details about this activity">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input type="text" name="category" id="category" value="{{ old('category') }}"
                               list="category-list"
                               class="w-full rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="e.g., Monitoring, Maintenance, Support">
                        <datalist id="category-list">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active Toggle --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active (will appear in daily tracking)</label>
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('activities.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-colors shadow-sm">
                            Create Activity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
