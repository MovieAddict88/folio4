<?php

class Admin extends Controller {
    public function index()
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        $data['title'] = 'Admin';
        $data['students'] = $this->model('Student_model')->getAllStudent();
        $this->view('templates/header', $data);
        $this->view('admin/index', $data);
        $this->view('templates/footer');
    }

    public function add()
    {
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $course = htmlspecialchars(trim($_POST['course']), ENT_QUOTES, 'UTF-8');

        if (empty($name) || empty($email) || empty($course) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flasher::setFlash('All fields are required and email must be valid.', 'error', 'danger');
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'course' => $course
        ];

        if ($this->model('Student_model')->addStudent($data) > 0) {
            Flasher::setFlash('Student data', 'added', 'success');
        } else {
            Flasher::setFlash('Failed to add student data', '', 'danger');
        }
        header('Location: ' . BASEURL . '/admin');
        exit;
    }

    public function delete($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            Flasher::setFlash('Invalid request.', '', 'danger');
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        if ($this->model('Student_model')->deleteStudent($id) > 0) {
            Flasher::setFlash('Student data', 'deleted', 'success');
        } else {
            Flasher::setFlash('Failed to delete student data', '', 'danger');
        }
        header('Location: ' . BASEURL . '/admin');
        exit;
    }

    public function getUpdate()
    {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid ID']);
            exit;
        }
        echo json_encode($this->model('Student_model')->getStudentById($id));
    }

    public function update()
    {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $course = htmlspecialchars(trim($_POST['course']), ENT_QUOTES, 'UTF-8');

        if ($id === false || empty($name) || empty($email) || empty($course) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flasher::setFlash('All fields are required and email must be valid.', 'error', 'danger');
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        $data = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'course' => $course
        ];

        if ($this->model('Student_model')->updateStudent($data) > 0) {
            Flasher::setFlash('Student data', 'updated', 'success');
        } else {
            // Note: rowCount() returns 0 on successful update if no rows were changed.
            // A more robust check might be needed here if this is an issue.
            Flasher::setFlash('Student data was not changed', '', 'info');
        }
        header('Location: ' . BASEURL . '/admin');
        exit;
    }
}