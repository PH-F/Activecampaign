<?php
namespace Phf\Activecampaign;

class ActivecampaignManager implements ActivecampaignInterface
{
    private $url;
    private $user;
    private $pass;

    public function __construct($url, $user, $pass)
    {
        $this->url = $url;
        $this->user = $user;
        $this->pass = $pass;
    }

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
    public function publish($subject, $senderEmail, $senderName, $content, $baseUrl, $list)
    {
        $params = [
            'api_user' => $this->user,
            'api_pass' => $this->pass,
            'api_action' => 'message_add',
            'api_output' => 'serialize',
        ];

        $post = [
            //'id'                     => 0, // adds a new one
            'format' => 'mime',
            'subject' => $subject,
            'fromemail' => $senderEmail,
            'fromname' => $senderName,
            'reply2' => $senderEmail,
            'priority' => '3', // 1=high, 3=medium/default, 5=low
            'charset' => 'utf-8',
            'encoding' => '8bit',

            // html version
            'htmlconstructor' => 'editor', // possible values: editor, external, upload
            'html' => $content, // content of your html email
            'htmlfetch' => $baseUrl, // URL where to fetch the body from
            'htmlfetchwhen' => 'send', // possible values: (fetch at) 'send' and (fetch) 'pers'(onalized)

            // text version
            'textconstructor' => 'editor', // possible values: editor, external, upload
            'text' => strip_tags($content), // content of your text only email
            'textfetch' => $baseUrl, // URL where to fetch the body from
            'textfetchwhen' => 'send', // possible values: (fetch at) 'send' and (fetch) 'pers'(onalized)

            // assign to lists:
            'p[]' => $list,
        ];

        $query = "";
        foreach ($params as $key => $value) {
            $query .= $key . '=' . urlencode($value) . '&';
        }
        $query = rtrim($query, '& ');

        $data = "";
        foreach ($post as $key => $value) {
            $data .= $key . '=' . urlencode($value) . '&';
        }

        $data = rtrim($data, '& ');
        $url = rtrim($this->url, '/ ');
        $api = $url . '/admin/api.php?' . $query;

        $request = curl_init($api);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $data);

        $response = (string)curl_exec($request);
        curl_close($request);

        if (!$response) {
            return "connection failed";
        }

        $result = unserialize($response);

        return ($result['result_code'] ? $result['id'] : 'FAILED');
    }


    /**
     * Method get all the lists
     *
     * @return mixed
     */
    public function lists()
    {
        $params = [
            'api_user' => $this->user,
            'api_pass' => $this->pass,
            'api_action' => 'list_list',
            'ids'       => 'all_with_name',
        ];

        $query = "";
        foreach ($params as $key => $value) {
            $query .= $key . '=' . urlencode($value) . '&';
        }
        $query = rtrim($query, '& ');

        $url = rtrim($this->url, '/ ');
        $api = $url . '/admin/api.php?' . $query;

        $request = curl_init($api);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

        $response = (string)curl_exec($request);
        curl_close($request);

        if (!$response) {
            return "connection failed";
        }

        $xml = (array)simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);

        return empty($xml) ? "FAILED" : $xml['row'];
    }
}