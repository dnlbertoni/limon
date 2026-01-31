use facturador; 

UPDATE `cfg_puestos` SET    `nombre` = 'docker_desa', 
                            `ip` = '172.26.0.1', 
                            `puesto_cf` = '5', 
                            `puesto_cnf` = '0095', 
                            `puerto_cf` = '192.168.10.106/fiscal.json', 
                            `printer_id` = '01', 
                            `tipo_cf` = 'cf_2g' 
    WHERE `cfg_puestos`.`id` = 2; 
    
UPDATE `cfg_puestos` SET    `printer_id` = '01', 
                            `tipo_cf` = 'cf_1g' 
    WHERE `cfg_puestos`.`id` = 1; 
