<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{

    protected $modelName = 'App\Models\Account';
    protected $format    = 'json';

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $account = $this->model->find($id);

        if (!$account) {
            return $this->failNotFound('Account not found.');
        }

        return $this->respond($account);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $json = $this->request->getJSON();

        $rules = [
            'username' => 'required|is_unique[accounts.username]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'username' =>  $json->username,
            'password' => password_hash($json->password, PASSWORD_DEFAULT),
        ];

        $this->model->insert($data);

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Account successfully registered',
            'data' => $data
        ]);
    }

    public function login()
    {
        $json = $this->request->getJSON();
        
        $query = $this->model->query('SELECT * FROM accounts WHERE username = ?', [$json->username]);

        $acc = $query->getRow();

        if (!$acc) {
            return $this->response->setJSON(['error' => 'User not found'])->setStatusCode(404);
        }

        if (!password_verify($json->password, $acc->password)) {
            return $this->response->setJSON(['error' => 'Invalid password'])->setStatusCode(401);
        }

        // otentikasi JWT deliberately unapplied, I'm just junior :(
        // for demo application with all the functions, please visit my portofolio web in miscellaneous
        return $this->response->setJSON(['message' => 'Login successful', 'user' => $acc]);
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $account = $this->model->find($id);

        if (!$account) {
            return $this->failNotFound('Account not found.');
        }

        $data = [
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];

        $this->model->update($id, $data);

        return $this->respond([
            'status' => 'success',
            'message' => 'Password successfully updated'
        ]);
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $account = $this->model->find($id);

        if (!$account) {
            return $this->failNotFound('Account not found.');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Account successfully deleted'
        ]);
    }
}
