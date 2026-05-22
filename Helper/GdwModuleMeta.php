<?php
declare(strict_types=1);

namespace GDW\Core\Helper;

final class GdwModuleMeta
{
    /** @return array{desc:string, config_path:string, config_anchor:string, repo_url:string, docs_url:string} */
    public static function getMeta(): array
    {
        return [
            'desc' => 'Configuracion base para modulos de Magento 2 creados por GDW.',
            'config_path' => '',
            'config_anchor' => '',
            'repo_url' => 'https://github.com/josecruzchavez/GDW_Core',
            'docs_url' => 'https://docs.gdw.mx/modulos/gdw_core',
        ];
    }
}
