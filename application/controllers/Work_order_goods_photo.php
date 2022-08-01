<?php

use Intervention\Image\Image;
use Intervention\Image\ImageManager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order_goods_photo
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderGoodsPhotoModel $workOrderGoodsPhoto
 * @property Uploader $uploader
 */
class Work_order_goods_photo extends MY_Controller
{
    /**
     * Work_order_goods_photo constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderGoodsPhotoModel', 'workOrderGoodsPhoto');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('WorkOrderModel', 'workOrder');

        $this->setFilterMethods([
            'ajax_photos' => 'GET',
            'edit_by_tally' => 'GET',
            'save_by_tally' => 'POST',
        ]);
    }

    /**
     * Save work order document.
     *
     * @param $id
     */
    public function save($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_TAKE_JOB);

        if ($this->validate(['input_files_uploaded[]' => 'required'])) {
            $files = $this->input->post('input_files_uploaded');
            $descriptions = $this->input->post('photo_description');

            $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsById($id);

            $this->db->trans_start();

            foreach ($files as $index => $file) {
                if (!empty($file)) {
                    $sourceFile = 'temp/' . $file;
                    $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                    if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                        $this->workOrderGoodsPhoto->create([
                            'id_work_order_goods' => $id,
                            'src' => $destFile,
                            'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                            'description' => get_if_exist($descriptions, $index, null)
                        ]);

                        $tempText = $workOrderGoods['no_work_order'] . "\nUPLOAD AT " . date('d M Y H:i:s') . "\nUPLOADED BY " . strtoupper(UserModel::authenticatedUserData('username')) . "\n" . $workOrderGoods['goods_name'];
                        $this->watermark($destFile, $file, $tempText);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Work order goods photo successfully created", 'work-order/view/' . $workOrderGoods['id_work_order']);
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit($id);
    }

    /**
     * Save work order document.
     *
     * @param $id
     */
    public function save_by_tally($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_TAKE_JOB);

        if ($this->validate(['input_files_uploaded[]' => 'required'])) {
            $files = $this->input->post('input_files_uploaded');
            $descriptions = $this->input->post('photo_description');

            $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsById($id);

            $this->db->trans_start();

            foreach ($files as $index => $file) {
                if (!empty($file)) {
                    $sourceFile = 'temp/' . $file;
                    $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                    if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                        $this->workOrderGoodsPhoto->create([
                            'id_work_order_goods' => $id,
                            'src' => $destFile,
                            'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                            'description' => get_if_exist($descriptions, $index, null)
                        ]);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Work order goods photo successfully created", 'work-order/view-upload/' . $workOrderGoods['id_work_order']);
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit_by_tally($id);
    }

    /**
     * Edit work order goods photo.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_CREATE);

        $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsById($id);
        $workOrderGoodsPhotos = $this->workOrderGoodsPhoto->getBy(['id_work_order_goods' => $id]);
        $workOrderStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderGoods['id_work_order'],
            'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_APPROVED
        ]);

        $this->render('tally/photo', compact('workOrderGoodsPhotos', 'workOrderGoods', 'workOrderStatuses'));
    }

    /**
     * Edit work order goods photo.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VIEW_PHOTO);

        $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsById($id);
        $workOrderGoodsPhotos = $this->workOrderGoodsPhoto->getBy(['id_work_order_goods' => $id]);
        $workOrderStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderGoods['id_work_order'],
            'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_APPROVED
        ]);

        $this->render('tally/photo', compact('workOrderGoodsPhotos', 'workOrderGoods', 'workOrderStatuses'));
    }

    /**
     * Edit work order goods photo by tally .
     *
     * @param $id
     */
    public function edit_by_tally($id)
    {
        $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsById($id);
        $workOrderGoodsPhotos = $this->workOrderGoodsPhoto->getBy(['id_work_order_goods' => $id]);

        $this->render('tally/photo_tally', compact('workOrderGoodsPhotos', 'workOrderGoods'));
    }

    /**
     * Perform deleting auction data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_DELETE);

        $workOrderGoodsPhoto = $this->workOrderGoodsPhoto->getById($id);
        $delete = $this->workOrderGoodsPhoto->delete($id);

        if($this->input->is_ajax_request()) {
            //$this->uploader->delete($workOrderGoodsPhoto['src']); // delete local
            $this->uploader->setDriver('s3')->delete($workOrderGoodsPhoto['src']); // delete s3
            $this->render_json($delete ? $workOrderGoodsPhoto : ['status' => 'danger', 'message' => "Delete photo " . basename($workOrderGoodsPhoto['src']) . " failed"]);
        } else {
            if ($delete) {
                flash('warning', "Photo " . basename($workOrderGoodsPhoto['src']) . " is successfully deleted");
            } else {
                flash('danger', "Delete photo " . basename($workOrderGoodsPhoto['src']) . " failed");
            }
            redirect('work-order-goods-photo/edit/' . $workOrderGoodsPhoto['id_work_order_goods']);
        }
    }

    /**
     * Add watermark to photo
     * 
     * @param string $path
     * @param string $photo_name
     * @param string $hasilText
     */
    public function watermark($path, $photo_name, $hasilText)
    {
        $manager = new ImageManager();

        $img = $manager->make(asset_url($path));
        $width = 2000;
        $height = null;
        if ($img->getHeight() > $img->getWidth()) {
            $height = 2000;
            $width = null;
        }

        // now you are able to resize the instance
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        // and insert a watermark for example
        if ($img->getHeight() > $img->getWidth()) {
            $watermark = $manager->make('assets/app/img/layout/watermark2.png');
            $watermark->resize(550, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $watermark = $manager->make('assets/app/img/layout/watermark1.png');
            $watermark->resize(850, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $img->insert($watermark, 'top-right', 1, 1);

        $img->text($hasilText, 50, $img->getHeight() - 290, function ($font) {
            $font->file(FCPATH . 'assets/plugins/font-googleapis/fonts/SourceSansPro-Bold.ttf');
            $font->size(65);
            $font->color('#FFFF00');
            $font->align('left');
            $font->valign('middle');
        });
        $data = $img->exif();

        $result = $this->s3FileManager->putObjectStream(env('S3_BUCKET'), $path, $img->stream(), $img->mime());
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get work order goods photo
     */
    public function ajax_photos()
    {
        $workOrderGoodsId = get_url_param('id_work_order_goods');
        $workOrderGoodsPhotos = $this->workOrderGoodsPhoto->getBy(['id_work_order_goods' => $workOrderGoodsId]);
        foreach ($workOrderGoodsPhotos as &$workOrderGoodsPhoto) {
            $workOrderGoodsPhoto['url'] = asset_url($workOrderGoodsPhoto['src']);
        }

        $this->render_json($workOrderGoodsPhotos);
    }
}