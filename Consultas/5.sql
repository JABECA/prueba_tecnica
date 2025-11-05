SELECT
  ca.placa,
  COALESCE(co.nombre, '—') AS origen,
  COALESCE(cd.nombre, '—') AS destino,
  v.tiempo_horas AS horas,
  v.fecha
FROM viajes AS v
JOIN carros   AS ca ON ca.idcarro = v.idcarro
LEFT JOIN ciudades AS co ON co.idciudad = v.idciudad_origen
LEFT JOIN ciudades AS cd ON cd.idciudad = v.idciudad_destino
WHERE v.fecha BETWEEN '2025-09-26 00:00:00' AND '2025-10-26 23:59:59'
  AND v.deleted_at  IS NULL
  AND ca.deleted_at IS NULL
ORDER BY v.fecha DESC, ca.placa;