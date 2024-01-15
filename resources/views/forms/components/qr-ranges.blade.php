@php
use Filament\Support\Facades\FilamentView;

$hasInlineLabel = $hasInlineLabel();
$id = $getId();
$isDisabled = $isDisabled();
$isReorderable = false;
$statePath = $getStatePath();
$suffixActions = $getSuffixActions();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-slot name="label">
        {{ $getLabel() }}
    </x-slot>

    <x-filament::input.wrapper @class(['h-9 h-full']) :suffix-actions="$suffixActions" :disabled="$isDisabled" :valid="! $errors->has($statePath)" :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-tags-input'])
        ">
        <div @if (FilamentView::hasSpaMode()) ax-load="visible" @else ax-load @endif ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms') }}" x-data="tagsInputFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                        splitKeys: @js($getSplitKeys()),
                    })" x-ignore {{ $getExtraAlpineAttributeBag() }}>
            <!-- <x-filament::input autocomplete="off" :autofocus="$isAutofocused()" :disabled="$isDisabled" :id="$id" :list="$id . '-suggestions'" :placeholder="$getPlaceholder()" type="text" x-bind="input" :attributes="\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())" /> -->

            <datalist id="{{ $id }}-suggestions">
                @foreach ($getSuggestions() as $suggestion)
                <template x-bind:key="@js($suggestion)" x-if="! state.includes(@js($suggestion))">
                    <option value="{{ $suggestion }}" />
                </template>
                @endforeach
            </datalist>

            <div @class([ '[&_.fi-badge-delete-button]:hidden'=> $isDisabled,
                ])
                >
                <div wire:ignore>
                    <template x-cloak x-if="state?.length">
                        <div @class([ 'flex h-full w-full p-1 flex-wrap gap-1.5 items-center' , 'border-t border-t-gray-200 dark:border-t-white/10' , ])>
                            <template x-for="(tag, index) in state" x-bind:key="`${tag}-${index}`" class="hidden">
                                <x-filament::badge :x-bind:x-sortable-item="null" @style([ '--c-400: var(--success-400)' ])>
                                    {{ $getTagPrefix() }}

                                    <span x-text="tag" class="select-none text-start"></span>

                                    {{ $getTagSuffix() }}
                                </x-filament::badge>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>