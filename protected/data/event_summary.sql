SELECT e.id,
    CASE WHEN e.external_id IS NULL THEN 'Not set' ELSE e.external_id END AS external_id,
    CASE WHEN e.name IS NULL OR e.name = '' THEN 'Not set' ELSE e.name END as name,
    CASE WHEN e.description IS NULL OR e.description = '' THEN 'Not set' ELSE 'Yes' END as description,
    CASE WHEN e.tags IS NULL THEN 'Not set' ELSE 'Yes' END as tags,
    (SELECT a.name FROM arena a WHERE a.id = e.arena_id) AS arena,
    (SELECT l.name FROM location l WHERE l.id = e.location_id) AS location,
    CASE WHEN e.recurrence_id IS NULL OR e.recurrence_id = 0 THEN 'No' ELSE 'Yes' END AS recurrence,
    CASE WHEN e.all_day = 0 THEN 'No' ELSE 'Yes' END AS all_day,
    DATE_FORMAT(e.start_date, '%m/%d/%Y') AS start_date,
    DATE_FORMAT(e.start_time, '%h:%i %p') AS start_time,
    CASE WHEN e.duration = 0 THEN 'Not Set' ELSE CONCAT(e.duration, ' minutes') END AS duration,
    CASE WHEN e.end_date = '0000-00-00' THEN 'Not Set' ELSE DATE_FORMAT(e.end_date, '%m/%d/%Y') END AS end_date,
    CASE WHEN e.end_time = '00:00:00' THEN 'Not Set' ELSE DATE_FORMAT(e.end_time, '%h:%i %p') END AS end_time,
    CONCAT('$', FORMAT(e.price, 2)) AS price,
    CASE WHEN e.notes IS NULL THEN 'Not set' ELSE 'Yes' END AS notes,
    (SELECT t.display_name FROM event_type t WHERE t.id = e.type_id) AS type,
    (SELECT s.display_name FROM event_status s WHERE s.id = e.status_id) AS status,
    (SELECT COUNT(DISTINCT er.id) FROM event_request er WHERE er.event_id = e.id AND er.status_id IN 
        (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED'))) AS outstanding_event_requests,
    (SELECT COUNT(DISTINCT r.id) FROM reservation r WHERE r.event_id = e.id AND r.status_id IN 
        (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED'))) AS outstanding_reservations
FROM event e
    INNER JOIN arena a
    ON e.arena_id = a.id
    INNER JOIN arena_user_assignment aua
    ON a.id = aua.arena_id
    INNER JOIN user u
    ON u.id = aua.user_id
WHERE u.id = 2
ORDER BY e.start_date ASC

        $sql = "SELECT e.id, "
                . "CASE WHEN e.external_id IS NULL THEN 'Not set' ELSE e.external_id END AS external_id, "
                . "CASE WHEN e.name IS NULL OR e.name = '' THEN 'Not set' ELSE e.name END as name, "
                . "CASE WHEN e.description IS NULL OR e.description = '' THEN 'Not set' ELSE 'Yes' END as description, "
                . "CASE WHEN e.tags IS NULL THEN 'Not set' ELSE 'Yes' END as tags, "
                . "(SELECT a.name FROM arena a WHERE a.id = e.arena_id) AS arena, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id) AS location, "
                . "CASE WHEN e.recurrence_id IS NULL OR e.recurrence_id = 0 THEN 'No' ELSE 'Yes' END AS recurrence, "
                . "CASE WHEN e.all_day = 0 THEN 'No' ELSE 'Yes' END AS all_day, "
                . "DATE_FORMAT(e.start_date, '%m/%d/%Y') AS start_date, "
                . "DATE_FORMAT(e.start_time, '%h:%i %p') AS start_time, "
                . "CASE WHEN e.duration = 0 THEN 'Not Set' ELSE CONCAT(e.duration, ' minutes') END AS duration, "
                . "CASE WHEN e.end_date = '0000-00-00' THEN 'Not Set' ELSE DATE_FORMAT(e.end_date, '%m/%d/%Y') END AS end_date, "
                . "CASE WHEN e.end_time = '00:00:00' THEN 'Not Set' ELSE DATE_FORMAT(e.end_time, '%h:%i %p') END AS end_time, "
                . "CONCAT('$', FORMAT(e.price, 2)) AS price, "
                . "CASE WHEN e.notes IS NULL THEN 'Not set' ELSE 'Yes' END AS notes, "
                . "(SELECT t.display_name FROM event_type t WHERE t.id = e.type_id) AS type, "
                . "(SELECT s.display_name FROM event_status s WHERE s.id = e.status_id) AS status, "
                . "(SELECT COUNT(DISTINCT er.id) FROM event_request er WHERE er.event_id = e.id AND er.status_id IN "
                . "    (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED'))) AS outstanding_event_requests, "
                . "(SELECT COUNT(DISTINCT r.id) FROM reservation r WHERE r.event_id = e.id AND r.status_id IN "
                . "    (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED'))) AS outstanding_reservations "
                . "FROM event e "
                . "    INNER JOIN arena a "
                . "    ON e.arena_id = a.id "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "    WHERE u.id = :uid ";

        
        if($aid !== null) {
            $sql .= "AND e.arena_id = :aid ";
        }
        
        if($from !== null) {
            $sql .= "AND e.start_date >= :from ";
        }
        
        if($to !== null) {
            $sql .= "AND e.start_date <= :to ";
        }
        
        if($tid !== null) {
            $sql .= "AND e.type_id = :tid ";
        }
        
        if($sid !== null) {
            $sql .= "AND e.status_id = :sid ";
        }
        
        $sql .= "ORDER BY e.start_date ASC";
        
