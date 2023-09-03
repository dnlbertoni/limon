/*******************
Tabla wizard
*******************/

-- Agrega la nueva columna
ALTER TABLE wizard
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;

-- Actualiza los valores existentes
SET @row_number = 0;
UPDATE wizard
SET id = (@row_number := @row_number + 1);

set @max_number =0;
select @max_number := max(id)+1 from wizard ;

ALTER TABLE wizard AUTO_INCREMENT = @max_number;

/*******************
Tabla stk_empresas
*******************/
-- Agrega la nueva columna
ALTER TABLE stk_empresas
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;

-- Actualiza los valores existentes
SET @row_number = 0;
UPDATE stk_empresas
SET id = (@row_number := @row_number + 1);

set @max_number =0;
select @max_number := max(id)+1 from stk_empresas ;

ALTER TABLE stk_empresas AUTO_INCREMENT = @max_number;

/*******************
Tabla stk_ventasarticulo
*******************/
-- Agrega la nueva columna
ALTER TABLE stk_ventasarticulo
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;

-- Actualiza los valores existentes
SET @row_number = 0;
UPDATE stk_ventasarticulo
SET id = (@row_number := @row_number + 1);

set @max_number =0;
select @max_number := max(id)+1 from stk_ventasarticulo ;
ALTER TABLE stk_ventasarticulo AUTO_INCREMENT = @max_number;

/*******************
Tabla tbl_preciosmovim
*******************/
-- Agrega la nueva columna
ALTER TABLE tbl_preciosmovim
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;

-- Actualiza los valores existentes
SET @row_number = 0;
UPDATE tbl_preciosmovim
SET id = (@row_number := @row_number + 1);

set @max_number =0;
select @max_number := max(id)+1 from tbl_preciosmovim ;
ALTER TABLE tbl_preciosmovim AUTO_INCREMENT = @max_number;

/*******************
Tabla user_menu
*******************/
-- Agrega la nueva columna
ALTER TABLE user_menu
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;

-- Actualiza los valores existentes
SET @row_number = 0;
UPDATE user_menu
SET id = (@row_number := @row_number + 1);

set @max_number =0;
select @max_number := max(id)+1 from user_menu ;
ALTER TABLE user_menu AUTO_INCREMENT = @max_number;