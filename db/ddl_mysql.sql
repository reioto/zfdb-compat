drop table if exists zftest cascade;

create table zftest (
  id INT unsigned AUTO_INCREMENT not null
  , name VARCHAR(64)
  , number INT
  , deci_number DECIMAL(3,3)
  , constraint zftest_PKC primary key (id)
) default character set utf8 engine innodb;
