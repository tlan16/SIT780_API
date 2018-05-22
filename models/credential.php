<?php
include_once dirname(__FILE__) . '/oracle.php';

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
     * @return string
     */
    public function getStudentId(): string
    {
        return $this->studentId;
    }

    /**
     * @param string $username
     */
    private function setUsername(string $username): void
    {
        $this->studentId = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    private function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function load($studentId)
    {
        $sql = "
            SELECT *
              FROM CREDENTIALS c
            WHERE c.STUDENT_ID = :student_id
        ";
        $params = ['student_id' => $studentId];

        $results = $this->exec($sql, $params);

        foreach ($results as $row) {
            if ($row && !empty($row['STUDENT_ID']) && !empty($row['PASSWORD'])) {
                $this->setUsername($row['STUDENT_ID']);
                $this->setPassword($row['PASSWORD']);
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
}