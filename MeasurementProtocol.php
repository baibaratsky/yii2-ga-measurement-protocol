<?php

namespace baibaratsky\yii\google\analytics;

use TheIconic\Tracking\GoogleAnalytics\Analytics;
use yii\base\BaseObject;

/**
 * Class MeasurementProtocol
 * @package baibaratsky\yii\google\analytics
 */
class MeasurementProtocol extends BaseObject
{
    /** @var string Tracking ID (UA-XXXX-Y) */
    public $trackingId;

    /** @var string Protocol version */
    public $version = '1';

    /** @var bool Use HTTPS instead of plain HTTP */
    public $useSsl = false;

    /** @var bool Override the IP address by the user’s */
    public $overrideIp = true;

    /** @var bool Anonymize the IP address of the sender */
    public $anonymizeIp = false;

    /** @var bool Use asynchronous requests (not waiting for a response) */
    public $asyncMode = false;

    /** @var bool Try to set ClientId automatically from `ga_` cookie */
    public $autoSetClientId = false;

    /**
     * @return Analytics
     */
    public function request()
    {
        $request = new Analytics($this->useSsl);
        $request->setTrackingId($this->trackingId)
                ->setProtocolVersion($this->version)
                ->setAsyncRequest($this->asyncMode);

        if ($this->overrideIp && isset(\Yii::$app->request->userIP)) {
            $request->setIpOverride(\Yii::$app->request->userIP);
        }

        if ($this->anonymizeIp) {
            $request->setAnonymizeIp(1);
        }

        if ($this->autoSetClientId) {
            $clientId = $this->extractClientIdFromCookie();
            if (!empty($clientId)) {
                $request->setClientId($clientId);
            }
        }

        return $request;
    }

    /**
     * @return string
     */
    protected function extractClientIdFromCookie()
    {
        $cookie = \Yii::$app->request->cookies->getValue('ga_', '');
        $cookieParts = explode('.', $cookie);
        $clientIdParts =  array_slice($cookieParts, -2);
        return implode('.', $clientIdParts);
    }
}