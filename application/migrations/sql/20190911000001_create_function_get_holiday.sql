DROP FUNCTION IF EXISTS get_holiday;
DELIMITER $$

    CREATE FUNCTION get_holiday (date_create DATE) RETURNS INT(11)
    BEGIN
        DECLARE total INT(11) DEFAULT 0;
        DECLARE `status` BOOLEAN DEFAULT FALSE;

        holiday_loop: LOOP    
            SELECT COUNT(holiday.date) FROM ".env('DB_HR_DATABASE') . ".schedule_holidays AS holiday WHERE
                holiday.date = date_create INTO `status`;    
                
            IF `status`=0 THEN
                LEAVE holiday_loop;
            END IF;
            IF total>30 THEN
                LEAVE holiday_loop;
            END IF;
            SET total = total + 1;         
            SET date_create = DATE_ADD(DATE(date_create), INTERVAL 1 DAY);    
        END LOOP holiday_loop;
        
        RETURN total;
    END$$

DELIMITER ;