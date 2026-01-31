ALTER TABLE `cfg_puestos` 
    ADD `printer_id` INT NULL AFTER `impresora`, 
    ADD `tipo_cf` ENUM('cf_1g','cf_2g','ws','afip_ol') NULL AFTER `printer_id`, 
    ADD `estado` INT NOT NULL DEFAULT '1' AFTER `tipo_cf`; 