<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Contracts\HasAffixActions;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TagsInput;

class QRRanges extends TagsInput implements HasAffixActions
{
    protected string $view = 'forms.components.qr-ranges';
    use HasAffixes;
}
