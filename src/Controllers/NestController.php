<?php

namespace Goldfinch\Nest\Controllers;

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

    public function index(HTTPRequest $request)
    {
        $params = $request->latestParams();

        if (count($params))
        {
            // 1)
            if ($this->NestedObject)
            {
                $start_nest = $this->NestedObject;
                $this->nested_tree[] = $start_nest;

                do
                {
                    $start_nest = isset($start_nest::$nest_up) ? $start_nest::$nest_up : null;
                    if ($start_nest) {
                      $this->nested_tree[] = $start_nest;
                    }
                }
                while ($start_nest);

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
                if (count($params) < count($this->nested_tree))
                {
                    $nested_tree = [];

                    for($i = 0; $i < count($params); $i++)
                    {
                        $nested_tree[] = $this->nested_tree[$i];
                    }

                    $this->nested_tree = $nested_tree;
                }

                // 2)
                if (count($params) === count($this->nested_tree))
                {
                    // dd($params, $this->URLSegment, $this->nested_tree);

                    $nest = last($this->nested_tree)::get()->filter('URLSegment', last($params))->first();

                    if ($nest && $nest->NestLink())
                    {
                        // dd($nest->ClassName, $nest->isOnDraft());
                        $nested_tree = explode('/', ltrim($nest->NestLink(), '/'));
                        $current_tree = array_values($params);
                        array_unshift($current_tree, $this->URLSegment);

                        if ($nested_tree === $current_tree)
                        {
                            // return $nest->renderWith(['BlogPost', 'Page']);
                            // return $this->customise($nest)->renderWith(['App\Models\BlogPost', 'Layout']);
                            // return $this->renderWith('Nest', 'Page');
                            // return $nest->renderWith(['Page']);

                            // dd($nest->getViewerTemplates());

                            // dd(SSViewer::create($nest->ClassName));
                            if (SSViewer::chooseTemplate($nest->ClassName))
                            {
                                return $this->customise([
                                  'Layout' => $nest->renderWith($nest->ClassName)
                                ])->renderWith('Page');
                            }
                            else
                            {
                                return $this->customise([
                                  'Layout' => $nest->renderWith('Goldfinch\Nest\Models\NestedObject')
                                ])->renderWith('Page');
                            }
                        }
                    }
                }
            }

            return $this->httpError(404);
        }

        return $this;
    }

    protected function init()
    {
        parent::init();

        // ..
    }

    public function NestedList()
    {
        if ($this->NestedObject)
        {
            return $this->NestedObject::get();
        }

        return null;
    }
}
