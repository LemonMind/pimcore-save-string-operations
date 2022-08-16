<?php

declare(strict_types=1);

namespace Lemonmind\GridOptionsSaveBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class LemonmindGridOptionsSaveBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindgridoptionssave/js/pimcore/startup.js',
        ];
    }
}
