<?php
namespace Posta\Dostava\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Dostava extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
	// naslijeđuje klasu AbstractCarrier i provodi interface CarrierInterface
{
    
    protected $_code = 'carriercode';
	
	protected $_logger;
   
    protected $_isFixed = true;

    protected $_rateResultFactory;


   
    protected $_rateMethodFactory;

   
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
		$this->_logger = $logger;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    
	// skuplja troškove dostave:
    public function collectRates(RateRequest $request)
    {
		// ako nije aktiviran
        if (!$this->getConfigFlag('active')) {
            return false;
        }

       
        $result = $this->_rateResultFactory->create();
		
		// vrijednosti varijabli iz config.xml ubacuje pomoću $this->getConfigData()
		$trosak = $this->getConfigData('price');
		$nacin_dostave = $this->_rateMethodFactory->create();
		$nacin_dostave->setCarrier($this->_code);
		$nacin_dostave->setCarrierTitle($this->getConfigData('title'));
		$nacin_dostave->setMethod($this->_code);
		$nacin_dostave->setMethodTitle($this->getConfigData('name'));
		$nacin_dostave->setPrice($trosak);

		$result->append($nacin_dostave);
        

        return $result;
    }

    
    public function getAllowedMethods()
    {
		
        return [$this->_code=> $this->getConfigData('name')];
    }
}
