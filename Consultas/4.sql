SELECT
  ca.placa,
  ca.color,
  ca.fecha_ingreso
FROM carros AS ca
LEFT JOIN viajes AS v ON v.idcarro = ca.idcarro
WHERE v.idviaje IS NULL
  AND ca.deleted_at IS NULL
ORDER BY ca.placa;