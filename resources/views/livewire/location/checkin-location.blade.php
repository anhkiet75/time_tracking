<div class="sm:w-full lg:w-[500px] mx-auto mt-10">
    <form wire:submit="create">
        {{ $this->form }}

        <button type="submit">
            Submit
        </button>
    </form>
    <x-filament-actions::modals />
</div>