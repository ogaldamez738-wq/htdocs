-- Seleccionar y usar la base de datos del sistema
USE sistema_inventario;

-- ====================================================================
-- 1. MÓDULO DE LOGIN Y SEGURIDAD
-- ====================================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL
);

-- ====================================================================
-- 2. MÓDULO DE CATEGORÍAS Y PRODUCTOS
-- ====================================================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    categoria_id INT NOT NULL,
    stock INT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- ====================================================================
-- 3. MÓDULO DE PROVEEDORES (Guías 21 y 22)
-- ====================================================================
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT
);

-- ====================================================================
-- 4. MÓDULO DE COMPRAS: ARQUITECTURA MAESTRO-DETALLE (Guía 23)
-- ====================================================================

-- Tabla Maestra (Cabecera de Factura)
CREATE TABLE IF NOT EXISTS compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla Detalle (Líneas de Productos Ingresados)
CREATE TABLE IF NOT EXISTS detalle_compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_compra DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- ====================================================================
-- INSERCIONES DE DATOS SEMILLA / PRUEBA INICIAL
-- ====================================================================

-- Categorías iniciales
INSERT INTO categorias (nombre_categoria) VALUES
('Computadoras'),
('Accesorios'),
('Oficina');

-- Productos iniciales
INSERT INTO productos (nombre_producto, categoria_id, stock, precio) VALUES
('Laptop Dell Inspiron 15', 1, 15, 720.00),
('Mouse Inalámbrico Logitech', 2, 25, 12.00);

-- Proveedor inicial de prueba
INSERT INTO proveedores (nombre_empresa, contacto, telefono, direccion) VALUES
('Tech Data S.A.', 'Juan Pérez (Ventas)', '2222-3333', 'San Salvador, El Salvador');

-- ====================================================================
-- CONSULTAS DE REPORTES Y DASHBOARD (Guías 11 y 12)
-- ====================================================================

-- Reporte 1: Vista completa del inventario con categorías
-- SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio
-- FROM productos p
-- INNER JOIN categorias c ON p.categoria_id = c.id;

-- Reporte 2: Vista filtrada exclusivamente para Accesorios
-- SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio
-- FROM productos p
-- INNER JOIN categorias c ON p.categoria_id = c.id
-- WHERE c.nombre_categoria = 'Accesorios';

-- Métricas para Tarjetas de Dashboard:
-- SELECT COUNT(id) AS total_productos_catalogo FROM productos;
-- SELECT SUM(precio * stock) AS valor_monetario_inventario FROM productos;
-- SELECT MAX(precio) AS producto_mas_caro FROM productos;

-- Reporte agrupado de stock por categoría:
-- SELECT c.nombre_categoria, SUM(p.stock) AS existencias_totales
-- FROM productos p
-- INNER JOIN categorias c ON p.categoria_id = c.id
-- GROUP BY c.nombre_categoria;