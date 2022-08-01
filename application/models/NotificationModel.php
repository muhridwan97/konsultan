<?php

use GuzzleHttp\Exception\GuzzleException;
use Pusher\Pusher;
use Pusher\PusherException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class NotificationModel
 * @property Mailer $mailer
 */
class NotificationModel extends MY_Model
{
    protected $table = 'notifications';

    const TYPE_CHAT_PUSH = 'CHAT_PUSH';
    const TYPE_WEB_PUSH = 'WEB_PUSH';
    const TYPE_MAIL_PUSH = 'MAIL_PUSH';

    private $type = [Notify::WEB_PUSH];
    private $users = [];

    /**
     * Set type of notification.
     *
     * @param $notificationType
     * @return $this
     */
    public function via($notificationType)
    {
        if (!key_exists(0, $notificationType)) $notificationType = [$notificationType];

        $this->type = $notificationType;

        if (in_array(Notify::MAIL_PUSH, $this->type)) {
            $this->load->model('modules/Mailer', 'mailer');
        }

        return $this;
    }

    /**
     * Send notification to users.
     *
     * @param Notify $notification
     * @param $users
     */
    public function send(Notify $notification, $users = null)
    {
        if (!empty($users)) {
            $this->users = $users;
        }

        if (!empty($this->users)) {
            if (!is_array($this->users) || (is_array($this->users) && !key_exists(0, $this->users))) {
                $this->users = [$this->users];
            }

            foreach ($this->users as $user) {
                if (in_array(Notify::DATABASE_PUSH, $this->type)) {
                    $data = $notification->toDatabase($user);
                    $this->create($data);
                }
                if (in_array(Notify::WEB_PUSH, $this->type)) {
                    $data = $notification->toWeb($user);
                    try {
                        $pusher = new Pusher(
                            env('PUSHER_APP_KEY'),
                            env('PUSHER_APP_SECRET'),
                            env('PUSHER_APP_ID'),
                            ['cluster' => env('PUSHER_APP_CLUSTER'), 'encrypted' => false]
                        );
                        $pusher->trigger($data['channel'] . '-' . $user['id'], $data['event'], $data['payload']);
                    } catch (PusherException $e) {
                        $e->getMessage();
                    }
                }
                if (in_array(Notify::MAIL_PUSH, $this->type)) {
                    $data = $notification->toMail($user);
                    $emailTo = $data['to'];
                    $subject = $data['subject'];
                    $template = $data['template'];
                    $payloadData = $data['data'];
                    $option = get_if_exist($data, 'option', []);
                    $this->mailer->send($emailTo, $subject, $template, $payloadData, $option);
                }
                if (in_array(Notify::CHAT_PUSH, $this->type)) {
                    $data = $notification->toChat($user);
                    $payload = $data['payload'];
                    $baseUri = get_if_exist($data, 'base_uri', env('CHAT_API_URL'));
                    $url = get_if_exist($data, 'url', '/');
                    $method = get_if_exist($data, 'method', 'GET');
                    if (!key_exists('token', $payload)) {
                        $payload['token'] = env('CHAT_API_TOKEN');
                    }
                    if (env('APP_ENVIRONMENT') == 'development' && !empty(env('CHAT_API_SANDBOX_NUMBER'))) {
                        if(isset($payload['chatId']) && strpos($payload['chatId'], '@g.us') !== false && !empty(env('CHAT_GROUP_API_SANDBOX_NUMBER'))){
                            $payload['chatId'] = detect_chat_id(env('CHAT_GROUP_API_SANDBOX_NUMBER'));
                        }else{
                            $payload['chatId'] = detect_chat_id(env('CHAT_API_SANDBOX_NUMBER'));
                        }                        
                    }

                    try {
                        $client = new GuzzleHttp\Client([
                            'base_uri' => $baseUri,
                            'verify' => boolval(env('CHAT_API_SECURE'))
                        ]);
                        $response = $client->request($method, $url, [
                            'query' => ['token' => $payload['token']],
                            'form_params' => $payload
                        ]);
                        $result = json_decode($response->getBody(), true);
                        $resultResponse = get_if_exist($result, 'sent', 1);
                        if (empty($resultResponse) || $resultResponse == '0' || $resultResponse == false) {
                            log_message('error', Notify::CHAT_PUSH . ': ' . json_encode($result));
                        }
                    } catch (GuzzleException $e) {
                        log_message('error', Notify::CHAT_PUSH . ': http request error - ' . $e->getMessage());
                    }
                }
                if (in_array(Notify::ARRAY_PUSH, $this->type)) {
                    $notification->toArray();
                }
            }
        }
    }

    /**
     * Set user of notification.
     *
     * @param $users
     * @return $this
     */
    public function to($users)
    {
        if (!empty($users)) {
            if (is_array($users) && !key_exists(0, $users)) $users = [$users];

            $this->users = $users;
        }

        return $this;
    }

    /**
     * Broadcast notification to users.
     *
     * TYPE_WEB_PUSH:
     * --------------
     * push web notification via web socket, following is the example of simple minimal payload:
     *
     * $data = [
     *      'id_user' => $supervisor['id_user'],
     *      'id_related' => $id,
     *      'channel' => NotificationModel::SUBSCRIBE_ACTIVITY,
     *      'event' => NotificationModel::EVENT_ACTIVITY_REPORTING,
     *      'payload' => [
     *          'message' => "Please review activity {$label}",
     *          'url' => site_url('activity/activity-report/view/' . $id),
     *          'time' => format_date('now', 'Y-m-d H:i:s'),
     *          'description' => $message
     *      ]
     * ];
     * $this->notification->broadcast($data);
     *
     *
     * TYPE_CHAT_PUSH:
     * ----------------
     * push notification to realtime chat with api example of the data bellow:
     * Payload data depends on CHAT API that you used!
     *
     * $data = [
     *      'url' => 'status',
     *      'method' => 'GET',
     *      'payload' => [
     *          'message' => "Please review activity",
     *          'url' => site_url('activity/activity-report/view/1'),
     *          'time' => format_date('now', 'Y-m-d H:i:s'),
     *          'description' => 'Message detail ' . php_sapi_name()
     *      ]
     * ];
     *
     *
     * TYPE_EMAIL_PUSH:
     * ----------------
     * send email to client, with template message and attachment, following the example:
     *
     * $data = [
     *      'to' => 'angga.aw92@gmail.com',
     *      'subject' => "User update information",
     *      'template' => 'email/basic',
     *      'option' => [
     *          'cc' => ['angga@mail.com', 'ari@mail.com'],
     *          'attachment' => '/path/to/file'
     *      ],
     *      'payload' => [
     *          'name' => 'Angga Ari Wijaya',
     *          'username' => 'angga.ari',
     *          'employees' => []
     *      ]
     * ];
     *
     * @param $data
     * @param string $type
     * @return array|bool
     */
    public function broadcast($data, $type = self::TYPE_WEB_PUSH)
    {
        $payload = $data['payload'];

        switch ($type) {
            case self::TYPE_WEB_PUSH:
                $this->create([
                    'id_user' => $data['id_user'],
                    'id_related' => $data['id_related'],
                    'channel' => $data['channel'],
                    'event' => $data['event'],
                    'data' => json_encode($data['payload'])
                ]);
                try {
                    $pusher = new Pusher(
                        env('PUSHER_APP_KEY'),
                        env('PUSHER_APP_SECRET'),
                        env('PUSHER_APP_ID'),
                        ['cluster' => env('PUSHER_APP_CLUSTER'), 'encrypted' => false]
                    );
                    $pusher->trigger($data['channel'] . '-' . $data['id_user'], $data['event'], $data['payload']);
                } catch (PusherException $e) {
                    return $e->getMessage();
                }
                break;
            case self::TYPE_CHAT_PUSH:
                $baseUri = get_if_exist($data, 'base_uri', env('CHAT_API_URL'));
                $url = get_if_exist($data, 'url', '/');
                $method = get_if_exist($data, 'method', 'GET');
                if (!key_exists('token', $payload)) {
                    $payload['token'] = env('CHAT_API_TOKEN');
                }
                if (env('APP_ENVIRONMENT') == 'development' && !empty(env('CHAT_API_SANDBOX_NUMBER')) && $url != 'dialog') {
                    if (in_array($url, ['sendMessage'])) {
                        $payload['body'] = $payload['body'] . "\n————————————————\nSent to {$payload['chatId']}";
                    }
                    if (isset($payload['chatId']) && strpos($payload['chatId'], '@g.us') !== false && !empty(env('CHAT_GROUP_API_SANDBOX_NUMBER'))) {
                        $payload['chatId'] = detect_chat_id(env('CHAT_GROUP_API_SANDBOX_NUMBER'));
                    } else {
                        $payload['chatId'] = detect_chat_id(env('CHAT_API_SANDBOX_NUMBER'));
                    }                        
                }

                if (preg_match('/send/', $url)) {
                    if (empty(str_replace(["@c.us", "@g.us"], "", $payload['chatId']))) {
                        //log_message('error', self::TYPE_CHAT_PUSH . ': chat id is empty');
                        return false;
                    }
                }

                try {
                    $client = new GuzzleHttp\Client([
                        'base_uri' => $baseUri,
                        'verify' => boolval(env('CHAT_API_SECURE'))
                    ]);

                    // phone number checking
                    if (preg_match('/@c.us$/', $payload['chatId'])) {
                        $response = $client->request('get', 'checkPhone', [
                            'query' => [
                                'token' => $payload['token'],
                                'phone' => str_replace(["@c.us"], "", $payload['chatId'])
                            ],
                        ]);
                        $result = json_decode($response->getBody(), true);
                        if (($result['result'] ?? '') == 'not exists') {
                            log_message('error', self::TYPE_CHAT_PUSH . ': phone not exist ' . $payload['chatId']);
                            return false;
                        }
                    }

                    $response = $client->request($method, $url, [
                        'query' => ['token' => $payload['token']],
                        'form_params' => $payload
                    ]);
                    $result = json_decode($response->getBody(), true);
                    $resultResponse = get_if_exist($result, 'sent', 1);
                    if (empty($resultResponse) || $resultResponse == '0' || $resultResponse == false) {
                        log_message('error', self::TYPE_CHAT_PUSH . ': ' . json_encode($result));
                    }
                    return $result;
                } catch (GuzzleException $e) {
                    log_message('error', self::TYPE_CHAT_PUSH . ': http request error - ' . $e->getMessage());
                    return ['error' => $e->getMessage()];
                }

            case self::TYPE_MAIL_PUSH:
                $this->load->model('modules/Mailer', 'mailer');
                $emailTo = $data['to'];
                $subject = $data['subject'];
                $template = $data['template'];
                $data = $payload;
                $option = get_if_exist($data, 'option', []);
                return $this->mailer->send($emailTo, $subject, $template, $data, $option);
        }
        return true;
    }

    /**
     * Get pushed notification data notifications by user from database, and parsed into list.
     *
     * @param $userId
     * @param int $limit
     * @return array
     */
    public function getByUser($userId, $limit = 100)
    {
        $this->db->from($this->table)
            ->where('id_user', $userId)
            ->limit($limit)
            ->order_by('created_at', 'desc');

        $notifications = $this->db->get()->result_array();

        foreach ($notifications as &$notification) {
            $notification['data'] = (array)json_decode($notification['data']);
        }

        return $notifications;
    }

    /**
     * Get sticky navbar notification.
     *
     * @param null $userId
     * @return array
     */
    public static function getUnread($userId = null)
    {
        if ($userId == null) {
            $userId = UserModel::authenticatedUserData('id');
        }

        $CI = get_instance();
        $CI->db->from('notifications')
            ->where([
                'id_user' => $userId,
                'is_read' => false,
                'created_at>=DATE(NOW()) - INTERVAL 7 DAY' => null
            ])
            ->order_by('created_at', 'desc')
            ->limit(3);

        $notifications = $CI->db->get()->result_array();

        foreach ($notifications as &$notification) {
            $notification['data'] = (array)json_decode($notification['data']);
        }

        return $notifications;
    }

    /**
     * Parse notification content.
     *
     * @param $payload
     * @param string $url
     * @return mixed
     */
    public static function parseMessage($payload, $url = '')
    {
        $message = $payload->message;
        if (property_exists($payload, 'link_text')) {
            $links = $payload->link_text;
            foreach ($links as $link) {
                $templateLink = "<a class='font-weight-medium' href='{$link->url}'>{$link->text}</a>";
                $message = str_replace($link->text, $templateLink, $message);
            }
        } else if (!empty($url)) {
            $message = "<a href='{$url}'>{$message}</a>";
        }

        return $message;
    }
}
