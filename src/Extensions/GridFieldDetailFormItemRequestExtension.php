<?php

namespace App\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\LiteralField;

class GridFieldDetailFormItemRequestExtension extends Extension
{
    public function updateFormActions($actions)
    {
        // $actions->fieldByName('RightGroup')->addExtraClass('TESTO');

        $actions->insertBefore('ActionMenus', LiteralField::create('test', '<a target="_blank" href="'.$this->owner->getRecord()->Link().'?stage=Stage" class="btn btn-primary bi bi-binoculars-fill me-1" title="Review page on the website"></a>'));
    }
}
