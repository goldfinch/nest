<?php

namespace App\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\CMS\Controllers\ContentController;

class PDOPageController extends ContentController
{
    private static $url_handlers = [
        '$@' => 'index',
    ];

    private static $allowed_actions = [];

    private $pdo_tree = [];

    public function index(HTTPRequest $request)
    {
        $params = $request->latestParams();

        if (count($params))
        {
            // 1)
            if ($this->PageDataObject)
            {
                $start_pdo = $this->PageDataObject;
                $this->pdo_tree[] = $start_pdo;

                do
                {
                    $start_pdo = isset($start_pdo::$pdo_up) ? $start_pdo::$pdo_up : null;
                    if ($start_pdo) {
                      $this->pdo_tree[] = $start_pdo;
                    }
                }
                while ($start_pdo);

                // $paramList = [
                //   'params' => [],
                //   'pdo_tree' => [],
                // ];

                // foreach($params as $ky => $p)
                // {
                //     $paramList['params'][] =
                //     $paramList['pdo_tree'][] =
                // }

                // remove excess tree up objects
                if (count($params) < count($this->pdo_tree))
                {
                    $pdo_tree = [];

                    for($i = 0; $i < count($params); $i++)
                    {
                        $pdo_tree[] = $this->pdo_tree[$i];
                    }

                    $this->pdo_tree = $pdo_tree;
                }

                // 2)
                if (count($params) === count($this->pdo_tree))
                {
                    // dd($params, $this->URLSegment, $this->pdo_tree);

                    $pdo = last($this->pdo_tree)::get()->filter('URLSegment', last($params))->first();

                    if ($pdo && $pdo->PDOLink())
                    {
                        // dd($pdo->ClassName, $pdo->isOnDraft());
                        $pdo_tree = explode('/', ltrim($pdo->PDOLink(), '/'));
                        $current_tree = array_values($params);
                        array_unshift($current_tree, $this->URLSegment);

                        if ($pdo_tree === $current_tree)
                        {
                            // return $pdo->renderWith(['BlogPost', 'Page']);
                            // return $this->customise($pdo)->renderWith(['App\Models\BlogPost', 'Layout']);
                            // return $this->renderWith('PDO', 'Page');
                            // return $pdo->renderWith(['Page']);
                            return $this->customise([
                              'Layout' => $pdo->renderWith('App\Models\BlogPost')
                            ])->renderWith('Page');
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
}
