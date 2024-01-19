@php
    use Filament\Support\Facades\FilamentView;
    use App\Models\BusinessQRCodeRange;
    use App\Models\Location;
    $color = $getColor() ?? 'primary';
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isReorderable = false;
    $statePath = $getStatePath();
    $suffixActions = $getSuffixActions();
    $ranges = ($getRecord() != null) ? BusinessQRCodeRange::where('business_id', $getRecord()->id)->get() : [];
    $used_ranges = [];

    foreach ($ranges as $item) {
        $used_ranges["{$item->start_range}-{$item->end_range}"] =
        \App\Filament\Helper\BusinessHelper::getUsedQRCode($item->start_range, $item->end_range);
    }
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-slot name="label">
        {{ $getLabel() }}
    </x-slot>

    <x-filament::input.wrapper @class(['h-9']) :suffix-actions="$suffixActions" :disabled="$isDisabled"
                               :valid="!$errors->has($statePath)"
                               :attributes="\Filament\Support\prepare_inherited_attributes($attributes)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-fo-tags-input'])">
        <div @if (FilamentView::hasSpaMode()) ax-load="visible" @else ax-load @endif
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('qr-ranges') }}"
             x-data="qrRangesInputFormComponent({
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                splitKeys: @js($getSplitKeys()),
            })" x-ignore {{ $getExtraAlpineAttributeBag() }}>

            <datalist id="{{ $id }}-suggestions">
                @foreach ($getSuggestions() as $suggestion)
                    <template x-bind:key="@js($suggestion)" x-if="! state.includes(@js($suggestion))">
                        <option value="{{ $suggestion }}"/>
                    </template>
                @endforeach
            </datalist>

            <div @class(['[&_.fi-badge-delete-button]:hidden' => $isDisabled])>
                <div wire:ignore>
                    <template x-cloak x-if="state?.length">
                        <div @class([
                            'flex h-full w-full p-1 flex-wrap gap-1.5 items-center',
                            'border-t border-t-gray-200 dark:border-t-white/10',
                        ]) x-data="{ ranges: @js($used_ranges) }">
                            <div x-init="console.log(ranges)"></div>

                            <template x-for="(tag, index) in state" x-bind:key="`${tag}-${index}`" class="hidden">
                                <x-filament::badge
                                    :x-bind:x-sortable-item="null" @style(['--c-400: var(--success-400)'])>
                                    {{ $getTagPrefix() }}

                                    <span x-text="tag" class="select-none text-start"></span>
                                    <x-slot name="deleteButton" x-on:click="deleteTag(tag)">
                                    </x-slot>
                                </x-filament::badge>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
