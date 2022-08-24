<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class LemonmindSaveStringOperationsBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindsavestringoperations/js/pimcore/element/helpers/gridColumnConfigExtended.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/makeWindow.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/concatWindow.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/startup.js',
        ];
    }
}
