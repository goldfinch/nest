<?php

namespace Goldfinch\Nest\Forms;

use Goldfinch\Nest\Models\NestedObject;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;

class NestedObjectURLSegmentField extends SiteTreeURLSegmentField
{
    protected $NestedObject = null;

    public function getPage()
    {
        return $this->getNestedObject() ?? NestedObject::singleton();
    }

    public function setNestedObject($object)
    {
        $this->NestedObject = $object;
        return $this;
    }

    public function getNestedObject()
    {
        return $this->NestedObject;
    }
}
