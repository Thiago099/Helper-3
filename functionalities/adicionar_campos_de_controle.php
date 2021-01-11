<?php
$database = $_GET['database'];
$table = $_GET['table'];
$db=new sql($database);
$result=$db->run("ALTER TABLE `$database`.`$table`
                  ADD COLUMN IF NOT EXISTS `created_by` INT NULL,
                  ADD COLUMN IF NOT EXISTS `created_at` DATETIME NULL,
                  ADD COLUMN IF NOT EXISTS `updated_by` INT NULL,
                  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL,
                  ADD COLUMN IF NOT EXISTS `excluido` TINYINT(1) NULL DEFAULT NULL;");
$result=$db->run("ALTER TABLE `$database`.`$table`
                    ADD COLUMN IF NOT EXISTS `id` INT NOT NULL AUTO_INCREMENT FIRST,
                    ADD PRIMARY KEY (`id`);");
$result=$db->run("ALTER TABLE `$database`.`$table`
                    ADD CONSTRAINT `FK_{$table}_created_by` FOREIGN KEY (`created_by`) REFERENCES `usuario` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;");
$result=$db->run("ALTER TABLE `$database`.`$table`
                    ADD CONSTRAINT `FK_{$table}_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `usuario` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;");
?>
