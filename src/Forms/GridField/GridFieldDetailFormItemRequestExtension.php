<?php

namespace Goldfinch\Nest\Forms\GridField;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\LiteralField;
use Goldfinch\Enchantment\Helpers\BuildHelper;

class GridFieldDetailFormItemRequestExtension extends Extension
{
    public function updateFormActions($actions)
    {
        // $actions->fieldByName('RightGroup')->addExtraClass('TESTO');
        if ($this->owner->getRecord()->hasMethod('Link')) {
            $icon = 'font-icon-eye';

            if (class_exists(BuildHelper::class)) {
                $icon = 'bi bi-binoculars-fill';
            }

            $actions->insertBefore(
                'ActionMenus',
                LiteralField::create(
                    'test',
                    '<a target="_blank" href="' .
                        $this->owner->getRecord()->Link() .
                        '?stage=Stage" class="btn btn-primary ' .
                        $icon .
                        ' me-1" title="Review page on the website"></a>',
                ),
            );
        }
    }
}
