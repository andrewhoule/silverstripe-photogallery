<?php

namespace AndrewHoule\PhotoGallery\Extensions;

use SilverStripe\ORM\DataExtension;

class Sortable extends DataExtension
{

    private static $db = [
        'SortID' => 'Int'
    ];

    private static $default_sort = 'SortID ASC';

}
