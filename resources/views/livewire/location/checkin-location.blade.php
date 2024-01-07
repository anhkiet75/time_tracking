<div class="py-12 w-full">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="">
                    <form wire:submit="create">
                        {{ $this->form }}
                    </form>
                    <x-filament-actions::modals />
                </div>
            </div>
        </div>
    </div>
</div>