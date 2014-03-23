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