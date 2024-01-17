<?php

namespace Goldfinch\Nest\Controllers;

use SilverStripe\Control\Director;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Controller;
use Goldfinch\Nest\Models\NestedObject;

class NestedObjectController extends Controller
{
    protected $nestedObject;

    public function __construct(NestedObject $nestedObject)
    {
        $this->nestedObject = $nestedObject;

        parent::__construct();

        $this->setFailover($this->nestedObject);
    }

    public function getNestedObject()
    {
        return $this->nestedObject;
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

        if ($page && !($page instanceof NestedObjectController)) {
            return Controller::join_links(
                $page->Link($action),
                '#'. $this->element->getAnchor()
            );
        }

        $curr = Controller::curr();

        if ($curr && !($curr instanceof NestedObjectController)) {
            return Controller::join_links(
                $curr->Link($action),
                '#'. $this->element->getAnchor()
            );
        }
    }
}
