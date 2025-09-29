<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class Qrcode extends Controller {
    public function generate($id)
    {
        $student = $this->model('Student_model')->getStudentById($id);

        if ($student) {
            $data = json_encode($student);

            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'   => QRCode::ECC_L,
                'scale'      => 10,
            ]);

            header('Content-Type: image/png');
            echo (new QRCode($options))->render($data);
        }
    }

    public function scan()
    {
        $data['title'] = 'Scan QR Code';
        $this->view('templates/header', $data);
        $this->view('qrcode/scan');
        $this->view('templates/footer');
    }

    public function process_scan()
    {
        if (isset($_POST['qrcode'])) {
            $qr_data = $_POST['qrcode'];
            $student_data = json_decode($qr_data, true);

            if (isset($student_data['id'])) {
                $student_id = $student_data['id'];
                if ($this->model('Attendance_model')->recordAttendance($student_id) > 0) {
                    Flasher::setFlash('Attendance recorded for student ' . $student_data['name'], 'successfully', 'success');
                } else {
                    Flasher::setFlash('Failed to record attendance', 'error', 'danger');
                }
            } else {
                Flasher::setFlash('Invalid QR Code', 'error', 'danger');
            }
        } else {
            Flasher::setFlash('No QR Code data received', 'error', 'danger');
        }

        header('Location: ' . BASEURL . '/qrcode/scan');
        exit;
    }
}