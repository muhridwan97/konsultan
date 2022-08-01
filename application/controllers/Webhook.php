<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Webhook
 * @property NotificationModel $notification
 */
class Webhook extends MY_Controller
{
    protected $strictRequest = false;

    /**
     * Webhook constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('NotificationModel', 'notification');
    }

    /**
     * Error notification summary
     */
    public function error_notification()
    {
        $yesterday = date('Y-m-d', strtotime('0 day'));
        $logFile = APPPATH . 'logs' . DIRECTORY_SEPARATOR . "log-{$yesterday}.log";

        $data = [];
        if (file_exists($logFile)) {
            $fn = fopen($logFile, "r");
            $lastErrorTime = '';
            $messageData = '';
            while (!feof($fn)) {
                $result = fgets($fn);
                if (!empty(trim($result))) {
                    if (preg_match('/^ERROR/', $result) && !preg_match('/404 Page Not Found/', $result)) {
                        preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $result, $time);
                        if (preg_match('/Severity: Warning -->/', $result)) {
                            $content = substr($result, strpos($result, "Severity: Warning -->") + 22);
                            $type = 'Syntax or Argument Error';
                        } else if (preg_match('/Query error:/', $result)) {
                            $content = substr($result, strpos($result, "Query error: "));
                            $type = 'Query Error';
                        } else if (preg_match('/CHAT_PUSH/', $result)) {
                            $content = substr($result, strpos($result, "CHAT_PUSH"));
                            $type = 'Chat Error';
                        } else {
                            $content = substr($result, strpos($result, " --> ") + 4);
                            $type = 'Other Error';
                        }
                        $messageData .= trim($content) . "\r\n";

                        if ($time[0] != $lastErrorTime) {
                            $data[$time[0]]['type'] = $type;
                            $data[$time[0]]['message'] = $messageData;
                            $lastErrorTime = $time[0];
                            $messageData = '';
                        }
                    }
                }
            }

            fclose($fn);
        }

        $chatId = '6281333377368-1557128212@g.us';
        if (!empty($data)) {
            $messages = '';
            foreach ($data as $time => $message) {
                $messages .= "*{$time} - {$message['type']}*\n";
                $messages .= "{$message['message']}";
                $messages .= "\n";
            }

            $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => $chatId,
                    'body' => $messages,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);

            $this->notification->broadcast([
                'url' => 'sendFile',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($chatId),
                    'body' => site_url('system-log/download/' . $logFile),
                    'filename' => $logFile,
                    'caption' => "*{LOG $yesterday}*"
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
        } else {
            $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($chatId),
                    'body' => 'No log available on ' . $yesterday,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
        }
    }

    /**
     * Notify people when code pushed to server
     */
    public function code_push()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $monitoringBranch = ['master', 'develop'];
        $update = false;
        $owner = '';
        $repository = '';
        $type = '';
        $branch = '';
        $author = '';
        $hash = '';
        $message = '';
        $time = '';

        if (!empty($payload) && isset($payload['push'])) {
            $lastChange = $payload['push']['changes'][count($payload['push']['changes']) - 1]['new'];
            $branch = isset($lastChange['name']) && !empty($lastChange['name']) ? $lastChange['name'] : '';
            if (in_array($branch, [$monitoringBranch]) || $lastChange['type'] == 'tag') {
                $update = true;
                $owner = $payload['repository']['owner']['display_name'];
                $repository = strtoupper($payload['repository']['name']);
                $type = ucfirst($lastChange['type']);
                $branch = $lastChange['name'];
                $hash = $lastChange['target']['hash'];
                $message = $lastChange['target']['message'];
                $author = $lastChange['target']['author']['raw'];
                $time = date('Y-m-d H:i:s', strtotime($lastChange['target']['date']));
            }
        }

        if ($update) {
            $result = $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id('6281333377368-1557128212@g.us'),
                    'body' => "PUSH REQUEST\n*{$repository}* Owned by {$owner}\n*{$type} {$branch}*\nAuthor: {$author}\nHash: {$hash}\nMessage: *{$message}*\n Pushed at {$time}",
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
            $this->render_json([
                'message' => $result
            ]);
        } else {
            $this->render_json([
                'message' => 'No update available'
            ]);
        }
    }

}
