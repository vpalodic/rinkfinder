        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, '
                . 'IF(sc.count IS NULL, 0, sc.count) AS count '
                . 'FROM event_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(e.id) AS count '
                . ' FROM event e '
                . ' INNER JOIN arena a '
                . ' ON e.arena_id = a.id '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN event_status s1 '
                . ' ON e.status_id = s1.id '
                . ' WHERE u.id = :uid  '
                . ' AND '
                . ' e.type_id = :etype '
                . ' AND '
                . ' e.start_date >= DATE_SUB(DATE_FORMAT(NOW(), "%Y-%m-%d"),'
                . ' INTERVAL :days DAY) '
                . ' GROUP BY s1.id) AS sc '
                . ' ON s.id = sc.id '
                . ' WHERE s.active = 1 '
                . ' ORDER BY s.display_order ASC ';



SELECT s.id, s.name, s.description, s.display_name, s.display_order,
 IF(sc.count IS NULL, 0, sc.count) AS count
 FROM event_status s
 LEFT JOIN
 (SELECT s1.id, COUNT(e.id) AS count
  FROM event e
  INNER JOIN arena a
  ON e.arena_id = a.id
  INNER JOIN arena_user_assignment aua
  ON a.id = aua.arena_id
  INNER JOIN user u
  ON u.id = aua.user_id
  INNER JOIN event_status s1
  ON e.status_id = s1.id
  WHERE u.id = 2 
  AND
  e.type_id = 1
  AND
  e.start_date >= DATE_SUB(DATE_FORMAT(NOW(), "%Y-%m-%d"), INTERVAL 360 DAY)
  GROUP BY s1.id) AS sc
 ON s.id = sc.id
 WHERE s.active = 1
 ORDER BY s.display_order ASC