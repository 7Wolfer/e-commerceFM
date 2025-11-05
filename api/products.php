<?php
require_once __DIR__.'/config.php';

try {
    // Preparar filtros
    $params = [];
    $filters = [];

    if(isset($_GET['categoria_id']) && $_GET['categoria_id'] !== '') {
        $filters[] = 'p.categoria_id = ?';
        $params[] = $_GET['categoria_id'];
    }
    if(isset($_GET['marca_id']) && $_GET['marca_id'] !== '') {
        $filters[] = 'p.marca_id = ?';
        $params[] = $_GET['marca_id'];
    }
    if(isset($_GET['nuevo']) && $_GET['nuevo'] === '1') {
        $filters[] = 'p.nuevo = 1';
    }
    if(isset($_GET['oferta']) && $_GET['oferta'] === '1') {
        $filters[] = 'p.oferta = 1';
    }
    if(isset($_GET['organico']) && $_GET['organico'] === '1') {
        $filters[] = 'p.organico = 1';
    }

    // Construir WHERE
    $where = count($filters) ? 'WHERE '.implode(' AND ', $filters) : '';

    // Ordenamiento
    $sort = 'p.id DESC';
    if(isset($_GET['sort'])) {
        switch($_GET['sort']) {
            case 'price_asc':
                $sort = 'p.precio ASC';
                break;
            case 'price_desc':
                $sort = 'p.precio DESC';
                break;
        }
    }

    // Consulta principal
    $sql = "SELECT p.*, c.nombre AS categoria, m.nombre AS marca
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN marcas m ON m.id = p.marca_id
            $where
            ORDER BY $sort";

    // Ejecutar consulta
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();

    // Formatear resultado
    $data = array_map(function($row) {
        return [
            'id' => $row['id'],
            'sku' => $row['sku'],
            'name' => $row['nombre'],
            'brand' => $row['marca'],
            'brand_id' => $row['marca_id'],
            'price' => floatval($row['precio']),
            'unit' => $row['unidad'],
            'img' => $row['imagen'],
            'category' => $row['categoria'],
            'category_id' => $row['categoria_id'],
            'tags' => array_values(array_filter([
                $row['nuevo'] ? 'nuevo' : null,
                $row['oferta'] ? 'oferta' : null,
                $row['organico'] ? 'organico' : null
            ]))
        ];
    }, $productos);

    json_response($data);

} catch (Exception $e) {
    error_log('Error en products.php: ' . $e->getMessage());
    error_response('Error al obtener productos: ' . $e->getMessage());
}
?>