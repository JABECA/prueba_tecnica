<?php

class FormatoVisitaController{
	
	/******************** 
	
	En resumen el código genera un PDF unificado de la vista, opcionalmente lo guarda y registra en el sistema o en base de datos, y finalmente lo envía al usuario para descarga o visualización. 
	
	**********************/
	

    /* Genera el PDF único del Formato de Visita, registra y lo entrega al navegador.*/
    public function generarFormatoVisita($rutaFormato, $cod_solicitud_credito, $infoAdicional, $cargaDoc = 0){

        // Preparación de utilidades y datos base
        $pdfMerger   = new PDFMerger();        // Instancia el combinador de PDFs (para unir varias páginas/archivos).
        $objCrypto   = new Encriptacion();     // Utilidad para cifrar/descifrar archivos.
        $this->objAppDB = new CDataFormato();  // Acceso a operaciones de datos/rutas asociadas a formatos.
        $archivos    = $this->obtenerImagenesVisita($cod_solicitud_credito); // Recupera la lista de archivos asociados a la visita.

        // Recorre y agrega cada archivo al merger 
        foreach ($archivos as $archivo) {                   // Itera sobre cada registro/archivo hallado para la visita.
            $base =                                        // Construye un nombre base único para el PDF intermedio:
                $archivo[Generales::COD_SOLICITUD_CREDITO] //  Código de solicitud (identifica la visita/documento).
                . '_' . $archivo[Clases::TIPO]             //  Tipo de archivo (ej.: foto, formulario, etc.).
                . '_' . $archivo[BaseDatos::COD_FOTOS_VISITA] //  Identificador de la foto/archivo dentro de la visita.
                . '.pdf';                                  //  Extensión final en PDF (asumimos que cada pieza ya está en PDF).

            // Aquí dejamos una sola ruta final de trabajo para evitar sobrescrituras accidentales.
            $archivoFinal = rtrim(DIGESTVISITA, '/\\') . DIRECTORY_SEPARATOR . $base; // Ruta absoluta hacia el archivo temporal descifrado.

            // Origen cifrado en caso de aplicar si los archivos fuente están cifrados dentro de IMAGENES_VISITA.
            $origenCifrado = rtrim(IMAGENES_VISITA, '/\\') . DIRECTORY_SEPARATOR
                           . $archivo[Generales::ENCONDODED] . $base; // Ruta del archivo de origen ( ofuscado ó cifrado ).

            // Descifra ó copia desde $origenCifrado hacia $archivoFinal para poderlo consumir.
            $objCrypto->deCifrarDocumento($origenCifrado, $archivoFinal, false); // false = no eliminar el origen (ajústalo a tu política).

            // Agrega el PDF descifrado al merger (todas las páginas).
            $pdfMerger->addPDF($archivoFinal, 'all');      // 'all' asegura incluir todas las páginas del PDF agregado.
        }

        //  Une todos los PDFs en un único archivo 
        // Si $rutaFormato viene vacío, crea una ruta temporal única para el PDF final.
        if (empty($rutaFormato)) {                          // Valida que exista una ruta de salida.
            $rutaFormato = sys_get_temp_dir()               // Toma el directorio temporal del sistema.
                         . DIRECTORY_SEPARATOR
                         . uniqid('visita_', true)          // Prefijo único para evitar colisiones en concurrencia.
                         . '.pdf';                          // Extensión del archivo final.
        }

        // Persiste el PDF final en disco en $rutaFormato
        $pdfMerger->merge('file', $rutaFormato);            // Ejecuta el merge y escribe el archivo de salida.

        //  Registro ó almacenamiento documental condicionado
        if ((int)$cargaDoc === 1) {                         // Solo si se ha solicitado registrar y cargar el documento.
            $documentosCliente = $this->obtenerDocumentosCliente($cod_solicitud_credito); // Datos del cliente/documentos previos 
            $unidad = $this->obtenerUnidadSolicitud($cod_solicitud_credito); // Unidad/centro asociado a la solicitud.

            // Nombre “visible” del PDF para almacenamiento/ZIP cifrado.
            $nombrePdf = $infoAdicional['nombre_pdf'] ?? ('FormatoVisita_' . $cod_solicitud_credito . '.pdf'); // Fallback si no viene en $infoAdicional.

            // Ruta de salida para el ZIP cifrado en contingencia.
            $rutaZipCifrado = rtrim(DIRECTOR_CONTINGENCIA, '/\\') . DIRECTORY_SEPARATOR . 'encoded_' . $nombrePdf . '.zip'; // Nombre del ZIP cifrado.

            // Comprime y cifra el PDF final para almacenamiento seguro deacuerdo a politica
            $this->zipYcifrarDocumento($rutaFormato, $rutaZipCifrado, true); // true = incluir/remover temporales

            // Registra el documento dentro de la categoría “OTROS DOCUMENTOS” 
            $this->guardarInfoArchivoDocumentos('OTROS DOCUMENTOS', $nombrePdf, $unidad); // Guarda metadatos del documento generado.

            // Estructura de auditoría/carga para la BD 
            $data_documentos = [                            // Arreglo con la metadata mínima para consolidar la carga del documento.
                Generales::COD_TIPO_CREDITO     => $infoAdicional[Generales::COD_TIPO_CREDITO] ?? null, // Tipo de crédito si viene
                Generales::COD_SOLICITUD_CREDITO=> $cod_solicitud_credito, // La solicitud a la que pertenece el documento.
                Generales::DOCUMENTOS           => 'formatoVisita_' . $cod_solicitud_credito . '.pdf', // Nombre estándar del documento.
                Generales::COD_USUARIO          => $_SESSION[Generales::COD_USUARIO] ?? null, // Usuario que disparó la acción.
                Generales::MODULO               => 135,    // Código de módulo 
            ];

            // Confirma y persiste en la BD que el documento fue cargado o registrado.
            $this->confirmarCargaDocumentos($data_documentos); // Inserta o actualiza el registro de auditoría de carga.
        }

        //  Entrega al navegador
        $fileName = 'FormatoVisita_' . $cod_solicitud_credito . '.pdf'; // Nombre final con el que verá o descargará el usuario.
        $this->streamPdf($fileName, $rutaFormato);          // Envía el PDF al navegador y elimina el temporal

        // Fin del proceso
        return 1;                                           // Retorna 1 para indicar éxito.
    }

    /*** Envía un PDF al navegador con encabezados correctos y limpia el archivo temporal ****/
    public function streamPdf($fileName, $route){
        
        // Sanea el nombre de archivo para evitar inyección en cabeceras.
        $safeName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', (string)$fileName); // Reemplaza caracteres no permitidos por guion bajo.

        // Valida que el archivo exista y sea legible antes de enviarlo.
        if (!is_file($route) || !is_readable($route)) {     // Verifica existencia y permisos de lectura.
            http_response_code(404);                        // Responde 404 si no está disponible.
            echo 'Archivo no disponible.';                  // Mensaje simple 
            return;                                         // Sale sin continuar con el envío.
        }

        // Limpia cualquier salida previa para no corromper los headers.
        if (ob_get_level()) { ob_end_clean(); }             // Vacía el buffer de salida si está activo.

        // Cabeceras HTTP para transferencia de archivo PDF.
        header('Content-Description: File Transfer');       // Describe la respuesta como transferencia de archivo.
        header('Content-Type: application/pdf');            // Indica que el contenido es un PDF.
        header('Content-Disposition: attachment; filename="' . $safeName . '"'); // attachment = descarga
        header('Expires: 0');                               // Evita cacheos antiguos 
        header('Cache-Control: must-revalidate');           // Obliga al navegador a validar la caché.
        header('Pragma: public');                           // Indica que puede ser almacenado en cachés públicas si corresponde.
        header('Content-Length: ' . filesize($route));      // Longitud exacta del archivo para progresos y o descargas correctas.

        // Envía el cuerpo del archivo al cliente.
        $fp = fopen($route, 'rb');                          // Abre el archivo en binario para lectura eficiente.
        fpassthru($fp);                                     // Envía el stream del archivo directamente a la salida.
        fclose($fp);                                        // Cierra el puntero de archivo.

        // Elimina el archivo temporal de servidor
        @unlink($route);                                    // Borra el archivo; @ suprime warning si ya no existiera.
    }

}


