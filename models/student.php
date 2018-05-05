<?php

class Student
{
    public static function getAll()
    {
        $xmlString = file_get_contents(dirname(__FILE__) . "/../asset/students.xml");
        $xml = simplexml_load_string($xmlString);

        $students = array();

        foreach ($xml->student as $studentXml) {
            $student = array();

            foreach ($studentXml as $key => $value)
                $student[$key] = (String)$value;

            $students[] = $student;
        }

        return $students;
    }
}