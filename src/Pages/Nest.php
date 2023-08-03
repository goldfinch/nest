<?php

namespace Goldfinch\Nest\Pages;

use Illuminate\Support\Str;
use SilverStripe\Core\ClassInfo;
use Goldfinch\Nest\Controllers\NestController;
use SilverStripe\Forms\DropdownField;
use Goldfinch\Nest\Models\NestedObject;
use SilverStripe\CMS\Model\SiteTree;

class Nest extends SiteTree
{
    private static $allowed_children = [];

    private static $controller_name = NestController::class;

    private static $db = [
        'NestedObject' => 'Varchar'
    ];

    private static $indexes = [];

    private static $owned_by = [];

    private static $casting = [];

    private static $defaults = [];

    private static $table_name = 'Nest';

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

        $classes = ClassInfo::getValidSubClasses(NestedObject::class);
        // $list = array_fill_keys($classes, '');

        $list[''] = '-';

        foreach($classes as $key => $class)
        {
            if ($class::$nest_down == Nest::class)
            {
                $list[$class] = Str::of(class_basename($class))->headline();
            }
        }

        $fields->addFieldsToTab(
          'Root.Advanced',
          [
              DropdownField::create(
                'NestedObject',
                'Nested object',
                $list,
                $this->NestedObject,
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

    public function NestedList()
    {
        if ($this->NestedObject)
        {
            $this->NestedObject::get();
        }

        return null;
    }
}
