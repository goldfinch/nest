<?php

namespace Goldfinch\Nest\Controllers;

use Goldfinch\Nest\Pages\Nest;
use SilverStripe\View\SSViewer;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\CMS\Controllers\ContentController;

class NestController extends ContentController
{
    private static $url_handlers = [
        '$@' => 'index',
    ];

    private static $allowed_actions = [];

    private $nested_tree = [];

    private $nestObject;

    public function getNestedObjectModel($request = null)
    {
        $params = $request->latestParams();

        if (count($params)) {
            // 1)
            if ($this->NestedObject) {
                $start_nest = $this->NestedObject;
                $this->nested_tree[] = $start_nest;

                do {
                    $start_nest = isset($start_nest::$nest_up)
                        ? $start_nest::$nest_up
                        : null;
                    if ($start_nest) {
                        $this->nested_tree[] = $start_nest;
                    }
                } while ($start_nest);

                // $paramList = [
                //   'params' => [],
                //   'nested_tree' => [],
                // ];

                // foreach($params as $ky => $p)
                // {
                //     $paramList['params'][] =
                //     $paramList['nested_tree'][] =
                // }

                // remove excess tree up objects
                if (count($params) < count($this->nested_tree)) {
                    $nested_tree = [];

                    for ($i = 0; $i < count($params); $i++) {
                        $nested_tree[] = $this->nested_tree[$i];
                    }

                    $this->nested_tree = $nested_tree;
                }

                // 2)
                if (count($params) === count($this->nested_tree)) {
                    $nest = last($this->nested_tree)
                        ::get()
                        ->filter('URLSegment', last($params))
                        ->first();

                    if ($nest && $nest->NestLink()) {
                        $nested_tree = explode(
                            '/',
                            ltrim($nest->NestLink(), '/'),
                        );
                        $current_tree = array_values($params);
                        array_unshift($current_tree, $this->URLSegment);

                        $page = $this;

                        while ($page = $page->getParent()) {
                            array_unshift($current_tree, $page->URLSegment);
                        }

                        if ($nested_tree === $current_tree) {
                            $this->nestObject = $nest;

                            return $nest;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function index(HTTPRequest $request)
    {
        $params = $request->latestParams();

        if (count($params)) {
            $nest = $this->getNestedObjectModel($request);

            if ($nest) {
                if ($nest->MetaTitle) {
                    $this->MetaTitle = $nest->MetaTitle;
                } elseif ($nest->Title) {
                    $this->MetaTitle = $nest->Title . ' - ' . $this->Title;
                }

                if ($nest->MetaDescription) {
                    $this->MetaDescription = $nest->MetaDescription;
                }

                $nest = $this->nestExtend($nest);

                if (SSViewer::chooseTemplate($nest->ClassName)) {

                    return $this->customise([
                        'IsObject' => true,
                        'Layout' => $nest->renderWith($nest->ClassName),
                    ])->renderWith('Page');
                } else {

                    return $this->customise([
                        'Layout' => $nest->renderWith(
                            'Goldfinch\Nest\Models\NestedObject',
                        ),
                    ])->renderWith('Page');
                }
            }

            return $this->httpError(404);
        }

        if ($this->NestedPseudo) {
            if ($this->NestedRedirectPageID) {
                return $this->redirect(
                    $this->NestedRedirectPage()->Link(),
                    301,
                );
            } else {
                if (is_subclass_of($this->Parent(), Nest::class)) {
                    return $this->redirect(
                        $this->Parent()->Link(),
                        301,
                    );
                } else {
                    return $this->httpError(404);
                }
            }
        }

        return $this->renderWith('Page', [
            'Layout' => $this->renderWith($this->ClassName),
        ]);
    }

    public function NestedList()
    {
        if ($this->NestedObject) {
            return $this->NestedObject::get();
        }

        return null;
    }

    // this method is used to extend from child controller
    public function nestExtend($nest)
    {
        return $nest;
    }

    public function CMSEditLink()
    {
        return $this->nestObject
            ? $this->nestObject->CMSEditLink()
            : parent::CMSEditLink();
        // return $this->getNestedObject()->CMSEditLink();
    }
}
