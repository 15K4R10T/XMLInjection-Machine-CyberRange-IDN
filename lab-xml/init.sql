-- =============================================
-- Lab XML Injection - Database Initialization
-- =============================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    secret VARCHAR(200)
);

INSERT INTO users (username, password, email, role, secret) VALUES
('admin',   'admin123',     'admin@lab.local',   'admin', 'FLAG{xml_injection_found_admin}'),
('alice',   'alice_pass',   'alice@lab.local',   'user',  'FLAG{xpath_alice_extracted}'),
('bob',     'b0b_s3cr3t',   'bob@lab.local',     'user',  'FLAG{xpath_bob_extracted}'),
('charlie', 'ch@rlie99',    'charlie@lab.local', 'user',  'FLAG{xpath_charlie_extracted}'),
('sysadm',  'Sup3rS3cr3t!', 'sys@lab.local',     'admin', 'FLAG{hidden_sysadmin_account}');

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    category VARCHAR(50),
    price DECIMAL(10,2),
    stock INT DEFAULT 0
);

INSERT INTO products (name, category, price, stock) VALUES
('Laptop Pro X',      'Electronics', 18500000, 12),
('Wireless Mouse',    'Electronics',   275000, 85),
('Mechanical Keyboard','Electronics',  950000, 34),
('USB-C Hub 7-Port',  'Electronics',   195000, 60),
('Monitor 27 inch',   'Electronics', 4200000,  8),
('Classified Asset',  'Restricted',  9999999,  1);

CREATE TABLE IF NOT EXISTS flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flag_name VARCHAR(100),
    flag_value VARCHAR(200),
    hint TEXT
);

INSERT INTO flags (flag_name, flag_value, hint) VALUES
('xxe_flag_1', 'FLAG{xxe_file_read_success}',        'Baca file /etc/passwd via XXE'),
('xxe_flag_2', 'FLAG{xxe_internal_network_probe}',   'Probe internal service via XXE SSRF'),
('xml_flag_1', 'FLAG{basic_xml_tag_injection}',      'Inject tag XML pada field nama'),
('xpath_flag', 'FLAG{xpath_auth_bypass_complete}',   'Bypass login via XPath injection');

CREATE TABLE IF NOT EXISTS xml_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submitted_at DATETIME DEFAULT NOW(),
    payload TEXT,
    parsed_result TEXT
);
