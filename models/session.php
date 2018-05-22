<?php
include_once dirname(__FILE__) . '/oracle.php';

class Session extends oracle
{
    const TTL = 3600; // 1 hour

    /**
     * @var string
     */
    private $studentId = '';

    /**
     * @var string
     */
    private $token = '';

    /**
     * @var DateTime|null
     */
    private $expiry = null;

    /**
     * @return string
     */
    public function getStudentId(): string
    {
        return $this->studentId;
    }

    /**
     * @param string $studentId
     */
    private function setStudentId(string $studentId): void
    {
        $this->studentId = $studentId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    private function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return DateTime|null
     */
    public function getExpiry(): ?DateTime
    {
        return $this->expiry;
    }

    /**
     * @param DateTime|null $expiry
     */
    private function setExpiry(?DateTime $expiry): void
    {
        $this->expiry = $expiry;
    }

    public function extendExpiry()
    {
        $sql = "
            UPDATE SESSIONS s
            SET EXPIRY = current_timestamp + interval '" . self::TTL . "' second
            WHERE s.TOKEN = :token
        ";
        $params = ['token' => $this->token];

        $this->exec($sql, $params);
    }

    /**
     * Load by token
     *
     * @param string $token
     * @return $this|bool
     */
    public function loadByToken($token)
    {
        $sql = "
            SELECT *
            FROM SESSIONS s
            where s.TOKEN = :token
                  AND s.EXPIRY > current_timestamp
        ";
        $params = ['token' => $token];

        $result = $this->exec($sql, $params);

        foreach ($result as $row) {
            if ($row && $row['STUDENT_ID'] && $row['TOKEN']) {
                $this->setStudentId($row['STUDENT_ID']);
                $this->setToken($row['TOKEN']);
                if ($row['EXPIRY'])
                    $this->setExpiry(new DateTime($row['EXPIRY']));
                return $this;
            }
        }

        return false;
    }

    public function delete()
    {
        $sql = "
            DELETE SESSIONS s
            where s.TOKEN = :token
        ";

        $params = ['token' => $this->getToken()];
        $this->exec($sql, $params, false);
    }

    public static function generateToken()
    {
        return sha1(microtime());
    }

    public static function create($student_id)
    {
        $sql = "
            INSERT INTO SESSIONS
            (STUDENT_ID, TOKEN, EXPIRY)
            VALUES
              (:student_id, :token, current_timestamp + interval '" . self::TTL . "' second)
        ";

        $token = self::generateToken();
        $params = array(
            'student_id' => $student_id,
            'token' => $token,
        );

        $session = new self();
        $session->exec($sql, $params, false);

        return $session->loadByToken($token);
    }
}