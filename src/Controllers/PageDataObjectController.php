<?php

namespace Goldfinch\Basement\Controllers;

use Goldfinch\Basement\Models\PageDataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\View\Requirements;

class PageDataObjectController extends Controller
{
    protected $pageDataObject;

    public function __construct(PageDataObject $pageDataObject)
    {
        $this->pageDataObject = $pageDataObject;

        parent::__construct();

        $this->setFailover($this->pageDataObject);
    }

    public function getPageDataObject()
    {
        return $this->pageDataObject;
    }

    public function forTemplate()
    {
        $defaultStyles = $this->config()->get('default_styles');
        $this->extend('updateForTemplateDefaultStyles', $defaultStyles);

        if ($this->config()->get('include_default_styles') && !empty($defaultStyles)) {
            foreach ($defaultStyles as $stylePath) {
                Requirements::css($stylePath);
            }
        }

        $template = 'Basement\\Elemental\\' . $this->element->config()->get('controller_template');
        $this->extend('updateForTemplateTemplate', $template);

        return $this->renderWith([
            'type' => 'Layout',
            $template
        ]);
    }

    public function Link($action = null)
    {
        $page = Director::get_current_page();

        if ($page && !($page instanceof PageDataObjectController)) {
            return Controller::join_links(
                $page->Link($action),
                '#'. $this->element->getAnchor()
            );
        }

        $curr = Controller::curr();

        if ($curr && !($curr instanceof PageDataObjectController)) {
            return Controller::join_links(
                $curr->Link($action),
                '#'. $this->element->getAnchor()
            );
        }
    }
}
