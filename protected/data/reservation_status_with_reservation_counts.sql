        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, IF(sc.count IS NULL, 0, sc.count) AS count '
                . 'FROM reservation_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(r.id) AS count '
                . ' FROM reservation r '
                . ' INNER JOIN event e '
                . ' ON r.event_id = e.id '
                . ' INNER JOIN arena a '
                . ' ON e.arena_id = a.id '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN reservation_status s1 '
                . ' ON r.status_id = s1.id '
                . ' WHERE u.id = :uid '
                . ' AND '
                . ' e.start_date >= DATE_SUB(DATE_FORMAT(NOW(), "%Y-%m-%d"),'
                . ' INTERVAL :days DAY) '
                . ' GROUP BY s1.id) AS sc '
                . 'ON s.id = sc.id '
                . 'WHERE s.active = 1 '
                . 'ORDER BY s.display_order ASC';
        
SELECT s.id, s.name, s.description, s.display_name,
s.display_order, IF(sc.count IS NULL, 0, sc.count) AS count
FROM reservation_status s
LEFT JOIN
(SELECT s1.id, COUNT(r.id) AS count
 FROM reservation r
 INNER JOIN event e
 ON r.event_id = e.id
 INNER JOIN arena a
 ON e.arena_id = a.id
 INNER JOIN arena_user_assignment aua
 ON a.id = aua.arena_id
 INNER JOIN user u
 ON u.id = aua.user_id
 INNER JOIN reservation_status s1
 ON r.status_id = s1.id
 WHERE u.id = 2
 AND
 e.start_date >= DATE_SUB(DATE_FORMAT(NOW(), "%Y-%m-%d"),
 INTERVAL 30 DAY)
 GROUP BY s1.id) AS sc
ON s.id = sc.id
WHERE s.active = 1
ORDER BY s.display_order ASC
        
