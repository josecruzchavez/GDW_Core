<?php
namespace GDW\Core\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
	const GDW_MODULE_CODE = 'gdwcore/';

	protected $objectManager;

	protected $storeManager;

	protected $moduleList;

	public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
		StoreManagerInterface $storeManager,
		ModuleListInterface $moduleList
    ) {
		parent::__construct($context);
		$this->objectManager = $objectManager;
		$this->storeManager = $storeManager;
		$this->_moduleList = $moduleList;
    }

	public function createObject($path, $arguments = [])
    {
        return $this->objectManager->create($path, $arguments);
    }

    public function getObject($path)
    {
        return $this->objectManager->get($path);
    }

	public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
	}

	public function getConfigValue($field, $storeId = null)
	{
		if($storeId == null){
			$storeId = $this->getStoreId();	
		}

		return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
	}

	public function getModuleCode(){
		return static::GDW_MODULE_CODE;
	}

	public function getVal($group, $code, $storeId = null)
	{
		if($storeId == null){
			$storeId = $this->getStoreId();	
		}

		return $this->getConfigValue($this->getModuleCode().$group.'/'. $code, $storeId);
	}

 	public function getDirectVal($code, $storeId = null)
    { 
		if($storeId == null){
			$storeId = $this->getStoreId();	
		}
        return $this->getConfigValue($this->getModuleCode().$code, $storeId);
    }

	public function getAdminUrl()
	{
		return $this->createObject('Magento\Backend\Helper\Data')->getHomePageUrl();
	}

	public function getVersion($code = null)
    {	
        return $code == null ? 'N/A' : $this->_moduleList->getOne($code)['setup_version'];
    }

	public function getGlobalInfoModule()
	{
		$html  = '<strong>Autor: </strong> José Cruz Chávez <br/>';
		$html .= '<strong>Donaciones: </strong> <a href="https://www.paypal.me/gestiondigitalweb" target="_blank" rel="nofollow">Mediante Paypal</a><br/>';
		$html .= '<strong>Sitio Web: </strong> <a href="https://gestiondigitalweb.com" target="_blank">https://gestiondigitalweb.com</a><br/>';
		$html .= '<strong>Dudas o requerimientos: </strong> <a href="mailto:soporte@gestiondigitalweb.com" target="_blank">soporte@gestiondigitalweb.com</a><br/>';
		return $html; 		
	}

	public function getInfo($name,$version,$desc)
	{
		$globalInfo = $this->getGlobalInfoModule();
		$vModule = $this->getVersion($version);
		$html = 
<<<HTML
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

	public function getInfoFull($name, $version, $descFull = null, $linkconfig = null)
	{
		$vModule = $this->getVersion($version);
		$html  = '<table style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:15px; width:100%;"><tr>';
        $html .= '<td style="padding:8px; width:33%;"><strong>Nombre: </strong>'.$name.'</td>';
        $html .= '<td style="padding:8px; width:33%;"><strong>Versión: </strong>'.$vModule.'</td>';
		  if($linkconfig != null){
			$fulllink = $this->getAdminUrl().$linkconfig;
			$html .= '<td style="padding:8px; width:33%;"><strong><a href="'.$fulllink.'">Configurar</a></strong></td>';
		  }else{
			$html .= '<td style="padding:8px; width:33%;">&nbsp;</td>';
		  }
    	$html .= '</tr></table>';
		  if($descFull != null){
			$html .= '<div style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:8px;">';
			$html .= $descFull;
			$html .= '</div>';
		  }
	  return $html;
	}

	public function getInfoSimple($name, $version, $descFull = null, $linkconfig = null)
	{
		return $this->getInfoFull($name, $version, $descFull, $linkconfig);
	}
}