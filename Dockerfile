FROM wnameless/oracle-xe-11g

ADD migration/credentials.sql /docker-entrypoint-initdb.d/
ADD migration/sessions.sql /docker-entrypoint-initdb.d/