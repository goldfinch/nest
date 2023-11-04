<?php

namespace Goldfinch\Nest\Models;

use SilverStripe\Forms\Tab;
use Goldfinch\Nest\Pages\Nest;
use SilverStripe\Forms\TabSet;
use gorriecoe\Link\Models\Link;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Control\Director;
use SilverStripe\ORM\RelationList;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use Goldfinch\Nest\Controllers\NestedObjectController;
use SilverStripe\VersionedAdmin\Forms\HistoryViewerField;

class NestedObject extends DataObject implements CMSPreviewable
{
    public static $nest_up = null;
    public static $nest_up_children = [];
    public static $nest_down = null;
    public static $nest_down_parents = [];

    private static $extensions = [
        Versioned::class,
    ];

    private static $singular_name = 'Nested object';

    private static $plural_name = 'Nested objects';

    private static $versioned_gridfield_extensions = true; // ? check if needed

    private static $controller_class = NestedObjectController::class;

    private static $controller_template = 'NestedObjectHolder'; // ? check if needed

    private static $table_name = 'NestedObject';

    private static $show_stage_link = true; // ? check if needed

    private static $show_live_link = true; // ? check if needed

    private static $cascade_duplicates = [];

    private static $db = [
        'URLSegment' => 'Varchar(255)',
        'Title' => 'Varchar(255)',
        'MenuTitle' => 'Varchar(100)',
        'MetaTitle' => 'Text',
        'MetaDescription' => 'Text',
        'ShowInSearch' => 'Boolean',
        'Sort' => 'Int',
        'ShowOnlyToRobots' => 'Boolean',
    ];

    private static $default_sort = 'LastEdited';

    private static $searchable_fields = [
        'ID' => [
            'field' => NumericField::class,  // ? check if needed
        ],
        'Title',
    ];

    private static $field_labels = [
        'Title' => 'Page name',
        'URLSegment' => 'URL',
        'MenuTitle' => 'Navigation title',
    ];

    private static $runCMSFieldsExtensions = true;

    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title)
        {
            $result->addError('Title is required');
        }

        return $result;
    }

    public function onBeforeDelete()
    {
        // ..

        parent::onBeforeDelete();
    }

    public function getCMSFields()
    {
        // $fields = parent::getCMSFields();

        $baseLink = Controller::join_links(
            Director::absoluteBaseURL(),
            (self::config()->get('nested_urls') && $this->ParentID ? $this->Parent()->RelativeLink(true) : null)
        );

        $urlsegment = SiteTreeURLSegmentField::create("URLSegment", $this->fieldLabel('URLSegment'))
            ->setURLPrefix($baseLink)
            ->setURLSuffix('?stage=Stage')
            ->setDefaultURL($this->generateURLSegment(_t(
                'SilverStripe\\CMS\\Controllers\\CMSMain.NEWPAGE',
                'New {pagetype}',
                ['pagetype' => $this->i18n_singular_name()]
            )));
        $helpText = (self::config()->get('nested_urls') && $this->numChildren())
            ? $this->fieldLabel('LinkChangeNote')
            : '';
        if (!URLSegmentFilter::create()->getAllowMultibyte()) {
            $helpText .= _t('SilverStripe\\CMS\\Forms\\SiteTreeURLSegmentField.HelpChars', ' Special characters are automatically converted or removed.');
        }
        $urlsegment->setHelpText($helpText);

        $fields = new FieldList(
            $rootTab = new TabSet(
                "Root",
                $tabMain = new Tab(
                    'Main',
                ),
                new TabSet('Settings',
                    new TabSet('General',
                      new Tab('General_Inner',
                        new TextField("Title", $this->fieldLabel('Title')),
                        $urlsegment,
                        new TextField("MenuTitle", $this->fieldLabel('MenuTitle')),
                      ),
                    ),
                    new TabSet('SEO',
                      new Tab('SEO_Inner',
                        TextField::create('MetaTitle', 'Meta title'),

                        TextareaField::create('MetaDescription', 'Meta description'),

                        // $googleSitemapTab,

                        CheckboxField::create('ShowInSearch', 'Show in search'),

                        CheckboxField::create('ShowOnlyToRobots', 'Show only to robots'),

                        Wrapper::create(

                          LinkField::create('ShowOnlyToRobots_BackLink', 'Back link for users', $this->owner),

                        )->displayIf('ShowOnlyToRobots')->isChecked()->end(),
                      )
                    ),
                )
            )
        );

        $tabMain->setTitle('Content');

        if ($this->ObsoleteClassName) {
            $obsoleteWarning = _t(
                'SilverStripe\\CMS\\Model\\SiteTree.OBSOLETECLASS',
                "This page is of obsolete type {type}. Saving will reset its type and you may lose data",
                ['type' => $this->ObsoleteClassName]
            );

            $fields->addFieldToTab(
                "Root.Main",
                LiteralField::create("ObsoleteWarningHeader", "<p class=\"alert alert-warning\">$obsoleteWarning</p>"),
                "Title"
            );
        }

        if (file_exists(PUBLIC_PATH . '/install.php')) {
            $fields->addFieldToTab('Root.Main', LiteralField::create(
                'InstallWarningHeader',
                '<div class="alert alert-warning">' . _t(
                    __CLASS__ . '.REMOVE_INSTALL_WARNING',
                    "Warning: You should remove install.php from this SilverStripe install for security reasons."
                )
                . '</div>'
            ), 'Title');
        }

        if (self::$runCMSFieldsExtensions) {
            $this->extend('updateCMSFields', $fields);
        }

        // $fields->addFieldsToTab(
        //   'Root.Settings',
        //   [
        //       TextField::create(
        //         'Component_Name',
        //         'Component name'
        //       ),

        //       CheckboxField::create(
        //         'Component_Visibility',
        //         'Visibility'
        //       ),
        //   ]
        // );

        $fields->addFieldToTab('Root.Settings', HistoryViewerField::create('NestedObjectHistory'));
        $fields->addFieldToTab('Root.History', HistoryViewerField::create('NestedObjectHistory'));

        return $fields;
    }

    // public function canView($member = null)
    // {
    //     return true; // Permission::check('ADMIN', 'any', $member);
    // }

    // public function canEdit($member = null)
    // {
    //     return Permission::check('ADMIN', 'any', $member);
    // }

    // public function canDelete($member = null)
    // {
    //     return Permission::check('ADMIN', 'any', $member);
    // }

    // public function canCreate($member = null, $context = [])
    // {
    //     return Permission::check('ADMIN', 'any', $member);
    // }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $this->URLSegment = $this->generateURLSegment($this->Title);

        // Ensure that this object has a non-conflicting URLSegment value.
        // dd($this->validURLSegment());
        $count = 2;
        while (!$this->validURLSegment()) {
            $this->URLSegment = preg_replace('/-[0-9]+$/', '', $this->URLSegment ?? '') . '-' . $count;
            $count++;
        }
    }

    public function validURLSegment()
    {
        // Check known urlsegment blacklists
        if (self::config()->get('nested_urls') && $this->ParentID) {
            // Guard against url segments for sub-pages
            $parent = $this->Parent();
            if ($controller = ModelAsController::controller_for($parent)) {
                if ($controller instanceof Controller && $controller->hasAction($this->URLSegment)) {
                    return false;
                }
            }
        } elseif (in_array(strtolower($this->URLSegment ?? ''), $this->getExcludedURLSegments() ?? [])) {
            // Guard against url segments for the base page
            // Default to '-2', onBeforeWrite takes care of further possible clashes
            return false;
        }

        // If any of the extensions return `0` consider the segment invalid
        $extensionResponses = array_filter(
            (array)$this->extend('augmentValidURLSegment'),
            function ($response) {
                return !is_null($response);
            }
        );
        if ($extensionResponses) {
            return min($extensionResponses);
        }

        // Check for clashing pages by url, id, and parent
        $source = NestedObject::get()->filter([
          'ClassName' => $this->ClassName,
          'URLSegment' => $this->URLSegment,
        ]);

        if ($this->ID) {
            $source = $source->exclude('ID', $this->ID);
        }

        if (self::config()->get('nested_urls')) {
            $source = $source->filter('ParentID', $this->ParentID ? $this->ParentID : 0);
        }

        return !$source->exists();
    }

    /**
     * Get the list of excluded root URL segments
     *
     * @return array List of lowercase urlsegments
     */
    protected function getExcludedURLSegments()
    {
        $excludes = [];

        // Build from rules
        foreach (Director::config()->get('rules') as $pattern => $rule) {
            $route = explode('/', $pattern ?? '');
            if (!empty($route) && strpos($route[0] ?? '', '$') === false) {
                $excludes[] = strtolower($route[0] ?? '');
            }
        }

        // Build from base folders
        foreach (glob(Director::publicFolder() . '/*', GLOB_ONLYDIR) as $folder) {
            $excludes[] = strtolower(basename($folder ?? ''));
        }

        $this->extend('updateExcludedURLSegments', $excludes);
        return $excludes;
    }

    public function PreviewLink($action = null)
    {
        $link = $this->AbsoluteLink($action);
        $this->extend('updatePreviewLink', $link, $action);
        return $link;
    }

    public function generateURLSegment($title)
    {
        $filter = URLSegmentFilter::create();
        $filteredTitle = $filter->filter($title);
        // dd($filteredTitle);
        // Fallback to generic page name if path is empty (= no valid, convertable characters)
        if (!$filteredTitle || $filteredTitle == '-' || $filteredTitle == '-1') {
            $filteredTitle = "page-$this->ID";
        }

        // Hook for extensions
        $this->extend('updateURLSegment', $filteredTitle, $title);

        return $filteredTitle;
    }

    public function getMimeType()
    {
        return 'text/html';
    }

    /**
     * Default cmsedit link for NestedObject
     * TODO: if no modeladmin is defined for the object, what link to use instead?
     */
    public function CMSEditLink()
    {
        // return $this->extend('CMSEditLink')[0] ?? '';
        // $admin = new ModelAdmin;
        // return Director::absoluteBaseURL() . '/' . $admin->getCMSEditLinkForManagedDataObject($this);
        return null;
    }

    public function Parent()
    {
        //
    }

    public function RelativeLink($action = null)
    {
        if ($this->ParentID && self::config()->get('nested_urls')) {
            $parent = $this->Parent();
            // If page is removed select parent from version history (for archive page view)
            if ((!$parent || !$parent->exists()) && !$this->isOnDraft()) {
                $parent = Versioned::get_latest_version(self::class, $this->ParentID);
            }
            $base = $parent ? $parent->RelativeLink($this->URLSegment) : null;
        } elseif (!$action && $this->URLSegment == RootURLController::get_homepage_link()) {
            // Unset base for root-level homepages.
            // Note: Homepages with action parameters (or $action === true)
            // need to retain their URLSegment.
            $base = null;
        } else {
            $base = $this->URLSegment;
        }

        // Legacy support: If $action === true, retain URLSegment for homepages,
        // but don't append any action
        if ($action === true) {
            $action = null;
        }

        $link = Controller::join_links($base, $action);

        $this->extend('updateRelativeLink', $link, $base, $action);

        return $link;
    }

    // public function Link($action = null)
    // {
    //     $relativeLink = $this->RelativeLink($action);
    //     $link =  Controller::join_links(Director::baseURL(), $relativeLink);
    //     $this->extend('updateLink', $link, $action, $relativeLink);
    //     return $link;
    // }

    // public function AbsoluteLink($action = null)
    // {
    //     if ($this->hasMethod('alternateAbsoluteLink')) {
    //         return $this->alternateAbsoluteLink($action);
    //     } else {
    //         return Director::absoluteURL((string) $this->Link($action));
    //     }
    // }

    public function Link($action = null)
    {
        return $this->NestLink();
    }

    public function AbsoluteLink($action = null)
    {
        return $this->NestLink(true);
    }

    public function NestLink($AbsoluteLink = false, $nestedLink = '')
    {
        // dump($this->ClassName,$this->isPublished());
        // $relativeLink = $this->RelativeLink($action);
        // $link =  Controller::join_links(Director::baseURL(), $relativeLink);
        if ($this->URLSegment)
        {
            $nestedLink = rtrim($this->URLSegment . '/' . $nestedLink, '/');
        }

        if (isset($this->ClassName::$nest_down))
        {
            $current = $this->ClassName::$nest_down;

            if ($current === Nest::class)
            {
                $nestPage = $current::get()->filter('NestedObject', $this->ClassName)->first();

                if ($nestPage && $nestPage->exists())
                {
                    if ($AbsoluteLink)
                    {
                        return $nestPage->AbsoluteLink() . '/' . $nestedLink;
                    }
                    else
                    {
                        $link = '/' . $nestPage->URLSegment . '/';

                        while($nestPage = $nestPage->getParent())
                        {
                            $link = '/' . $nestPage->URLSegment . $link;
                        }

                        return $link . $nestedLink;
                    }
                }

                return '#broken-link';
            }

            $parent = $this->$current();

            if (is_subclass_of($parent, RelationList::class))
            {
                // TODO: multiple conacoil
                // $parent->first()->map('ID', 'URLSegment')->toArray();
                // $parent->map('ID', 'URLSegment')->toArray();
                $obj = $parent->first();

                if ($obj && $obj->exists())
                {
                    return $obj->NestLink($AbsoluteLink, $nestedLink);
                }
            }
            else
            {
                // single, belongs to ..
            }
        }
        else
        {
            return '#broken-link'; // $this->URLSegment . '/' . $nestedLink;
        }

        return '';
    }

    public function isUpNested()
    {
        return isset($this->ClassName::$nest_up) && $this->ClassName::$nest_up;
    }

    public function isDownNested()
    {
        return isset($this->ClassName::$nest_down) && $this->ClassName::$nest_down;
    }

    public function upNestedClass()
    {
        return $this->isUpNested() ? $this->ClassName::$nest_up : null;
    }

    public function downNestedClass()
    {
        return $this->isDownNested() ? $this->ClassName::$nest_down : null;
    }

    public function getNestedParent()
    {
        if ($this->isDownNested())
        {
            // only Nest page as parent
            if (Nest::class === $this->downNestedClass())
            {
                return $this->downNestedClass()::get()->filter('NestedObject', $this->ClassName)->first();
            }
            else
            {
                // TODO? DataObject ...
            }
        }

        return null;
    }

    // public function NestedChildren()
    // {
    //     if (
    //       isset($this->ClassName::$nest_up) &&
    //       $this->ClassName::$nest_up &&
    //       isset($this->ClassName::$nest_up_children) &&
    //       is_array($this->ClassName::$nest_up_children) &&
    //       !empty($this->ClassName::$nest_up_children)
    //     )
    //     {
    //         $list = new ArrayList;

    //         foreach($this->ClassName::$nest_up_children as $method)
    //         {
    //             $list->push(ArrayData::create(['Relationship' => $method, 'List' => $this->$method()]));
    //         }

    //         return $list;
    //     }

    //     return null;
    // }

    // public function NestedParents()
    // {
    //     if (
    //       isset($this->ClassName::$nest_down) &&
    //       $this->ClassName::$nest_down &&
    //       isset($this->ClassName::$nest_down_parents) &&
    //       is_array($this->ClassName::$nest_down_parents) &&
    //       !empty($this->ClassName::$nest_down_parents)
    //     )
    //     {
    //         $current = $this->ClassName::$nest_down;

    //         if ($current === Nest::class)
    //         {
    //             return $current::get()->filter('NestedObject', $this->ClassName)->first();
    //         }

    //         $parent = $this->$current();

    //         if (is_subclass_of($parent, RelationList::class))
    //         {
    //             // multiple
    //             return $parent->first();
    //         }
    //         else
    //         {
    //             // single, belongs to ..
    //         }
    //     }
    // }
}
