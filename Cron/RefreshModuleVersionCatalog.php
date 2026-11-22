<?php
declare(strict_types=1);

namespace GDW\Core\Cron;

use GDW\Core\Model\ModuleVersionCatalog;

class RefreshModuleVersionCatalog
{
    private $moduleVersionCatalog;

    public function __construct(
        ModuleVersionCatalog $moduleVersionCatalog
    ) {
        $this->moduleVersionCatalog = $moduleVersionCatalog;
    }

    public function execute(): void
    {
        $this->moduleVersionCatalog->refresh();
    }
}
