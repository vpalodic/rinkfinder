        $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS viewUrl, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.url AS home_url, "
                . "a.lat, "
                . "a.lng, "
                . "( 3959 * ACOS( COS( RADIANS( :lat ) ) * COS( RADIANS( a.lat ) "
                . ") * COS( RADIANS( a.lng ) - RADIANS( :lng ) ) + SIN( RADIANS( "
                . ":lat ) ) * SIN( RADIANS( a.lat ) ) ) ) AS distance, "
                . "CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN "
                . "aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END "
                . "AS contact_type, "
                . "c.id AS contact_id, "
                . "CONCAT(c.first_name, ' ', c.last_name) AS contact_name, "
                . "c.phone AS contact_phone, "
                . "c.ext AS contact_ext, "
                . "c.fax AS contact_fax, "
                . "c.fax_ext AS contact_fax_ext, "
                . "c.email AS contact_email, "
                . "et.id AS event_type_id, "
                . "et.display_name AS event_type_name, "
                . "ec.view_url AS event_view_url, "
                . "ec.count AS event_count, "
                . "ec.start_date_time "
                . "FROM arena a "
                . "    LEFT OUTER JOIN arena_contact_assignment aca "
                . "    ON a.id = aca.arena_id AND a.status_id = (SELECT ass.id "
                . "         FROM arena_status ass WHERE ass.name = 'OPEN') "
                . "    LEFT OUTER JOIN contact c "
                . "    ON c.id = aca.contact_id AND c.active = 1 "
                . "    LEFT OUTER JOIN (SELECT e.arena_id, e.type_id, "
                . "        '' AS view_url, COUNT(e.id) AS count, "
                . "        MIN(CAST(CONCAT(e.start_date, ' ', e.start_time) AS DATETIME)) AS start_date_time "
                . "        FROM event e "
                . "        WHERE e.status_id = (SELECT es.id FROM event_status es WHERE es.name = 'OPEN') "
                . "        GROUP BY e.arena_id, e.type_id, view_url) ec "
                . "    ON a.id = ec.arena_id "
                . "    LEFT OUTER JOIN event_type et "
                . "    ON ec.type_id = et.id AND et.active = 1 ";
        

SELECT
    CONCAT('" . $url . "?id=', a.id) AS view_url,
    a.id,
    a.name AS arena_name, 
    a.address_line1,
    CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN 
    NULL ELSE a.address_line2 END AS address_line2, 
    CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, 
    a.phone,
    a.ext,
    a.fax,
    a.fax_ext,
    a.url AS home_url,
    a.lat, 
    a.lng, 
    0 AS distance,
    CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END AS contact_type,
    c.id AS contact_id,
    CONCAT(c.first_name, ' ', c.last_name) AS contact_name,
    c.phone AS contact_phone,
    c.ext AS contact_ext,
    c.fax AS contact_fax,
    c.fax_ext AS contact_fax_ext,
    c.email AS contact_email,
    et.id AS event_type_id,
    et.display_name AS event_type_name,
    ec.view_url AS event_view_url,
    ec.count AS event_count,
    ec.start_date_time
FROM arena a
    LEFT OUTER JOIN arena_contact_assignment aca
    ON a.id = aca.arena_id AND a.status_id = (SELECT ass.id FROM arena_status ass WHERE ass.name = "OPEN")
    LEFT OUTER JOIN contact c
    ON c.id = aca.contact_id AND c.active = true
    LEFT OUTER JOIN (SELECT e.arena_id, e.type_id, "" AS view_url, COUNT(e.id) AS count, MIN(CAST(CONCAT(e.start_date, ' ', e.start_time) AS DATETIME)) AS start_date_time
                     FROM event e 
                     WHERE e.status_id = (SELECT es.id FROM event_status es WHERE es.name = "OPEN")
                     GROUP BY e.arena_id, e.type_id, view_url) ec
    ON a.id = ec.arena_id    
    LEFT OUTER JOIN event_type et
    ON ec.type_id = et.id AND et.active = 1

