<?php

class Student
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var array $students
     */
    private $students = array();

    /**
     * Student constructor.
     * @param string $id
     *
     * @throws Exception
     */
    public function __construct($id)
    {
        $this->students = self::getAll();

        if (!$this->students[$id])
            throw new Exception("student with id `$id` does not exist.");

        $this->id = $id;
    }

    public function delete()
    {
        unset($this->students[$this->id]);
    }

    public function save()
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        xmlwriter_set_indent_string($xw, ' ');

        xmlwriter_start_document($xw, '1.0', 'UTF-8');

        xmlwriter_start_element($xw, 'students');

        foreach ($this->students as $student) {
            // Start a child element
            xmlwriter_start_element($xw, 'student');

            xmlwriter_start_element($xw, 'student_id');
            xmlwriter_text($xw, $student['student_id']);
            xmlwriter_end_element($xw); // student_id

            xmlwriter_start_element($xw, 'email');
            xmlwriter_text($xw, $student['email']);
            xmlwriter_end_element($xw); // email

            xmlwriter_start_element($xw, 'firstname');
            xmlwriter_text($xw, $student['firstname']);
            xmlwriter_end_element($xw); // firstname

            xmlwriter_start_element($xw, 'lastname');
            xmlwriter_text($xw, $student['lastname']);
            xmlwriter_end_element($xw); // lastname

            xmlwriter_start_element($xw, 'address');
            xmlwriter_text($xw, $student['address']);
            xmlwriter_end_element($xw); // address

            xmlwriter_end_element($xw); // student
        }

        xmlwriter_end_element($xw); // students

        xmlwriter_end_document($xw);

        file_put_contents(self::getDataFilePath(), xmlwriter_output_memory($xw));
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        $xmlString = file_get_contents(self::getDataFilePath());
        $xml = simplexml_load_string($xmlString);

        $students = array();

        foreach ($xml->student as $studentXml) {
            $student = array();

            foreach ($studentXml as $key => $value)
                $student[$key] = (String)$value;

            if (!$student['student_id'])
                continue;
            $students[$student['student_id']] = $student;
        }

        return $students;
    }

    private static function getDataFilePath()
    {
        return dirname(__FILE__) . "/../asset/students.xml";
    }
}