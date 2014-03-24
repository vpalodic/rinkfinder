$sql = 'SELECT s.id, s.name, s.description, s.display_name, '
. 's.display_order, IF(sc.count IS NULL, 0, sc.count) AS count '
. 'FROM arena_status s '
. 'LEFT JOIN '
. '(SELECT s1.id, COUNT(a.id) AS count '
. ' FROM arena a '
. ' INNER JOIN arena_user_assignment aua '
. ' ON a.id = aua.arena_id '
. ' INNER JOIN user u '
. ' ON u.id = aua.user_id '
. ' INNER JOIN arena_status s1 '
. ' ON a.status_id = s1.id '
. ' WHERE u.id = :uid '
. ' GROUP BY s1.id) AS sc '
. 'ON s.id = sc.id '
. 'WHERE s.active = 1 '
. 'ORDER BY s.display_order ASC';


SELECT s.id, s.name, s.description, s.display_name,
s.display_order, IF(sc.count IS NULL, 0, sc.count) AS count
FROM arena_status s
LEFT JOIN
(SELECT s1.id, COUNT(a.id) AS count
 FROM arena a
 INNER JOIN arena_user_assignment aua
 ON a.id = aua.arena_id
 INNER JOIN user u
 ON u.id = aua.user_id
 INNER JOIN arena_status s1
 ON a.status_id = s1.id
 WHERE u.id = 2
 GROUP BY s1.id) AS sc
ON s.id = sc.id
WHERE s.active = 1
ORDER BY s.display_order ASC

SELECT t.id AS type_id, t.name AS type_name, t.description AS type_description,
t.display_name AS type_display_name, t.display_order as type_display_order,
IF(tc.count IS NULL, 0, tc.count) AS type_count,
s.id AS status_id, s.name AS status_name, s.description AS status_description,
s.display_name AS status_display_name, s.display_order AS status_display_order,
IF(s.count IS NULL, 0, s.count) AS status_count
FROM event_type t
LEFT JOIN
(SELECT t1.id, COUNT(e.id) AS count
 FROM event e
 INNER JOIN arena a
 ON e.arena_id = a.id
 INNER JOIN arena_user_assignment aua
 ON a.id = aua.arena_id
 INNER JOIN user u
 ON u.id = aua.user_id
 INNER JOIN event_type t1
 ON e.type_id = t1.id
 WHERE u.id = 2
 GROUP BY t1.id) AS tc
ON t.id = tc.id
LEFT JOIN 
(SELECT s2.id, s2.name, s2.description, s2.display_name, s2.display_order,
 sc.type_id, IF(sc.count IS NULL, 0, sc.count) AS count
 FROM arena_status s2
 LEFT JOIN
 (SELECT s1.id, et.id AS type_id, COUNT(e.id) AS count
  FROM event_type et
  LEFT JOIN event e
  ON et.id = e.type_id
  INNER JOIN arena a
  ON e.arena_id = a.id
  INNER JOIN arena_user_assignment aua
  ON a.id = aua.arena_id
  INNER JOIN user u
  ON u.id = aua.user_id
  INNER JOIN event_status s1
  ON e.status_id = s1.id
  WHERE u.id = 2
  GROUP BY s1.id, et.id) AS sc
 ON s2.id = sc.id
 WHERE s2.active = 1) AS s
ON t.id = s.type_id
WHERE t.active = 1
ORDER BY t.display_order ASC, s.display_order ASC