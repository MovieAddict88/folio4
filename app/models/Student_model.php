<?php

class Student_model {
    private $table = 'students';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllStudent()
    {
        $this->db->query('SELECT * FROM ' . $this->table);
        return $this->db->resultSet();
    }

    public function getStudentById($id)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id=:id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function addStudent($data)
    {
        $query = "INSERT INTO students
                    VALUES
                  (null, :name, :email, :course)";

        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('course', $data['course']);

        $this->db->execute();

        return $this->db->rowCount();
    }

    public function deleteStudent($id)
    {
        $query = "DELETE FROM students WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();

        return $this->db->rowCount();
    }


    public function updateStudent($data)
    {
        $query = "UPDATE students SET
                    name = :name,
                    email = :email,
                    course = :course
                  WHERE id = :id";

        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('course', $data['course']);
        $this->db->bind('id', $data['id']);

        $this->db->execute();

        return $this->db->rowCount();
    }
}