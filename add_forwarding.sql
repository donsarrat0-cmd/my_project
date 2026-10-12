USE doc_tracker;
ALTER TABLE receiving_records ADD INDEX idx_doc(document_id);
ALTER TABLE receiving_records DROP INDEX document_id;
