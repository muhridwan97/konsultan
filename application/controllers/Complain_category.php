<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Complain_category
 * @property ComplainCategoryModel $complainCategory
 * @property Exporter $exporter
 */
class Complain_category extends MY_Controller
{
    /**
     * Complain_category constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ComplainCategoryModel', 'complainCategory');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show list of complain categories.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_VIEW);

        $complainCategories = $this->complainCategory->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Complain categories", $complainCategories);
        } else {
            $this->render('complain_category/index', compact('complainCategories'));
        }
    }

    /**
     * View single complain category by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_VIEW);

        $complainCategory = $this->complainCategory->getById($id);

        $this->render('complain_category/view', compact('complainCategory'));
    }

    /**
     * Show create complain category form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_CREATE);

        $this->render('complain_category/create');
    }

    /**
     * Show edit form complain category.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_EDIT);

        $complainCategory = $this->complainCategory->getById($id);

        $this->render('complain_category/edit', compact('complainCategory'));
    }

    /**
     * Set complain category data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'category' => 'trim|required|max_length[500]',
            'category_type' => 'trim|required',
            'value_type' => 'trim|required',
        ];
    }

    /**
     * Save data complain category.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_CREATE);

        if ($this->validate()) {
            $valueType = $this->input->post('value_type');
            $categoryType = $this->input->post('category_type');
            $category = $this->input->post('category');

            $save = $this->complainCategory->create([
                'value_type' => $valueType,
                'category_type' => $categoryType,
                'category' => $category,
            ]);

            if ($save) {
                flash('success', "Complain category {$category} successfully created", 'complain-category');
            } else {
                flash('danger', "Save complain category {$category} failed");
            }
        }
        $this->create();
    }

    /**
     * Update complain category data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_EDIT);

        $valueType = $this->input->post('value_type');
        $categoryType = $this->input->post('category_type');
        $category = $this->input->post('category');

        $update = $this->complainCategory->update([
            'value_type' => $valueType,
            'category_type' => $categoryType,
            'category' => $category,
        ], $id);

        if ($update) {
            flash('success', "Complain category {$category} updated", 'complain-category');
        } else {
            flash('danger', "Update complain category {$category}} failed");
        }
        $this->edit($id);
    }

    /**
     * Perform deleting data complain category.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_DELETE);

        $complain_category = $this->complainCategory->getById($id);

        if ($this->complainCategory->delete($id, true)) {
            flash('warning', "Complain category <strong> {$complain_category['category']} </strong> successfully deleted");
        } else {
            flash('danger', "Delete Complain category {$complain_category['category']} failed");
        }

        redirect('complain-category');
    }
}