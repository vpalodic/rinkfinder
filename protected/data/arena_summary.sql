SELECT a.id,
    CASE WHEN a.external_id IS NULL THEN 'Not set' ELSE a.external_id END AS external_id,
    a.name,
    CASE WHEN a.description IS NULL THEN 'Not set' ELSE 'Yes' END as description,
    CASE WHEN a.tags IS NULL THEN 'Not set' ELSE 'Yes' END as tags,
    a.address_line1,
    CASE WHEN a.address_line2 IS NULL THEN 'Not set' ELSE a.address_line2 END AS address_line2,
    a.city,
    a.state,
    a.zip,
    IF(a.lat IS NULL OR a.lat = 0 OR a.lng IS NULL OR a.lng = 0, 'No', 'Yes') AS geocoded,
    CASE WHEN a.phone IS NULL THEN 'Not set' ELSE a.phone END AS phone,
    CASE WHEN a.ext IS NULL THEN 'Not set' ELSE a.ext END AS ext,
    CASE WHEN a.fax IS NULL THEN 'Not set' ELSE a.fax END AS fax,
    CASE WHEN a.fax_ext IS NULL THEN 'Not set' ELSE a.fax_ext END AS fax_ext,
    CASE WHEN a.logo IS NULL THEN 'Not set' ELSE 'Yes' END AS logo,
    CASE WHEN a.url IS NULL THEN 'Not set' ELSE 'Yes' END AS url,
    CASE WHEN a.notes IS NULL THEN 'Not set' ELSE 'Yes' END AS notes,
    (SELECT s.display_name FROM arena_status s WHERE s.id = a.status_id) AS status,
    (SELECT COUNT(DISTINCT aua.user_id) FROM arena_user_assignment aua WHERE aua.arena_id = a.id) AS managers,
    (SELECT COUNT(DISTINCT l.id) FROM location l WHERE l.arena_id = a.id) AS locations,
    (SELECT COUNT(DISTINCT aca.contact_id) FROM arena_contact_assignment aca WHERE aca.arena_id = a.id) AS contacts,
    (SELECT COUNT(DISTINCT arp.id) FROM arena_reservation_policy arp WHERE arp.arena_id = a.id) AS reservation_policies,
    (SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.id = a.id AND e.id IN 
        (SELECT er.event_id FROM event_request er WHERE e.id = er.event_id AND er.status_id IN 
                (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED')))) AS outstanding_event_requests,
    (SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.id = a.id AND e.id IN 
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
                . "CASE WHEN a.external_id IS NULL THEN 'Not set' ELSE a.external_id END AS external_id, "
                . "a.name, "
                . "CASE WHEN a.description IS NULL THEN 'Not set' ELSE 'Yes' END as description, "
                . "CASE WHEN a.tags IS NULL THEN 'Not set' ELSE 'Yes' END as tags, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL THEN 'Not set' ELSE a.address_line2 END AS address_line2, "
                . "a.city, "
                . "a.state, "
                . "a.zip, "
                . "IF(a.lat IS NULL OR a.lat = 0 OR a.lng IS NULL OR a.lng = 0, 'No', 'Yes') AS geocoded, "
                . "CASE WHEN a.phone IS NULL THEN 'Not set' ELSE a.phone END AS phone, "
                . "CASE WHEN a.ext IS NULL THEN 'Not set' ELSE a.ext END AS ext, "
                . "CASE WHEN a.fax IS NULL THEN 'Not set' ELSE a.fax END AS fax, "
                . "CASE WHEN a.fax_ext IS NULL THEN 'Not set' ELSE a.fax_ext END AS fax_ext, "
                . "CASE WHEN a.logo IS NULL THEN 'Not set' ELSE 'Yes' END AS logo, "
                . "CASE WHEN a.url IS NULL THEN 'Not set' ELSE 'Yes' END AS url, "
                . "CASE WHEN a.notes IS NULL THEN 'Not set' ELSE 'Yes' END AS notes, "
                . "(SELECT s.display_name FROM arena_status s WHERE s.id = a.status_id) AS status, "
                . "(SELECT COUNT(DISTINCT aua.user_id) FROM arena_user_assignment aua WHERE aua.arena_id = a.id) AS managers, "
                . "(SELECT COUNT(DISTINCT l.id) FROM location l WHERE l.arena_id = a.id) AS locations, "
                . "(SELECT COUNT(DISTINCT aca.contact_id) FROM arena_contact_assignment aca WHERE aca.arena_id = a.id) AS contacts, "
                . "(SELECT COUNT(DISTINCT arp.id) FROM arena_reservation_policy arp WHERE arp.arena_id = a.id) AS reservation_policies, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.id = a.id AND e.id IN "
                . "    (SELECT er.event_id FROM event_request er WHERE e.id = er.event_id AND er.status_id IN "
                . "        (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED')))) AS outstanding_event_requests, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.id = a.id AND e.id IN "
                . "    (SELECT r.event_id FROM reservation r WHERE e.id = r.event_id AND r.status_id IN "
                . "        (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED')))) AS outstanding_reservations "
                . "FROM arena a "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "WHERE u.id = :uid ";
        
        if($sid !== null) {
            $sql += "AND a.status_id = :sid ";
        }
        
        $sql += "ORDER BY a.name ASC";
        
