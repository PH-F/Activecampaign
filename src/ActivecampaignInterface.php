<?php
namespace Phf\Activecampaign;

interface ActivecampaignInterface
{
    /**
     * Method to publish an ezine
     *
     * @param $subject
     * @param $senderEmail
     * @param $senderName
     * @param $content
     * @param $baseUrl
     * @param $list
     * @return mixed
     */
    public function publish($subject, $senderEmail, $senderName, $content, $baseUrl, $list);

    /**
     * Method get all the lists
     *
     * @return mixed
     */
    public function lists();
}