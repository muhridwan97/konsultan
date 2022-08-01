<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Illuminate\Support\Str;
use Milon\Barcode\DNS2D;

/**
 * Class Linked_entry_permit
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitCustomerModel $transporterEntryPermitCustomer
 * @property TransporterEntryPermitUploadModel $transporterEntryPermitUpload
 * @property TransporterEntryPermitChecklistModel $transporterEntryPermitChecklist
 * @property TransporterEntryPermitChecklistDetailModel $transporterEntryPermitChecklistDetail
 * @property SafeConductModel $safeConduct
 * @property SafeConductChecklistModel $safeConductChecklist
 * @property SafeConductChecklistDetailModel $safeConductChecklistDetail
 * @property BookingModel $booking
 * @property NotificationModel $notification
 * @property PeopleModel $people
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Linked_entry_permit extends MY_Controller
{
    /**
     * Linked_entry_permit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('TransporterEntryPermitCustomerModel', 'transporterEntryPermitCustomer');
        $this->load->model('TransporterEntryPermitUploadModel', 'transporterEntryPermitUpload');
        $this->load->model('TransporterEntryPermitChecklistModel', 'transporterEntryPermitChecklist');
        $this->load->model('TransporterEntryPermitChecklistDetailModel', 'transporterEntryPermitChecklistDetail');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('SafeConductChecklistDetailModel', 'safeConductChecklistDetail');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
            'get_tep_reference_customer' => 'GET',
            'update_linked_tep' => 'GET|POST|PUT',
        ]);
    }

    /**
     * Show form create linked transporter entry permit.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);

        $customerId = $this->input->post('customer');
        $uploadIds = if_empty($this->input->post('uploads'), []);

        $customer = empty($customerId) ? [] : $this->people->getById($customerId);
        $uploads = empty($uploadIds) ? [] : $this->upload->getBy(['uploads.id' => $uploadIds]);

        $this->render('transporter_entry_permit/create_linked_tep', compact('customer', 'uploads'));
    }

    /**
     * Save linked transporter entry permit (OUTBOUND)
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);

        if ($this->validate()) {
            $uploadIds = if_empty($this->input->post('uploads'), []);
            $customerId = $this->input->post('customer');
            $emailType = $this->input->post('email_type');
            $inputEmail = $this->input->post('input_email');
            $description = $this->input->post('description');
            $linkedTepId = $this->input->post('linked_tep');

            $this->db->trans_start();

            $code = $this->transporterEntryPermit->generateCode();
            $expiredAt = date('Y-m-d 23:59:59');

            $this->transporterEntryPermit->create([
                'tep_code' => $code,
                'tep_category' => "OUTBOUND",
                'expired_at' => $expiredAt,
                'description' => $description,
                'id_branch' => get_active_branch('id'),
                'id_tep_reference' => null,
                'id_linked_tep' => $linkedTepId,
            ]);
            $tepId = $this->db->insert_id();

            $this->transporterEntryPermitCustomer->create([
                'id_tep' => $tepId,
                'id_customer' => $customerId,
            ]);

            foreach ($uploadIds as $uploadId) {
                $this->transporterEntryPermitUpload->create([
                    'id_tep' => $tepId,
                    'id_upload' => $uploadId
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $customer = $this->people->getById($customerId);

                // send email to customer
                if (!empty($emailType)) {
                    $barcode = new DNS2D();
                    $barcode->setStorPath(APPPATH . "cache/");
                    $baseFolder = 'qr/' . date('Y/m');
                    $this->uploader->makeFolder($baseFolder);

                    $qrCode = $barcode->getBarcodePNG($code, "QRCODE", 8, 8);
                    $qrFileName = $baseFolder . '/' . Str::slug($code) . '-' . uniqid() . '.jpg';
                    base64_to_jpeg($qrCode, Uploader::UPLOAD_PATH . $qrFileName);
                    $qrCodeUrl = base_url('uploads/' . $qrFileName);

                    $emailTo = $emailType == 'INPUT' ? $inputEmail : $customer['email'];
                    $emailTitle = "Transporter entry permit {$customer['name']} for {$customer['name']}";
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'name' => $customer['name'],
                        'email' => $emailTo,
                        'content' => "
                            Transporter entry permit is generated and will be expired at <b>{$expiredAt}</b>,
                            please deliver this information (the code) to your transporter vendor or driver. <br> 
                            (THE CODES ARE NEEDED WHEN VISIT AND PICK UP FOR INBOUND / OUTBOUND ACTIVITY) .
                            <br><br>
                            <table style='width:100%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
                                <tr style='border-bottom: 1px solid #aaaaaa'>
                                    <th align='center'>No</th>
                                    <th>Code</th>
                                    <th>Expired At</th>
                                </tr>
                                <tr style='border-bottom: 1px solid #aaaaaa'>
                                    <td align='center'>1</td>
                                    <th>{$code}</th>
                                    <td>{$expiredAt}</td>
                                </tr>
                                <tr style='border-bottom: 1px solid #aaaaaa'>
                                    <td align='center' colspan='2'>QR Code</td>
                                    <td><img src='" . base_url('uploads/' . $qrFileName) . "' alt='{$code}'></td>
                                </tr>
                            </table>
                        "
                    ];
                    if (!empty($emailTo)) {
                        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                    }

                    // send qr code image to group
                    $userCreator = UserModel::authenticatedUserData('name');
                    $whatsappGroup = get_active_branch('whatsapp_group');
                    $this->notification->broadcast([
                        'url' => 'sendFile',
                        'method' => 'POST',
                        'payload' => [
                            'chatId' => $whatsappGroup,
                            'body' => $qrCodeUrl,
                            'filename' => basename($qrCodeUrl),
                            'caption' => "[TEP CREATED] Transporter entry permit OUTBOUND is created by {$userCreator} for customer {$customer['name']} code TEP {$code}"
                        ]
                    ], NotificationModel::TYPE_CHAT_PUSH);
                }

                flash('success', "Entry permit for {$customer['name']} successfully created", 'transporter-entry-permit');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'total_code' => 'trim|required|integer|is_natural_no_zero',
            'customer' => 'required',
            'uploads[]' => 'required',
            'linked_tep' => 'required',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Get tep reference for the link.
     *
     * @param $customerId
     */
    public function get_tep_reference_customer($customerId)
    {
        $customerTargetId = -1;
        if ($customerId == 9359) { // SMGP in medan 2
            $customerTargetId = 9151; // SMGP in medan 1
        }
        $tepCustomers = $this->transporterEntryPermitCustomer->getAll([
            'customer' => $customerTargetId,
            'branch' => 2, // hard-coded branch medan 1
            'outstanding_linked_tep' => 1,
        ]);

        $this->render_json($tepCustomers);
    }

    /**
     * Update linked tep.
     *
     * @param $id
     */
    public function update_linked_tep($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_EDIT);

        $tep = $this->transporterEntryPermit->getById($id);

        $linkedTep = $this->transporterEntryPermit->getAll([
            'branch' => 2,
            'tep' => $tep['id_linked_tep']
        ]);
        if (empty($tep['id_linked_tep']) || empty($linkedTep)) {
            flash('danger', 'No another TEP is linked to current data', '_back');
        } else {
            $linkedTep = $linkedTep[0];
        }

        if (_is_method('get')) {
            $this->render('transporter_entry_permit/update_linked_tep', compact('tep', 'linkedTep'));
        } else {
            $safeConducts = $this->safeConduct->getSafeConductsByTepId($tep['id']);
            $safeConduct = null;
            if (!empty($safeConducts)) {
                $safeConduct = $safeConducts[0];
                $safeConduct['checklists'] = $this->safeConductChecklist->getSafeConductChecklistBySafeConductId($safeConduct['id']);
                foreach ($safeConduct['checklists'] as &$safeConductChecklist) {
                    $safeConductChecklist['details'] = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($safeConductChecklist['id']);
                }
            }
            $tepChecklists = $this->transporterEntryPermitChecklist->getBy([
                'transporter_entry_permit_checklists.id_tep' => $tep['id']
            ]);
            foreach ($tepChecklists as &$tepChecklist) {
                $tepChecklist['details'] = $this->transporterEntryPermitChecklistDetail->getBy([
                    'transporter_entry_permit_checklist_details.id_tep_checklist' => $tepChecklist['id']
                ]);
            }

            $this->db->trans_start();

            $this->transporterEntryPermit->update([
                'checked_in_at' => if_empty($tep['checked_in_at'], null),
                'checked_in_by' => if_empty($tep['checked_in_by'], null),
                'checked_in_description' => if_empty($tep['checked_in_description'], null),
                'checked_out_at' => if_empty($tep['checked_out_at'], null),
                'checked_out_by' => if_empty($tep['checked_out_by'], null),
                'checked_out_description' => if_empty($tep['checked_out_description'], null),
                'receiver_name' => if_empty($tep['receiver_name'], null),
                'receiver_contact' => if_empty($tep['receiver_contact'], null),
                'receiver_email' => if_empty($tep['receiver_email'], null),
                'receiver_vehicle' => if_empty($tep['receiver_vehicle'], null),
                'receiver_no_police' => if_empty($tep['receiver_no_police'], null),
                'additional_guest_name' => if_empty($tep['additional_guest_name'], null),
            ], $linkedTep['id']);

            // update checklist linked tep
            $this->transporterEntryPermitChecklist->delete(['id_tep' => $linkedTep['id']]);
            foreach ($tepChecklists as $tepChecklist) {
                $this->transporterEntryPermitChecklist->create([
                    'id_tep' => $linkedTep['id'],
                    'id_container' => if_empty($tepChecklist['id_container'], null),
                    'type' => $tepChecklist['type'],
                    'attachment' => $tepChecklist['attachment'],
                    'attachment_seal' => $tepChecklist['attachment_seal'],
                    'description' => $tepChecklist['description'],
                    'created_at' => $tepChecklist['created_at'],
                    'created_by' => $tepChecklist['created_by'],
                ]);
                $tepChecklistId = $this->db->insert_id();

                foreach ($tepChecklist['details'] as $tepDetail) {
                    $this->transporterEntryPermitChecklistDetail->create([
                        'id_tep_checklist' => $tepChecklistId,
                        'id_checklist' => $tepDetail['id_checklist'],
                        'result' => $tepDetail['result'],
                        'description' => $tepDetail['description'],
                        'created_at' => $tepDetail['created_at'],
                        'created_by' => $tepDetail['created_by'],
                    ]);
                }
            }

            $linkedSafeConducts = $this->safeConduct->getSafeConductBaseQuery($linkedTep['id_branch'])->where([
                'safe_conducts.id_transporter_entry_permit' => $linkedTep['id']
            ])->get()->result_array();
            foreach ($linkedSafeConducts as $linkedSafeConduct) {
                // update security check from tep (no need from safe conduct to eager data acquired)
                $this->safeConduct->updateSafeConduct([
                    'security_in_date' => if_empty($tep['checked_in_at'], null),
                    'security_in_description' => get_if_exist($safeConduct, 'security_in_description', null),
                    'security_out_date' => if_empty($tep['checked_out_at'], null),
                    'security_out_description' => get_if_exist($safeConduct, 'security_out_description', null),
                ], $linkedSafeConduct['id']);

                // copy current safe conduct checklist to linked tep -> safe conduct
                if (!empty($safeConduct)) {
                    $this->safeConductChecklist->delete(['id_safe_conduct' => $linkedSafeConduct['id']]);
                    foreach ($safeConduct['checklists'] as $checklist) {
                        $this->safeConductChecklist->create([
                            'id_safe_conduct' => $linkedSafeConduct['id'],
                            'id_container' => if_empty($checklist['id_container'], null),
                            'type' => $checklist['type'],
                            'attachment' => $checklist['attachment'],
                            'attachment_seal' => $checklist['attachment_seal'],
                            'description' => $checklist['description'],
                            'created_at' => $checklist['created_at'],
                            'created_by' => $checklist['created_by'],
                        ]);
                        $safeConductChecklistId = $this->db->insert_id();

                        foreach ($checklist['details'] as $detail) {
                            $this->safeConductChecklistDetail->create([
                                'id_safe_conduct_checklist' => $safeConductChecklistId,
                                'id_checklist' => $detail['id_checklist'],
                                'result' => $detail['result'],
                                'description' => $detail['description'],
                                'created_at' => $detail['created_at'],
                                'created_by' => $detail['created_by'],
                            ]);
                        }
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Entry permit for {$linkedTep['tep_code']} successfully updated by {$tep['tep_code']}");
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
            redirect('transporter-entry-permit');
        }
    }
}
