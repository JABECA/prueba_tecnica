SELECT  c.color, COUNT(*) AS total
FROM carros AS c
WHERE c.deleted_at IS NULL
GROUP BY c.color
ORDER BY total DESC;