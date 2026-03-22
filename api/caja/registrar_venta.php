<?php
/**
 * Piel Morena — API Caja: Registrar Venta de Producto
 * POST → Crea movimiento de caja + decrementa stock del producto
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input       = json_decode(file_get_contents('php://input'), true) ?: [];
$id_producto = intval($input['id_producto'] ?? 0);
$cantidad    = intval($input['cantidad'] ?? 1);
$metodo_pago = $input['metodo_pago'] ?? 'efectivo';

if (!$id_producto || $cantidad < 1) {
    responder_json(false, null, 'Producto y cantidad son obligatorios', 400);
}

if (!in_array($metodo_pago, ['efectivo', 'tarjeta', 'transferencia', 'otro'])) {
    $metodo_pago = 'efectivo';
}

// Buscar producto
$stmt = $db->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ? AND activo = 1");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch();

if (!$producto) {
    responder_json(false, null, 'Producto no encontrado o inactivo', 404);
}

if ($producto['stock'] < $cantidad) {
    responder_json(false, null, 'Stock insuficiente. Disponible: ' . $producto['stock'], 400);
}

$monto = $producto['precio'] * $cantidad;
$concepto = 'Venta producto: ' . $producto['nombre'];
if ($cantidad > 1) {
    $concepto .= ' x' . $cantidad;
}

// Transaccion: registrar venta + decrementar stock
try {
    $db->beginTransaction();

    // Insertar movimiento de caja
    $stmt = $db->prepare(
        "INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_usuario, fecha)
         VALUES ('entrada', ?, ?, ?, ?, CURDATE())"
    );
    $stmt->execute([$monto, $concepto, $metodo_pago, usuario_actual_id()]);
    $id_movimiento = $db->lastInsertId();

    // Decrementar stock
    $stmt = $db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $stmt->execute([$cantidad, $id_producto]);

    $db->commit();

    // Obtener nuevo stock
    $stmt = $db->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $nuevo_stock = (int) $stmt->fetchColumn();

    responder_json(true, [
        'id_movimiento' => $id_movimiento,
        'monto'         => $monto,
        'producto'      => $producto['nombre'],
        'cantidad'      => $cantidad,
        'nuevo_stock'   => $nuevo_stock
    ]);
} catch (Exception $e) {
    $db->rollBack();
    responder_json(false, null, 'Error al registrar la venta', 500);
}
