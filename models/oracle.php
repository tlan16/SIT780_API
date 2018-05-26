<?php

abstract class oracle
{
    /**
     * @var resource
     */
    private $connection;

    /**
     * oracle constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $conn = oci_connect(
            getDotEnv('DB_USERNAME'),
            getDotEnv('DB_PASSWORD'),
            getDotEnv('DB_HOST'));

        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            throw new Exception($e['message']);
        }

        $this->connection = $conn;
    }

    protected function exec($sql, $params, $fetch = true)
    {
        $stid = oci_parse($this->getConnection(), $sql);
        foreach ($params as $key => $value)
            oci_bind_by_name($stid, ":$key", $params[$key]);
        oci_execute($stid);

        $results = array();
        if ($fetch)
            while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS))
                $results[] = $row;

        return $results;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

}