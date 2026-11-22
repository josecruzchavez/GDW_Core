<?php
declare(strict_types=1);

namespace GDW\Core\Cron;

use GDW\Core\Model\ModuleVersionCatalog;

class RefreshModuleVersionCatalog
{
    public function __construct(
        private readonly ModuleVersionCatalog $moduleVersionCatalog
    ) {
    }

    public function execute(): void
    {
        $this->moduleVersionCatalog->refresh();
    }
}
