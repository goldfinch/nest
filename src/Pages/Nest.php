<?php

namespace Goldfinch\Nest\Pages;

use Illuminate\Support\Str;
use Goldfinch\Nest\Pages\Nest;
use Composer\InstalledVersions;
use SilverStripe\Core\ClassInfo;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use Goldfinch\Nest\Models\NestedObject;
use SilverStripe\Forms\TreeDropdownField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use Goldfinch\Nest\Controllers\NestController;

class Nest extends SiteTree
{
    private static $controller_name = NestController::class;

    private static $table_name = 'Nest';

    private static $default_sort = "\"Sort\"";

    private static $allowed_children = [Nest::class];

    private static $db = [
        'NestedObject' => 'Varchar',
        'NestedPseudo' => 'Boolean',
    ];

    private static $has_one = [
        'NestedRedirectPage' => SiteTree::class,
    ];

    private static $icon = null;

    private static $icon_class = 'font-icon-p-alt';

    private static $base_description = 'Generic content page';

    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();

        $classes = ClassInfo::getValidSubClasses(NestedObject::class);
        // $list = array_fill_keys($classes, '');

        $list[''] = '-';

        foreach ($classes as $key => $class) {
            if (
                $class::$nest_down &&
                ($class::$nest_down == Nest::class ||
                    get_parent_class($class::$nest_down) == Nest::class)
            ) {
                $list[$class] = Str::of(class_basename($class))->headline();
            }
        }

        $tabName = InstalledVersions::isInstalled('goldfinch/basement')
            ? 'Root.Advanced'
            : 'Root.Settings';

        $fields->addFieldsToTab($tabName, [
            DropdownField::create(
                'NestedObject',
                'Nested object',
                $list,
                $this->NestedObject,
            ),

            CheckboxField::create(
                'NestedPseudo',
                'Nested Pseudo page',
            )->setDescription(
                'Makes this page pseudo, that is not accessable and returns 404. The sub nested objects will not be affected.',
            ),

            Wrapper::create(
                TreeDropdownField::create(
                    'NestedRedirectPageID',
                    'Redirect to',
                    SiteTree::class,
                ),
            )
                ->displayIf('NestedPseudo')
                ->isChecked()
                ->end(),
        ]);

        return $fields;
    }

    public function getNestedList()
    {
        if ($this->NestedObject) {
            return $this->NestedObject::get();
        }

        return null;
    }
}
