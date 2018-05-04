DROP TABLE SESSIONS;
CREATE TABLE SESSIONS
(
  student_id varchar2(25) NOT NULL,
  token      varchar2(50) NOT NULL,
  exiry      date         NOT NULL
);