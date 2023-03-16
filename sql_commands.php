<?php 

// sql command to get all work, and their information, receipts, notes, and so on
$sql1 = 'SELECT work.id AS work_id, work.title AS work_title, work.description AS work_description, work.job_type AS work_job_type, 
        GROUP_CONCAT(DISTINCT work_time.start_datetime) AS start_datetime, GROUP_CONCAT(DISTINCT work_time.end_datetime) AS end_datetime,
        work_adjustment.adjustment AS work_adjustment, 
        GROUP_CONCAT(work_product.id) AS work_product_id, GROUP_CONCAT(work_product.quantity) AS work_product_quantity, 
        GROUP_CONCAT(product.id) AS product_id, GROUP_CONCAT(product.name) AS product_name, GROUP_CONCAT(product.description) AS product_description, 
        GROUP_CONCAT(product_price.price) AS product_price,
        GROUP_CONCAT(note.id) AS note_id, GROUP_CONCAT(note.title) AS note_title, GROUP_CONCAT(note.description) AS note_description
        
        From work
        LEFT JOIN work_time ON work.id = work_time.work_id
        INNER JOIN work_adjustment ON work.id = work_adjustment.work_id
        LEFT JOIN work_product ON work.id = work_product.work_id
        INNER JOIN product ON work_product.product_id = product.id
        INNER JOIN product_price ON product.id = product_price.product_id
        LEFT JOIN note ON work.id = note.work_id
        
        WHERE work_time.start_datetime BETWEEN ? AND ? OR work_time.end_datetime BETWEEN ? AND ?';

$sql2 = 'SELECT work.id AS work_id, work.title AS work_title, work.description AS work_description, work.job_type AS work_job_type, 
        work_adjustment.adjustment AS work_adjustment, 
        work_product.id AS work_product_id, work_product.quantity AS work_product_quantity, 
        product.id AS product_id, product.name AS product_name, product.description AS product_description, 
        product_price.price AS product_price
        
        From work
        INNER JOIN work_adjustment ON work.id = work_adjustment.work_id
        INNER JOIN work_product ON work.id = work_product.work_id
        INNER JOIN product ON work_product.product_id = product.id
        INNER JOIN product_price ON product.id = product_price.product_id
        INNER JOIN';

?>