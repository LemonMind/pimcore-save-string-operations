<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class LemonmindSaveStringOperationsBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;
    use PackageVersionTrait;

    protected function getComposerPackageName(): string
    {
        return 'lemonmind/pimcore-save-string-operations';
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/lemonmindsavestringoperations/js/pimcore/element/helpers/gridColumnConfigExtended.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/formHandler.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/replaceWindow.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/concatWindow.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/convertWindow.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/numericWindow.js',
            '/bundles/lemonmindsavestringoperations/js/pimcore/startup.js',
        ];
    }
}
