DROP FUNCTION IF EXISTS get_max_week;
DELIMITER $$

    CREATE FUNCTION get_max_week (year_week VARCHAR(10)) RETURNS DATE
    BEGIN
        DECLARE tanggal DATE;
        SET tanggal = (SELECT MAX(ref_dates.date) FROM ref_dates
        WHERE ref_dates.year_week = year_week);
        
        RETURN tanggal;
    END$$

DELIMITER ;