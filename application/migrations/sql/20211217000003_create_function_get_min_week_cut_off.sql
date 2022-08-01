DROP FUNCTION IF EXISTS get_min_week_cut_off;
DELIMITER $$

    CREATE FUNCTION get_min_week_cut_off (year_week VARCHAR(10), branch INT(11)) RETURNS TIMESTAMP
    BEGIN
        DECLARE date_time TIMESTAMP;
        SET date_time = (SELECT MIN(CONCAT(get_min_week(year_week), ' ',cut_off.start)) AS START FROM ref_operation_cut_offs cut_off
            WHERE id_branch = branch AND STATUS = 'ACTIVE'
            GROUP BY id_branch);
        
        RETURN date_time;
    END$$

DELIMITER ;