<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Notification
 * @property NotificationModel $notification
 */
class Whatsapp_dialog extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'ajax_get_participant' => 'GET',
        ]);
    }
    public function index()
    {
        $data = [
            'url' => 'dialogs',
            'method' => 'GET',
            'payload' => []
        ];
        $results = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        $results = $results['dialogs'];
        for ($i=0;$i<count($results);$i++) {
            $results[$i]['isGroup']=$results[$i]['metadata']['isGroup'];
            $results[$i]['participants']=$results[$i]['metadata']['participants'];
            $results[$i]['groupInviteLink']=$results[$i]['metadata']['groupInviteLink'];
            $results[$i]['no']=($i+1);
        }
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];

        $this->render('whatsapp_dialog/index', compact('datas'));
    }

    /**
     * Get the account status and QR code for authorization.
     */
    public function status()
    {
        $data = [
            'url' => 'status',
            'method' => 'GET',
            'payload' => [
                'full' => true,
                'no_wakeup' => false
            ]
        ];
        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        print_debug($result);
        
    }
    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $data = [
            'url' => 'dialogs',
            'method' => 'GET',
            'payload' => []
        ];
        $results = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        $results = $results['dialogs'];
        // print_r(count($results));
        for ($i=0;$i<count($results);$i++) {
            $results[$i]['isGroup']=$results[$i]['metadata']['isGroup'];
            $results[$i]['participants']=$results[$i]['metadata']['participants'];
            $results[$i]['groupInviteLink']=$results[$i]['metadata']['groupInviteLink'];
            $results[$i]['no']=($i+1);
            // print_r($results[$i]);
            // print_r("</br></br>") ;
        }
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        // print_debug($datas);

        $this->render_json($datas);
    }

    /**
     * Direct link to QR-code in the form of an image, not base64.
     */
    public function qr()
    {
        $data = [
            'url' => 'qr_code',
            'method' => 'GET',
            'payload' => []
        ];
        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        print_debug($result);
    }

    /**
     * Direct link to QR-code in the form of an image, not base64.
     */
    public function settings()
    {
        $data = [
            'url' => 'settings',
            'method' => 'GET',
            'payload' => []
        ];
        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        print_debug($result);
    }

    
    /**
     * Ajax get all participant
     */
    public function ajax_get_participant()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $whatsapp_group = $this->input->get('whatsapp_group');

            $data = [
                'url' => 'dialog',
                'method' => 'GET',
                'payload' => [
                    'chatId' => detect_chat_id($whatsapp_group),
                ]
            ];
            $results = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
            $participantResult = [];
            $tempParticipant = [];
            if(!empty($results['metadata']['participantsInfo'])){
                $participants = $results['metadata']['participantsInfo'];
                foreach ($participants as $key => $participant) {
                    $tempParticipant = [
                        'id' => $participant['id'],
                        'name' => $participant['name'],
                        'number' => invert_chat_id($participant['id']),
                    ];
                    $participantResult [] = $tempParticipant;
                }
            }else{
                $participants = $results['metadata']['participants'];
                foreach ($participants as $key => $participant) {
                    $tempParticipant = [
                        'id' => $participant,
                        'name' => 'unknown',
                        'number' => invert_chat_id($participant),
                    ];
                    $participantResult [] = $tempParticipant;
                }
            }            

            header('Content-Type: application/json');
            echo json_encode($participantResult);
        }
    }

    /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    
}
