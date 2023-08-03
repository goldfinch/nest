<?php

use Goldfinch\Basement\Models\PageDataObject;

use Wilr\GoogleSitemaps\GoogleSitemap;

GoogleSitemap::register_dataobject(PageDataObject::class); // , 'daily');
