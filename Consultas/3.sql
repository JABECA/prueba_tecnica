SELECT
  ca.placa,
  ca.fecha_ingreso,
  ROUND(AVG(v.tiempo_horas), 2) AS promedio_horas
FROM carros AS ca
LEFT JOIN viajes AS v ON v.idcarro = ca.idcarro
WHERE ca.placa = 'BBB456'
  AND ca.deleted_at IS NULL
GROUP BY ca.idcarro, ca.placa, ca.fecha_ingreso;