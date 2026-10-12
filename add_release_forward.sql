USE doc_tracker;
ALTER TABLE documents MODIFY status ENUM('Pending Receiving','Received','Forwarded','Released','Completed') NOT NULL DEFAULT 'Received';
UPDATE documents SET status='Released' WHERE status='Pending Receiving';
ALTER TABLE documents MODIFY status ENUM('Received','Forwarded','Released','Completed') NOT NULL DEFAULT 'Received';
