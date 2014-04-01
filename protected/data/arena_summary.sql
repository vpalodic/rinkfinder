SELECT a.id,
    a.external_id,
    a.name,
    a.description,
    a.tags,
    a.address_line1,
    a.address_line2,
    a.city,
    a.state,
    a.zip,
    IF(a.lat IS NULL OR a.lat = 0 OR a.lng IS NULL OR a.lng = 0, 'No', 'Yes') AS geocoded,
    a.phone,
    a.ext,
    a.fax,
    a.fax_ext,
    a.logo,
    a.url,
    a.notes,
    (SELECT s.display_name FROM arena_status s WHERE s.id = a.status_id) AS status,
    (SELECT COUNT(DISTINCT aua.user_id) FROM arena_user_assignment aua WHERE aua.arena_id = a.id) AS managers,
    (SELECT COUNT(DISTINCT l.id) FROM location l WHERE l.arena_id = a.id) AS locations,
    (SELECT COUNT(DISTINCT aca.contact_id) FROM arena_contact_assignment aca WHERE aca.arena_id = a.id) AS contacts,
    (SELECT COUNT(DISTINCT arp.id) FROM arena_reservation_policy arp WHERE arp.arena_id = a.id) AS reservation_policies,
    (SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.arena_id = a.id AND e.id IN 
        (SELECT er.event_id FROM event_request er WHERE e.id = er.event_id AND er.status_id IN 
                (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED')))) AS outstanding_event_requests,
    (SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.arena_id = a.id AND e.id IN 
        (SELECT r.event_id FROM reservation r WHERE e.id = r.event_id AND r.status_id IN 
                (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED')))) AS outstanding_reservations
FROM arena a
    INNER JOIN arena_user_assignment aua
    ON a.id = aua.arena_id
    INNER JOIN user u
    ON u.id = aua.user_id
WHERE u.id = 2
ORDER BY a.name ASC

        $sql = "SELECT a.id, "
                . "a.external_id, "
                . "a.name, "
                . "a.description, "
                . "a.tags, "
                . "a.address_line1, "
                . "a.address_line2, "
                . "a.city, "
                . "a.state, "
                . "a.zip, "
                . "IF(a.lat IS NULL OR a.lat = 0 OR a.lng IS NULL OR a.lng = 0, 'No', 'Yes') AS geocoded, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url, "
                . "a.notes, "
                . "a.status, "
                . "(SELECT COUNT(DISTINCT aua.user_id) FROM arena_user_assignment aua WHERE aua.arena_id = a.id) AS managers, "
                . "(SELECT COUNT(DISTINCT l.id) FROM location l WHERE l.arena_id = a.id) AS locations, "
                . "(SELECT COUNT(DISTINCT aca.contact_id) FROM arena_contact_assignment aca WHERE aca.arena_id = a.id) AS contacts, "
                . "(SELECT COUNT(DISTINCT arp.id) FROM arena_reservation_policy arp WHERE arp.arena_id = a.id) AS reservation_policies, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.arena_id = a.id AND e.id IN "
                . "    (SELECT er.event_id FROM event_request er WHERE e.id = er.event_id AND er.status_id IN "
                . "        (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED')))) AS outstanding_event_requests, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.arena_id = a.id AND e.id IN "
                . "    (SELECT r.event_id FROM reservation r WHERE e.id = r.event_id AND r.status_id IN "
                . "        (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED')))) AS outstanding_reservations "
                . "FROM arena a "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "WHERE u.id = :uid ";
        
        if($sid !== null) {
            $sql .= "AND a.status_id = :sid ";
        }
        
        $sql .= "ORDER BY a.name ASC";
        
