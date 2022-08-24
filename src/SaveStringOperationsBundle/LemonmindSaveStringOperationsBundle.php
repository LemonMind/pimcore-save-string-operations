<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class LemonmindSaveStringOperationsBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindsavestringreplace/js/pimcore/element/helpers/gridColumnConfigExtended.js',
            '/bundles/lemonmindsavestringreplace/js/pimcore/makeWindow.js',
            '/bundles/lemonmindsavestringreplace/js/pimcore/concatWindow.js',
            '/bundles/lemonmindsavestringreplace/js/pimcore/startup.js',
        ];
    }
}
