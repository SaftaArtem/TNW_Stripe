<?php
/**
 * TNW_Stripe extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category  TNW
 * @package   TNW_Stripe
 * @copyright Copyright (c) 2017-2018
 * @license   Open Software License (OSL 3.0)
 */
namespace TNW\Stripe\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use TNW\Stripe\Model\Adminhtml\Source\Environment;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ENVIRONMENT = 'environment';
    const KEY_ACTIVE = 'active';
    const KEY_LIVE_PUBLISHABLE_KEY = 'live_publishable_key';
    const KEY_LIVE_SECRET_KEY = 'live_secret_key';
    const KEY_TEST_PUBLISHABLE_KEY = 'test_publishable_key';
    const KEY_TEST_SECRET_KEY = 'test_secret_key';
    const KEY_COUNTRY_CREDIT_CARD = 'countrycreditcard';
    const KEY_CURRENCY = 'currency';
    const KEY_CC_TYPES = 'cctypes';
    const KEY_CC_TYPES_STRIPE_MAPPER = 'cctypes_stripe_mapper';
    const KEY_USE_CVV = 'useccv';
    const KEY_ALLOW_SPECIFIC = 'allowspecific';
    const KEY_SPECIFIC_COUNTRY = 'specificcountry';
    const KEY_SDK_URL = 'sdk_url';

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     * @param null $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $serializer,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->serializer = $serializer;
    }

    /**
     * Return the country specific card type config
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCountrySpecificCardTypeConfig($storeId = null)
    {
        $countryCardTypes = $this->getValue(self::KEY_COUNTRY_CREDIT_CARD, $storeId);
        if (!$countryCardTypes) {
            return [];
        }

        $countryCardTypes = $this->serializer->unserialize($countryCardTypes);
        return is_array($countryCardTypes) ? $countryCardTypes : [];
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getAvailableCardTypes($storeId = null)
    {
        $ccTypes = $this->getValue(self::KEY_CC_TYPES, $storeId);

        return !empty($ccTypes) ? explode(',', $ccTypes) : [];
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getCcTypesMapper($storeId = null)
    {
        $result = json_decode(
            $this->getValue(self::KEY_CC_TYPES_STRIPE_MAPPER, $storeId),
            true
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Gets list of card types available for country.
     *
     * @param string $country
     * @param int|null $storeId
     * @return array
     */
    public function getCountryAvailableCardTypes($country, $storeId = null)
    {
        $types = $this->getCountrySpecificCardTypeConfig($storeId);

        return (!empty($types[$country])) ? $types[$country] : [];
    }

    /**
     * Checks if cvv field is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCvvEnabled($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_USE_CVV, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getCurrency($storeId = null)
    {
        return $this->getValue(self::KEY_CURRENCY, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     * @deprecated
     * @see isCvvEnabled($storeId = null)
     */
    public function isCcvEnabled($storeId = null)
    {
        return $this->isCvvEnabled($storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getEnvironment($storeId = null)
    {
        return $this->getValue(Config::KEY_ENVIRONMENT, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_ACTIVE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getPublishableKey($storeId = null)
    {
        if ($this->isTestMode()) {
            return $this->getValue(self::KEY_TEST_PUBLISHABLE_KEY, $storeId);
        }

        return $this->getValue(self::KEY_LIVE_PUBLISHABLE_KEY, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getSecretKey($storeId = null)
    {
        if ($this->isTestMode()) {
            return $this->getValue(self::KEY_TEST_SECRET_KEY, $storeId);
        }

        return $this->getValue(self::KEY_LIVE_SECRET_KEY, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isTestMode($storeId = null)
    {
        return $this->getEnvironment($storeId) == Environment::ENVIRONMENT_TEST;
    }

    /**
     * @return string
     */
    public function getSdkUrl()
    {
        return $this->getValue(self::KEY_SDK_URL);
    }
}
