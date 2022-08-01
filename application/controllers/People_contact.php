<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class People_contact
 * @property PeopleModel $people
 * @property PeopleContactModel $peopleContact
 */
class People_contact extends CI_Controller
{
    /**
     * People Contact constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PeopleModel', 'people');
        $this->load->model('PeopleContactModel', 'peopleContact');
    }

    public function create($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PEOPLE_CREATE);

        $person = $this->people->getById($id);
        $data = [
            'title' => 'Contacts',
            'subtitle' => 'Create contacts',
            'page' => 'people_contact/create',
            'person' => $person
        ];
        $this->load->view('template/layout', $data);
    }

    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PEOPLE_EDIT);

        $contact = $this->peopleContact->getContactById($id);
        $data = [
            'title' => 'Contacts',
            'subtitle' => 'Edit contacts',
            'page' => 'people_contact/edit',
            'contact' => $contact
        ];
        $this->load->view('template/layout', $data);
    }

    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PEOPLE_CREATE);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('id_person', 'ID Person', 'trim|required');
            $personId = $this->input->post('id_person');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $name = $this->input->post('contact_name');
                $occupation = $this->input->post('occupation');
                $phone = $this->input->post('phone');
                $email = $this->input->post('email');
                $address = $this->input->post('address');

                if (!empty($name) || !empty($occupation) || !empty($phone) || !empty($email) || !empty($address)) {
                    $this->peopleContact->insertContact([
                        'id_person' => $personId,
                        'name' => $name,
                        'occupation' => $occupation,
                        'phone' => $phone,
                        'email' => $email,
                        'address' => $address
                    ]);

                    redirect("people/view/" . $personId);
                } else {
                    flash('warning', 'Contact can not be empty');
                    $this->create($personId);
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect("people");
    }

    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PEOPLE_EDIT);

        $contact = $this->peopleContact->getContactById($id);
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $name = $this->input->post('contact_name');
            $occupation = $this->input->post('occupation');
            $phone = $this->input->post('phone');
            $email = $this->input->post('email');
            $address = $this->input->post('address');

            if (!empty($name) || !empty($occupation) || !empty($phone) || !empty($email) || !empty($address)) {
                $this->peopleContact->updateContact([
                    'name' => $name,
                    'occupation' => $occupation,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $address
                ], $id);
                redirect("people/view/" . $contact['id_person']);
            } else {
                flash('warning', 'Contact can not be empty');
                $this->edit($id);
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
            $this->edit($id);
        }
    }

    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PEOPLE_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $contact = $this->peopleContact->getContactById($id);

            if ($this->peopleContact->deleteContact($id)) {
                flash('warning', "Contact <strong>{$contact['name']}</strong> successfully deleted");
            } else {
                flash('danger', "Delete contact <strong>{$contact['name']}</strong> failed, try again or contact administrator");
            }
            redirect('people/view/' . $contact['id_person']);
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('people');
    }
}