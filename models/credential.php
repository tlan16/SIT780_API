<?php
include_once dirname(__FILE__) . '/oracle.php';
include_once dirname(__FILE__) . '/../models/student.php';

class Credential extends oracle
{
    /**
     * @var string
     */
    private $studentId = '';

    /**
     * @var string
     */
    private $password = '';

    /**
     * @var bool
     */
    private $isAdmin = false;

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $username
     */
    private function setUsername($username)
    {
        $this->studentId = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    private function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    private function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    public function load($studentId)
    {
        $sql = "
            SELECT *
              FROM CREDENTIALS c
            WHERE c.STUDENT_ID = :student_id
        ";
        $params = array('student_id' => $studentId);

        $results = $this->exec($sql, $params);

        foreach ($results as $row) {
            if ($row && !empty($row['STUDENT_ID']) && !empty($row['PASSWORD'])) {
                $this->setUsername($row['STUDENT_ID']);
                $this->setPassword($row['PASSWORD']);
                $this->setIsAdmin((bool)$row['IS_ADMIN']);
                return $this;
            }
        }

        return false;
    }

    public function verifyPassword($userInput)
    {
        if (empty($userInput) || empty($this->password))
            return false;
        return passwordVerify($userInput, $this->getPassword());
    }

    public function toArray($extra)
    {
        $result = array(
            'studentId' => $this->getStudentId(),
            'isAdmin' => $this->isAdmin(),
            'student' => null,
        );

        $student = $this->getStudent();
        if ($student instanceof Student)
            $result['student'] = $student->toArray();

        if (is_array($extra))
            $result = array_merge($result, $extra);

        return $result;
    }

    public function getStudent()
    {
        $student = Student::getById($this->studentId);
        return $student->load($this->studentId);
    }
}