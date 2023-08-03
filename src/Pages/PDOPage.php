<?php

namespace App\Pages;

use Page;
use Illuminate\Support\Str;
use SilverStripe\Core\ClassInfo;
use App\Controllers\PDOPageController;
use SilverStripe\Forms\DropdownField;
use Goldfinch\Basement\Models\PageDataObject;

class PDOPage extends Page
{
    private static $allowed_children = [];

    private static $controller_name = PDOPageController::class;

    private static $db = [
        'PageDataObject' => 'Varchar'
    ];

    private static $indexes = [];

    private static $owned_by = [];

    private static $casting = [];

    private static $defaults = [];

    private static $table_name = 'PDOPage';

    private static $default_sort = "\"Sort\"";

    private static $icon = null;

    private static $icon_class = 'font-icon-page';

    private static $searchable_fields = [];

    private static $field_labels = [];

    private static $description = null;

    private static $base_description = 'Generic content page';

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
    }

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
    }

    public function validate()
    {
        $result = parent::validate();

        return $result;
    }

    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();

        $classes = ClassInfo::getValidSubClasses(PageDataObject::class);
        // $list = array_fill_keys($classes, '');

        $list[''] = '-';

        foreach($classes as $key => $class)
        {
            if ($class::$pdo_down == PDOPage::class)
            {
                $list[$class] = Str::of(class_basename($class))->headline();
            }
        }

        $fields->addFieldsToTab(
          'Root.Advanced',
          [
              DropdownField::create(
                'PageDataObject',
                'Page Data Object',
                $list,
                $this->PageDataObject,
              )
          ]
        );

        return $fields;
    }

    public function SchemaData()
    {
        // Spatie\SchemaOrg\Schema
    }

    public function OpenGraph()
    {
        // Astrotomic\OpenGraph\OpenGraph
    }

    public function PDOList()
    {
        if ($this->PageDataObject)
        {
            $this->PageDataObject::get();
        }

        return null;
    }
}
