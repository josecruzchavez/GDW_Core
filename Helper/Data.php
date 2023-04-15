<?php
namespace GDW\Core\Helper;

use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\ProductMetadataInterface;

class Data extends AbstractHelper
{
	const GDW_MODULE_CODE = 'gdwcore/';

	protected $objectManager;
	protected $storeManager;
	protected $moduleList;
	protected $registry;
	protected $context;

	public function __construct(
		Context $context,
		Registry $registry,
		ObjectManagerInterface $objectManager,
		StoreManagerInterface $storeManager,
		ModuleListInterface $moduleList
    ) {
		parent::__construct($context);
		$this->objectManager = $objectManager;
		$this->storeManager = $storeManager;
		$this->moduleList = $moduleList;
		$this->registry = $registry;
    }

	public function createObject($path, $arguments = [])
    {
        return $this->objectManager->create($path, $arguments);
    }

    public function getObject($path)
    {
        return $this->objectManager->get($path);
    }

	public function getStoreData()
    {
        return $this->storeManager->getStore();
    }

	public function getStoreId()
    {
        return $this->getStoreData()->getId();
	}

	public function getConfigValue($field, $storeId = null)
	{
		$IdStore = ($storeId == null ? $this->getStoreId() : $storeId);
		return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $IdStore);
	}

	public function getModuleCode()
	{
		return static::GDW_MODULE_CODE;
	}

	public function getVal($group, $code, $storeId = null)
	{
		return $this->getConfigValue($this->getModuleCode().$group.'/'. $code, $storeId);
	}

 	public function getDirectVal($code, $storeId = null)
    { 
        return $this->getConfigValue($this->getModuleCode().$code, $storeId);
    }

	public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

	public function getAdminUrl()
	{
		return $this->createObject('Magento\Backend\Helper\Data')->getHomePageUrl();
	}

	public function log($message, $file = null, $level = null)
    {  
        try {
            $name = ($file == null || $file == false ? 'gdw_core.log' : $file);

			if($this->versionMagentoCompare('2.4.3')){
				$writer = new \Zend_Log_Writer_Stream(BP.'/var/log/'.$name);
				$logger = new \Zend_Log();
			}else{
				$writer = new \Zend\Log\Writer\Stream(BP.'/var/log/'.$name);
				$logger = new \Zend\Log\Logger();
			}

            $logger->addWriter($writer);
            
            $m = $message;
            if(is_array($message)){$m = json_encode($message);}

            if($this->versionMagentoCompare('2.4.3')){
				$logger->info($m);
			}else{
				$l = 6;
            	$levels = ['emergency','alert','critical','error','warning','notice','info','debug'];
            	if(($level != null || $level != false) && in_array($level, $levels)){
                	$l = array_search($level, $levels);
            	} 
				$logger->log($l,$m);
			}
        } catch (\Throwable $th) {
            throw $th;
        }
    }

	public function getVersion($code = null)
    {	
		$result = 'N/A';
		if($code != null){
			if(isset($this->moduleList->getOne($code)['setup_version'])){
				$result = $this->moduleList->getOne($code)['setup_version'];
			}
		}
        return $result;
    }

	public function versionMagentoCompare($ver, $operator = '>=')
    {
        $productMetadata = $this->getObject(ProductMetadataInterface::class);
        $version = $productMetadata->getVersion();
        return version_compare($version, $ver, $operator);
    }

	public function getGlobalInfoModule()
	{
		$html  = '<strong>Autor: </strong> <a href="https://www.linkedin.com/in/jose-cruz-chavez" target="_blank" rel="nofollow">José Cruz Chávez</a><br/>';
		$html .= '<strong>Donaciones: </strong> <a href="https://www.paypal.me/gestiondigitalweb" target="_blank" rel="nofollow">Mediante Paypal</a><br/>';
		$html .= '<strong>Sitio Web: </strong> <a href="https://gdw.mx" target="_blank">https://gdw.mx</a><br/>';
		$html .= '<strong>Dudas o requerimientos: </strong> <a href="mailto:jcruz@gdw.mx" target="_blank">jcruz@gdw.mx</a><br/>';
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

	public function getInfoFull($name, $version, $descFull = null, $linkconfig = null, $secc = null)
	{
		$vModule = $this->getVersion($version);
		$html  = '<table style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:15px; width:100%;"><tr>';
        $html .= '<td style="padding:8px; width:33%;"><strong>Nombre: </strong>'.$name.'</td>';
        $html .= '<td style="padding:8px; width:33%;"><strong>Versión: </strong>'.$vModule.'</td>';
		  if($linkconfig != null){
			$fulllink = $this->createObject('Magento\Backend\Helper\Data')->getUrl($linkconfig);
			if($secc != null){$fulllink = $fulllink.$secc;}
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

	public function getCommandInfoFull($command, $descFull = null)
	{
		$html  = '<table style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:15px; width:100%; border-top:10px solid white;"><tr>';
        $html .= '<td style="padding:8px; width:66%;"><strong>Command: </strong>'.$command.'</td>';
		$html .= '<td style="padding:8px; width:33%;">&nbsp;</td>';
    	$html .= '</tr></table>';
		  if($descFull != null){
			$html .= '<div style="background:#f8f8f8; border:0px solid #ccc; margin:0px !important; padding:8px;">';
			$html .= $descFull;
			$html .= '</div>';
		  }
	  return $html;
	}
}