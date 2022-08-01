<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Attachment_photo
 * @property AttachmentPhotoModel $attachmentPhoto
 * @property Exporter $exporter
 */
class Attachment_photo extends MY_Controller
{
    /**
     * Attachment photo constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('AttachmentPhotoModel', 'attachmentPhoto');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show handling attachment Photo data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_VIEW);

        $attachmentPhotos = $this->attachmentPhoto->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Attachment photos", $attachmentPhotos);
        } else {
            $this->render('attachment_photo/index', compact('attachmentPhotos'));
        }
    }

    /**
     * Show detail attachment photo.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_VIEW);

        $attachmentPhoto = $this->attachmentPhoto->getById($id);

        $this->render('attachment_photo/view', compact('attachmentPhoto'));
    }

    /**
     * Show create attachment photo form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_CREATE);

        $this->render('attachment_photo/create');
    }

    /**
     * Base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'photo_name' => 'trim|required|max_length[100]',
            'description' => 'trim|required|max_length[500]'
        ];
    }

    /**
     * Save new attachment photo.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_CREATE);

        if ($this->validate()) {
            $photoName = $this->input->post('photo_name');
            $description = $this->input->post('description');

            $save = $this->attachmentPhoto->create([
                'photo_name' => $photoName,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Attachment photo {$photoName} successfully created", 'attachment-photo');
            } else {
                flash('danger', "Save attachment photo {$photoName} failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit attachment photo form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_EDIT);

        $attachmentPhoto = $this->attachmentPhoto->getById($id);

        $this->render('attachment_photo/edit', compact('attachmentPhoto'));
    }

    /**
     * Update data branch by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_EDIT);

        if ($this->validate()) {
            $photoName = $this->input->post('photo_name');
            $description = $this->input->post('description');

            $update = $this->attachmentPhoto->update([
                'photo_name' => $photoName,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Attachment Photo {$photoName} successfully updated", 'attachment-photo');
            } else {
                flash('danger', "Update attachment photo {$photoName} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting attachment photo data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ATTACHMENT_PHOTO_DELETE);

        $attachmentPhoto = $this->attachmentPhoto->getById($id);

        if ($this->attachmentPhoto->delete($id)) {
            flash('warning', "Attachment photo {$attachmentPhoto['attachment_photo']} successfully deleted");
        } else {
            flash('danger', "Delete attachment photo {$attachmentPhoto['attachment_photo']} failed");
        }

        redirect('attachment-photo');
    }

}