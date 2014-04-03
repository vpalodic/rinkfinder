        $sql = 'SELECT 0 AS id, '
                . '"INACTIVE" AS name, ' 
                . '"Contact may be assigned to an Arena but will not show under contacts" AS description, '
                . '"Inactive" AS display_name, '
                . '2 AS display_order, '
                . 'IF(COUNT(c.id) IS NULL, 0, COUNT(c.id)) AS count '
                . 'FROM contact c '
                . 'INNER JOIN arena_contact_assignment aca '
                . 'ON c.id = aca.contact_id '
                . 'INNER JOIN arena a '
                . 'ON a.id = aca.arena_id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id '
                . 'INNER JOIN user u '
                . 'ON u.id = aua.user_id '
                . 'WHERE c.active = 0 '
                . 'AND u.id = :uid ';
        
        $sql2 = 'SELECT 1 AS id, '
                . '"ACTIVE" AS name, '
                . '"Contact may be assigned to an Arena and will show under contacts" AS description, '
                . '"Active" AS display_name, '
                . '1 AS display_order, '
                . 'IF(COUNT(c.id) IS NULL, 0, COUNT(c.id)) AS count '
                . 'FROM contact c '
                . 'INNER JOIN arena_contact_assignment aca '
                . 'ON c.id = aca.contact_id '
                . 'INNER JOIN arena a '
                . 'ON a.id = aca.arena_id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id '
                . 'INNER JOIN user u '
                . 'ON u.id = aua.user_id '
                . 'WHERE c.active = 1 '
                . 'AND u.id = :uid ';


        if($aid !== null) {
            $sql .= "AND a.arena_id = :aid ";
            $sql2 .= "AND a.arena_id = :aid ";
            $parms['aid'] = $aid;
        }
        
        if($sid !== null) {
            if($sid > 0) {
                $sql = $sql2;
            }

            $parms['sid'] = $sid;
        } else {
            $sql .= ' UNION ' . $sql2;
        }
        
        $sql .= ' ORDER BY display_order ASC ';
        
SELECT 0 AS id,
    "INACTIVE" AS name,
    "Contact may be assigned to an Arena but will not show under contacts" AS description,
    "Inactive" AS display_name,
    2 AS display_order,
    IF(COUNT(c.id) IS NULL, 0, COUNT(c.id)) AS count
FROM contact c
    INNER JOIN arena_contact_assignment aca
    ON c.id = aca.contact_id
    INNER JOIN arena a
    ON a.id = aca.arena_id
    INNER JOIN arena_user_assignment aua
    ON a.id = aua.arena_id
    INNER JOIN user u
    ON u.id = aua.user_id
WHERE c.active = 0
UNION
SELECT 1 AS id,
    "ACTIVE" AS name,
    "Contact may be assigned to an Arena and will show under contacts" AS description,
    "Active" AS display_name,
    1 AS display_order,
    IF(COUNT(c.id) IS NULL, 0, COUNT(c.id)) AS count
FROM contact c
    INNER JOIN arena_contact_assignment aca
    ON c.id = aca.contact_id
    INNER JOIN arena a
    ON a.id = aca.arena_id
    INNER JOIN arena_user_assignment aua
    ON a.id = aua.arena_id
    INNER JOIN user u
    ON u.id = aua.user_id
WHERE c.active = 1
ORDER BY display_order ASC