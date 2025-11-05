SELECT
  ca.placa,
  COALESCE(cd.nombre, '—') AS destino,
  v.tiempo_horas AS horas,
  v.fecha
FROM viajes AS v
JOIN carros   AS ca ON ca.idcarro = v.idcarro
LEFT JOIN ciudades AS co ON co.idciudad = v.idciudad_origen
LEFT JOIN ciudades AS cd ON cd.idciudad = v.idciudad_destino
WHERE co.nombre = 'Medellin'
  AND v.fecha >= '2025-10-08 00:00:00'
  AND ca.deleted_at IS NULL
  AND v.deleted_at  IS NULL
ORDER BY v.fecha DESC;