<?php
declare(strict_types=1);

namespace GDW\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\App\ProductMetadataInterface;

class Internal extends AbstractHelper
{
    const GDW_MODULE_CODE = 'gdwcore/';

    /** @var BackendHelper */
    private $backendHelper;

    /** @var ModuleListInterface */
    private $moduleList;

    /** @var ProductMetadataInterface */
    private $productMetadata;

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);
        $this->backendHelper = $backendHelper;
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
    }

    public function getModuleCode(): string
    {
        return static::GDW_MODULE_CODE;
    }

    public function getAdminUrl(): string
    {
        return $this->backendHelper->getHomePageUrl();
    }

    public function getVersion(?string $code = null): string
    {
        if (!$code) {
            return 'N/A';
        }

        $info = $this->moduleList->getOne($code);
        return $info['setup_version'] ?? 'N/A';
    }

    public function versionMagentoCompare($ver, $operator = '>='): bool
    {
        $version = $this->productMetadata->getVersion();
        return version_compare($version, $ver, $operator);
    }

    public function getGlobalInfoModule(): string
    {
        $html  = '<strong>Autor: </strong> <a href="https://www.linkedin.com/in/jose-cruz-chavez" target="_blank" rel="nofollow">José Cruz Chávez</a><br/>';
        $html .= '<strong>Donaciones: </strong> <a href="https://www.paypal.me/gestiondigitalweb" target="_blank" rel="nofollow">Mediante Paypal</a><br/>';
        $html .= '<strong>Sitio Web: </strong> <a href="https://gdw.mx" target="_blank">https://gdw.mx</a><br/>';
        $html .= '<strong>Dudas o requerimientos: </strong> <a href="mailto:jcruz@gdw.mx" target="_blank">jcruz@gdw.mx</a><br/>';
        return $html;
    }

    public function getInfo(string $name, string $version, string $desc): string
    {
        $globalInfo = $this->getGlobalInfoModule();
        $vModule = $this->getVersion($version);

        $html = <<<HTML
<table style="background:#f8f8f8; border:1px solid #ccc; min-height:100px; margin:5px 0; padding:15px; width:100%;"><tr>
<td valign="top" style="width:40%; padding:8px;">
    <p><strong>Información:</strong></p>
    <p>
        <strong>Nombre:</strong> $name <br/>
        <strong>Versión:</strong> $vModule <br/>
        <strong>Descripción:</strong> $desc <br/>
    </p>
</td>
<td valign="top" style="width:45%; padding:8px;"><p><strong>Extensiones y tiendas online Magento</strong></p><p>$globalInfo</p></td>
</tr></table>
HTML;

        return $html;
    }

    public function getInfoFull(string $name, string $version, ?string $descFull = null, ?string $linkconfig = null, ?string $secc = null): string
    {
        $vModule = $this->getVersion($version);

        $html  = '<table style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:15px; width:100%;"><tr>';
        $html .= '<td style="padding:8px; width:33%;"><strong>Nombre: </strong>' . $name . '</td>';
        $html .= '<td style="padding:8px; width:33%;"><strong>Versión: </strong>' . $vModule . '</td>';

        if ($linkconfig !== null) {
            $fulllink = $this->backendHelper->getUrl($linkconfig);
            if ($secc !== null) {
                $fulllink .= $secc;
            }
            $html .= '<td style="padding:8px; width:33%;"><strong><a href="' . $fulllink . '">Configurar</a></strong></td>';
        } else {
            $html .= '<td style="padding:8px; width:33%;">&nbsp;</td>';
        }

        $html .= '</tr></table>';

        if ($descFull !== null) {
            $html .= '<div style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:8px;">';
            $html .= $descFull;
            $html .= '</div>';
        }

        return $html;
    }

    public function getInfoSimple(string $name, string $version, ?string $descFull = null, ?string $linkconfig = null): string
    {
        return $this->getInfoFull($name, $version, $descFull, $linkconfig);
    }

    public function getCommandInfoFull(string $command, ?string $descFull = null): string
    {
        $html  = '<table style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:15px; width:100%; border-top:10px solid white;"><tr>';
        $html .= '<td style="padding:8px; width:66%;"><strong>Command: </strong>' . $command . '</td>';
        $html .= '<td style="padding:8px; width:33%;">&nbsp;</td>';
        $html .= '</tr></table>';

        if ($descFull !== null) {
            $html .= '<div style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:8px;">';
            $html .= $descFull;
            $html .= '</div>';
        }

        return $html;
    }
}