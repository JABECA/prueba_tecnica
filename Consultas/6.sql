
-- para validar solo la ciudad tanto origen como destino, ignora los viajes eliminados
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
WHERE (co.activo = 0 OR cd.activo = 0)
  AND v.deleted_at  IS NULL
  AND ca.deleted_at IS NULL
ORDER BY v.fecha DESC;


-- para validar solo la ciudad destino en estado cero, ignora los viajes eliminados
SELECT 
  ca.placa, 
  COALESCE(co.nombre, '—') AS origen, 
  COALESCE(cd.nombre, '—') AS destino, 
  v.tiempo_horas AS horas, 
  v.fecha 
FROM viajes AS v 
  JOIN carros AS ca ON ca.idcarro = v.idcarro 
  LEFT JOIN ciudades AS co ON co.idciudad = v.idciudad_origen 
  LEFT JOIN ciudades AS cd ON cd.idciudad = v.idciudad_destino
WHERE cd.activo = 0 AND 
      v.deleted_at IS NULL AND 
      ca.deleted_at IS NULL 
ORDER BY v.fecha DESC;


-- para validar solo la ciudad origen en estado cero, ignora los viajes eliminados
SELECT 
  ca.placa, 
  COALESCE(co.nombre, '—') AS origen, 
  COALESCE(cd.nombre, '—') AS destino, 
  v.tiempo_horas AS horas, 
  v.fecha 
FROM viajes AS v 
  JOIN carros AS ca ON ca.idcarro = v.idcarro 
  LEFT JOIN ciudades AS co ON co.idciudad = v.idciudad_origen 
  LEFT JOIN ciudades AS cd ON cd.idciudad = v.idciudad_destino
WHERE co.activo = 0 
  AND v.deleted_at IS NULL 
  AND ca.deleted_at IS NULL 
ORDER BY v.fecha DESC;