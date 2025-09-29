<?php

class Attendance_model {
    private $table = 'attendance';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function recordAttendance($student_id)
    {
        $query = "INSERT INTO " . $this->table . " (student_id) VALUES (:student_id)";
        $this->db->query($query);
        $this->db->bind('student_id', $student_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}