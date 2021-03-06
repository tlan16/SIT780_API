<?php

class Student
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $firstname
     */
    private $firstname;

    /**
     * @var string $lastname
     */
    private $lastname;

    /**
     * @var string $address
     */
    private $address;

    /**
     * @var array $students
     */
    private $students = array();

    /**
     * Student constructor.
     * @param string $id
     */
    public function __construct($id)
    {
        $this->students = self::getAll();
        if (isset($this->students[$this->id]))
            $this->load($id);
        $this->setId($id);
    }

    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function delete()
    {
        unset($this->students[$this->id]);
        $this->id = null;
    }

    /**
     * Save students
     */
    public function save()
    {
        if ($this->getId()) {
            // init new student
            if (!isset($this->students[$this->id]))
                $this->students[$this->id] = array(
                    'student_id' => $this->id,
                );

            $this->students[$this->id]['email'] = $this->email;
            $this->students[$this->id]['firstname'] = $this->firstname;
            $this->students[$this->id]['lastname'] = $this->lastname;
            $this->students[$this->id]['address'] = $this->address;
        }

        $this->writeToXml();
    }

    private function writeToXml()
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

            foreach ($studentXml as $key => $value) {
                $key = (String)$key;
                $student[$key] = (String)$value;
            }

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

    public static function getById($id)
    {
        $students = self::getAll();
        if ($students && isset($students[$id])) {
           $student = new self($id);
           $student->load($id);
           return $student;
        }
        return null;
    }

    public function load($id)
    {
        $students = self::getAll();
        if ($students && !empty($students[$id])) {
            $data = $students[$id];
            $this->setId($data['student_id']);
            $this->setEmail($data['email']);
            $this->setFirstname($data['firstname']);
            $this->setLastname($data['lastname']);
            $this->setAddress($data['address']);
            return $this;
        }
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'address' => $this->getAddress(),
        );
    }
}