CREATE TABLE users (
    user_id char(100) not null PRIMARY KEY,
    user_name  char(30) not null UNIQUE,
    user_email char(30) not null,
    user_password char(100) not null,
    is_admin tinyint(1) DEFAULT 0
);

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `is_admin`) VALUES
(1, 'admin', 'admin@web.com', 'Admin123!', 1);

CREATE TABLE entry (
  id varchar(64) NOT NULL PRIMARY KEY,
  upload_id varchar(64) NOT NULL,
  startedDateTime timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  wait int(11) NOT NULL,
  serverIPAddress varchar(32) NOT NULL,
  server_lat float(10,6) DEFAULT NULL,
  server_lng float(10,6) DEFAULT NULL
);

CREATE TABLE request (
  entry_id varchar(64) NOT NULL,
  method varchar(25) NOT NULL,
  url varchar(500) NOT NULL,
  headers longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`headers`))
); 

CREATE TABLE response (
  entry_id varchar(64) NOT NULL,
  status int(11) NOT NULL,
  statusText varchar(100) NOT NULL,
  headers longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`headers`))
); 

CREATE TABLE uploads (
  id varchar(64) NOT NULL,
  user_id int(11) NOT NULL,
  filename varchar(1000) NOT NULL,
  user_isp varchar(150) NOT NULL,
  user_lat float(10,6) NOT NULL,
  user_lng float(10,6) NOT NULL,
  created_at datetime DEFAULT current_timestamp()
); 