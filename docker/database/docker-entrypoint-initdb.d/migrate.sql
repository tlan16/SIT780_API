CREATE TABLE SYSTEM.CREDENTIALS
(
  student_id varchar2(25)        NOT NULL,
  password   varchar2(40)        NOT NULL,
  is_admin   NUMBER(1) DEFAULT 0 NOT NULL
);

CREATE TABLE SYSTEM.SESSIONS
(
  student_id varchar2(25) NOT NULL,
  token      varchar2(50) NOT NULL,
  expiry     date         NOT NULL
);

COMMIT;

INSERT INTO SYSTEM.CREDENTIALS (
  STUDENT_ID,
  PASSWORD,
  IS_ADMIN
) VALUES (
  'lanti',
  '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',
  1
);

COMMIT;
