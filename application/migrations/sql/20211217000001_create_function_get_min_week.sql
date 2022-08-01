DROP FUNCTION IF EXISTS get_min_week;
DELIMITER $$

    CREATE FUNCTION get_min_week (year_week VARCHAR(10)) RETURNS DATE
    BEGIN
        DECLARE tanggal DATE;
        SET tanggal = (SELECT MIN(ref_dates.date) FROM ref_dates
        WHERE ref_dates.year_week = year_week);
        
        RETURN tanggal;
    END$$

DELIMITER ;