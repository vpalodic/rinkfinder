SELECT r.id,
    CASE WHEN r.source_id IS NULL THEN 'Manual' ELSE r.source_id END AS source,
    e.id AS event_id,
    CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start,
    a.name AS arena,
    a.id AS arena_id,
    (SELECT l.name FROM location l WHERE l.id = e.location_id) AS location,
    (SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id,
    (SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE r.for_id = p.user_id) AS party,
    r.notes,
    (SELECT s.display_name FROM reservation_status s WHERE s.id = r.status_id) AS status
FROM reservation r
    INNER JOIN event e
    ON r.event_id = e.id
    INNER JOIN arena a
    ON e.arena_id = a.id
    INNER JOIN arena_user_assignment aua
    ON a.id = aua.arena_id
    INNER JOIN user u
    ON u.id = aua.user_id
WHERE u.id = 2
ORDER BY e.start_date ASC

        $sql = "SELECT r.id, "
                . "CASE WHEN r.source_id IS NULL THEN 'Manual' ELSE r.source_id END AS source, "
                . "e.id AS event_id, "
                . "CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id) AS location, "
                . "(SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE r.for_id = p.user_id) AS party, "
                . "r.notes, "
                . "(SELECT s.display_name FROM reservation_status s WHERE s.id = r.status_id) AS status "
                . "FROM reservation r "
                . "    INNER JOIN event e "
                . "    ON r.event_id = e.id "
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
        
        if($sid !== null) {
            $sql .= "AND r.status_id = :sid ";
        }
        
        $sql .= "ORDER BY e.start_date ASC";
        
