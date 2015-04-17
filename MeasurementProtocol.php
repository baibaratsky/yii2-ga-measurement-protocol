<?php

namespace baibaratsky\yii\google\analytics;

use TheIconic\Tracking\GoogleAnalytics\Analytics;
use yii\base\Object;

/**
 * Class MeasurementProtocol
 * @package baibaratsky\yii\google\analytics
 */
class MeasurementProtocol extends Object
{
    /** @var string Tracking ID (UA-XXXX-Y) */
    public $trackingId;

    /** @var string Protocol version */
    public $version = '1';

    /** @var bool Use HTTPS instead of plain HTTP */
    public $useSsl = false;

    /** @var bool Override the IP address by the user’s one */
    public $overrideIp = true;

    /** @var bool Anonymize the IP address of the sender */
    public $anonymizeIp = false;

    /** @var bool Use asynchronous requests (not waiting for a response) */
    public $asyncMode = false;

    /**
     * @return Analytics
     */
    public function request()
    {
        $request = new Analytics($this->useSsl);
        $request->setTrackingId($this->trackingId)
                ->setProtocolVersion($this->version)
                ->setAsyncRequest($this->asyncMode);

        if ($this->overrideIp) {
            if (isset(\Yii::$app->request)) {
                $request->setIpOverride(\Yii::$app->request->userIP);
            } else {
                \Yii::warning('Cant’t access the request component to override the IP address. Is it a web application?');
            }
        }

        if ($this->anonymizeIp) {
            $request->setAnonymizeIp(1);
        }

        return $request;
    }
}