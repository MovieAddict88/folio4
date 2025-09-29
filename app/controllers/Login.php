<?php

class Login extends Controller {
    public function index()
    {
        $data['title'] = 'Login';
        $this->view('templates/header', $data);
        $this->view('login/index');
        $this->view('templates/footer');
    }

    public function process()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $admin = $this->model('Admin_model')->getAdminByUsername($username);

        if ($admin) {
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin'] = true;
                header('Location: ' . BASEURL . '/admin');
            } else {
                header('Location: ' . BASEURL . '/login');
            }
        } else {
            header('Location: ' . BASEURL . '/login');
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASEURL . '/login');
    }
}