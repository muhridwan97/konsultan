<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class News
 * @property NewsModel $news
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property Exporter $exporter
 */
class News extends MY_Controller
{
    /**
     * News constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('NewsModel', 'news');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show news data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("News", $this->news->getAll());
        } else {
            $this->render('news/index');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $news = $this->news->getAll($filters);

        $this->render_json($news);
    }

    /**
     * Show view news form.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_VIEW);

        $news = $this->news->getById($id);

        $this->render('news/view', compact('news'));
    }

    /**
     * Show create news form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_CREATE);

        $this->render('news/create');
    }

    /**
     * Rule validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'title' => 'trim|required|max_length[200]',
            'content' => 'trim|required',
            'type' => 'trim|required|max_length[20]',
            'is_sticky' => 'trim',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Save new news.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_CREATE);

        if ($this->validate()) {
            $title = $this->input->post('title');
            $content = $this->input->post('content');
            $type = $this->input->post('type');
            $isPopup = $this->input->post('is_popup');
            $isSticky = if_empty($this->input->post('is_sticky'), 0);
            $description = $this->input->post('description');
            $expired_date = sql_date_format($this->input->post('expired_date'));

            // upload attachment if exist
            $fileName = '';
            $uploadPassed = true;
            if (!empty($_FILES['featured']['name'])) {
                $fileName = 'FE_' . time() . '_' . rand(100, 999);
                $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'news';
                if ($this->documentType->makeFolder('news')) {
                    $upload = $this->uploadDocumentFile->uploadTo('featured', $fileName, $saveTo);
                    if (!$upload['status']) {
                        $uploadPassed = false;
                        flash('warning', $upload['errors']);
                    } else {
                        $fileName = $upload['data']['file_name'];
                    }
                } else {
                    $uploadPassed = false;
                    flash('warning', 'Making folder upload failed, try again');
                }
            }

            if ($uploadPassed) {
                $save = $this->news->create([
                    'title' => $title,
                    'content' => $content,
                    'featured' => $fileName,
                    'type' => $type,
                    'is_popup' => $isPopup,
                    'expired_date' => $expired_date,
                    'is_sticky' => $isSticky,
                    'description' => $description,
                ]);

                if ($save) {
                    flash('success', "Readdress booking {$title} successfully created", 'news');
                } else {
                    flash('danger', 'Something is getting wrong, try again or contact administrator');
                }
            }
        }
        $this->create();
    }

    /**
     * Show edit news form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_EDIT);

        $news = $this->news->getById($id);

        $this->render('news/edit', compact('news'));
    }

    /**
     * Update data news by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_EDIT);

        if ($this->validate()) {
            $title = $this->input->post('title');
            $content = $this->input->post('content');
            $type = $this->input->post('type');
            $news_type = $this->input->post('news_type');
            $isPopup = $this->input->post('is_popup');
            $isSticky = if_empty($this->input->post('is_sticky'), 0);
            $description = $this->input->post('description');
            $expired_date = sql_date_format($this->input->post('expired_date'));

            $news = $this->news->getById($id);

            // upload attachment if exist, set default old name just in case the attachment does not change
            $fileName = $news['featured'];
            $uploadPassed = true;
            if (!empty($_FILES['featured']['name'])) {
                // setup location and file name
                $fileName = 'FE_' . time() . '_' . rand(100, 999);
                $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'news';

                // find or create base folder
                if ($this->documentType->makeFolder('news')) {
                    // try upload with standard config
                    $upload = $this->uploadDocumentFile->uploadTo('featured', $fileName, $saveTo);
                    if (!$upload['status']) {
                        $uploadPassed = false;
                        flash('warning', $upload['errors']);
                    } else {
                        // delete old file
                        if (!empty($fileName)) {
                            $this->uploadDocumentFile->deleteFile($news['featured'], $saveTo);
                        }
                        // put new file name
                        $fileName = $upload['data']['file_name'];
                    }
                } else {
                    $uploadPassed = false;
                    flash('warning', 'Folder news is missing or failed to be created, try again');
                }
            }

            if ($uploadPassed) {
                $update = $this->news->update([
                    'title' => $title,
                    'content' => $content,
                    'featured' => $fileName,
                    'type' => $type,
                    'is_popup' => $isPopup,
                    'is_sticky' => $isSticky,
                    'description' => $description,
                    'expired_date' => $expired_date,
                ], $id);

                if ($update) {
                    flash('success', "News <strong>{$title}</strong> successfully updated", 'news');
                } else {
                    flash('danger', "Update news <strong>{$title}</strong> failed, try again or contact administrator");
                }
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting news data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_NEWS_DELETE);

        $news = $this->news->getById($id);

        if ($this->news->delete($id, true)) {
            flash('warning', "News <strong>{$news['title']}</strong> successfully deleted");
        } else {
            flash('danger', "Delete news {$news['title']} failed");
        }
        redirect('news');
    }

}