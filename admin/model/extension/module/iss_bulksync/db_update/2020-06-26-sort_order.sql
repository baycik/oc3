ALTER TABLE `iss_sync_entries` 
ADD COLUMN `sort_order` VARCHAR(45) NULL AFTER `stock_status`;
