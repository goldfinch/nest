<?php

namespace Goldfinch\Nest\Forms\GridField;

use SilverStripe\View\SSViewer;
use SilverStripe\View\ArrayData;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\AbstractGridFieldComponent;
use SilverStripe\Forms\GridField\GridField_ActionMenuLink;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;

/**
 * A button that allows a user to view readonly details of a record. This is
 * disabled by default and intended for use in readonly {@link GridField}
 * instances.
 */
class GridFieldViewNestButton extends AbstractGridFieldComponent implements GridField_ColumnProvider, GridField_ActionMenuLink
{
    /**
     * @inheritdoc
     */
    public function getTitle($gridField, $record, $columnName)
    {
        return _t(__CLASS__ . '.VIEW', "View");
    }

    /**
     * @inheritdoc
     */
    public function getGroup($gridField, $record, $columnName)
    {
        return GridField_ActionMenuItem::DEFAULT_GROUP;
    }

    /**
     * @inheritdoc
     */
    public function getExtraData($gridField, $record, $columnName)
    {
        return [
            "classNames" => "bi bi-binoculars-fill"
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUrl($gridField, $record, $columnName)
    {
        $link = Controller::join_links($gridField->Link('item'), $record->ID, 'view');
        return $gridField->addAllStateToUrl($link);
    }

    public function augmentColumns($field, &$columns)
    {
        if (!in_array('Actions', $columns ?? [])) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnsHandled($field)
    {
        return ['Actions'];
    }

    public function getColumnContent($field, $record, $col)
    {
        if (!$record->canView()) {
            return null;
        }
        // $data = new ArrayData([
        //     'Link' => $this->getURL($field, $record, $col),
        // ]);
        // $template = SSViewer::get_templates_by_class($this, '', __CLASS__);
        // return $data->renderWith($template);
        return '<a title="View on the website" target="_blank" class="grid-field__icon-action bi bi-binoculars-fill action action-detail view-link action-menu--handled" href="javascript:window.open(\''.$record->Link().'\',\'_blank\')">
    <span class="sr-only">View</span>
</a>
';
    }

    public function getColumnAttributes($field, $record, $col)
    {
        return ['class' => 'grid-field__col-compact'];
    }

    public function getColumnMetadata($gridField, $col)
    {
        return ['title' => null];
    }
}
