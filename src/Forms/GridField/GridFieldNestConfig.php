<?php

namespace Goldfinch\Nest\Forms\GridField;

use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;

class GridFieldNestConfig extends GridFieldConfig
{
    public function __construct($itemsPerPage = null)
    {
        parent::__construct($itemsPerPage);

        $this->addComponents(
            // GridFieldFilterHeader::create(),
            GridFieldToolbarHeader::create(),
            GridFieldSortableHeader::create(),
            GridFieldDataColumns::create(),
            GridFieldDetailForm::create(),
            GridFieldDeleteAction::create(),
            GridFieldEditButton::create(),
            GridFieldViewNestButton::create(),
            // GridField_ActionMenu::create(),
        );

        $dataColumns = $this->getComponentByType(GridFieldDataColumns::class);

        $dataColumns->setDisplayFields([
            'Title' => 'Title',
            'Link'=> 'URL',
            'LastEdited' => 'Changed'
        ]);

        // $this->addComponent($dataColumns);

        $this->extend('updateConfig');
    }
}
