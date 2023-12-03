<?php

namespace App\Http\Controllers;

use App\Models\SiteMap;

class SiteMapController
{
       public function makeSiteMap(): void
       {
           new SiteMap();
       }
}
