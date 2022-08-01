<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Raw_contact
 * @property RawContactModel rawContact
 * @property InvoiceModel invoice
 * @property Exporter exporter
 */
class Raw_contact extends MY_Controller
{
    /**
     * Raw_contact constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('RawContactModel', 'rawContact');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'export' => 'GET',
            'invoice' => 'GET',
        ]);
    }

    /**
     * Show raw contact data list.
     */
    public function index()
    {
        $this->render('raw_contact/index');
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $contacts = $this->rawContact->getAll($filters);
        $this->render_json($contacts);
    }

    /**
     * Export raw contact to excel.
     */
    public function export()
    {
        $contacts = $this->rawContact->getAll();
        $this->exporter->exportFromArray('Raw customer contact', $contacts);
    }

    /**
     * Show view raw contact form.
     *
     * @param $id
     */
    public function view($id)
    {
        $rawContact = $this->rawContact->getById($id);

        $this->render('raw_contact/view', compact('rawContact'));
    }

    /**
     * Show invoice that created by raw contact.
     *
     * @param $id
     */
    public function invoice($id)
    {
        $rawContact = $this->rawContact->getById($id);
        $invoices = $this->invoice->getInvoicesByRawContact($id);

        $this->render('raw_contact/invoice', compact('rawContact', 'invoices'));
    }

    /**
     * Perform deleting raw contact data.
     *
     * @param $id
     */
    public function delete($id)
    {
        $rawContactData = $this->rawContact->getById($id);

        if ($this->rawContact->delete($id)) {
            flash('warning', "Contact <strong>{$rawContactData['company']}</strong> successfully deleted");
        } else {
            flash('danger', "Delete raw contact <strong>{$rawContactData['company']}</strong> failed");
        }
        redirect('raw_contact');
    }

}