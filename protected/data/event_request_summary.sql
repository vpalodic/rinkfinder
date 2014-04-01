SELECT er.id,
    e.id AS event_id,
    CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start,
    a.name AS arena,
    a.id AS arena_id,
    (SELECT l.name FROM location l WHERE l.id = e.location_id) AS location,
    (SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id,
    (SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.requester_id = p.user_id) AS requested_by,
    (SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.acknowledger_id = p.user_id) AS acknowledged_by,
    CASE WHEN er.acknowledged_on IS NULL THEN NULL ELSE DATE_FORMAT(er.acknowledged_on, '%m/%d/%Y %h:%i %p') END AS acknowledged_on,
    (SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.accepter_id = p.user_id) AS accepted_by,
    CASE WHEN er.accepted_on IS NULL THEN NULL ELSE DATE_FORMAT(er.accepted_on, '%m/%d/%Y %h:%i %p') END AS accepted_on,
    (SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.rejector_id = p.user_id) AS rejected_by,
    CASE WHEN er.rejected_on IS NULL THEN NULL ELSE DATE_FORMAT(er.rejected_on, '%m/%d/%Y %h:%i %p') END AS rejected_on,
    er.rejected_reason,
    er.notes,
    (SELECT t.display_name FROM event_request_type t WHERE t.id = er.type_id) AS type,
    (SELECT s.display_name FROM event_request_status s WHERE s.id = er.status_id) AS status
FROM event_request er
    INNER JOIN event e
    ON er.event_id = e.id
    INNER JOIN arena a
    ON e.arena_id = a.id
    INNER JOIN arena_user_assignment aua
    ON a.id = aua.arena_id
    INNER JOIN user u
    ON u.id = aua.user_id
WHERE u.id = 2
ORDER BY e.start_date ASC

        $sql = "SELECT er.id, "
                . "e.id AS event_id, "
                . "CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id) AS location, "
                . "(SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.requester_id = p.user_id) AS requested_by, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.acknowledger_id = p.user_id) AS acknowledged_by, "
                . "CASE WHEN er.acknowledged_on IS NULL THEN NULL ELSE DATE_FORMAT(er.acknowledged_on, '%m/%d/%Y %h:%i %p') END AS acknowledged_on, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.accepter_id = p.user_id) AS accepted_by, "
                . "CASE WHEN er.accepted_on IS NULL THEN NULL ELSE DATE_FORMAT(er.accepted_on, '%m/%d/%Y %h:%i %p') END AS accepted_on, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.rejector_id = p.user_id) AS rejected_by, "
                . "CASE WHEN er.rejected_on IS NULL THEN NULL ELSE DATE_FORMAT(er.rejected_on, '%m/%d/%Y %h:%i %p') END AS rejected_on, "
                . "er.rejected_reason, "
                . "er.notes, "
                . "(SELECT t.display_name FROM event_request_type t WHERE t.id = er.type_id) AS type, "
                . "(SELECT s.display_name FROM event_request_status s WHERE s.id = er.status_id) AS status "
                . "FROM event_request er "
                . "    INNER JOIN event e "
                . "    ON er.event_id = e.id "
                . "    INNER JOIN arena a "
                . "    ON e.arena_id = a.id "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "WHERE u.id = :uid ";
        
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
        
