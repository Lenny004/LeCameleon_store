--BASE DE DATOS (TABLAS)
CREATE TABLE tbtipo_empleado (
    idtipo_empleado SERIAL primary key,
    tipo_empleado character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbestado_empleado(
    idestado_empleado SERIAL primary key,
    nombre_estado character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbempleado (
    idempleado SERIAL primary key,
    nombre_empleado character varying(50) NOT NULL,
    apellido_empleado character varying(50) NOT NULL,
    duiempleado character varying(10) UNIQUE NOT NULL,
    nitempleado character varying(17) UNIQUE NOT NULL,
    telefono_empleado character varying(9) NOT NULL,
    correo_empleado character varying(50) NOT NULL,
    fecha_nacimiento_empleado timestamp without time zone NOT NULL,
    idtipo_empleado integer NOT NULL REFERENCES tbtipo_empleado(idtipo_empleado),
    idestado_empleado integer NOT NULL REFERENCES tbestado_empleado (idestado_empleado)
);

CREATE TABLE tbestado_usuario_c (
    idestado_usuario_c SERIAL primary key,
    estado_usuario_c character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbtipo_usuario_e(
    idtipo_usuario_e SERIAL primary key,
    tipo_usuario_e character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbestado_usuario_e (
    idestado_usuario_e SERIAL primary key,
    estado_usuario_e character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbusuario_cliente(
    idusuario_c SERIAL primary key,
    usuario_c character varying(75) NOT NULL,
    contrasena_c character varying(200) NOT NULL,
    intentos_c integer NULL,
    nombre_cliente character varying(50) NOT NULL,
    apellido_cliente character varying(50) NOT NULL,
    correo_cliente character varying(60) NOT NULL,
    telefono_cliente varchar(9) UNIQUE NOT NULL,
    direccion_cliente character varying(1000) NULL,
    fecha_creacion timestamp NOT NULL,
    fecha_bloqueo_c timestamp without time zone,
    fecha_desbloqueo_c timestamp without time zone,
    idestado_usuario_c integer NOT NULL REFERENCES tbestado_usuario_c(idestado_usuario_c)
);

CREATE TABLE tbusuario_empleado(
    idusuario_e SERIAL primary key,
    usuario_e character varying(75) NOT NULL,
    contrasena_e character varying(100) NOT NULL,
    intentos_e integer,
    fecha_bloqueo_e timestamp without time zone,
    fecha_desbloqueo_e timestamp without time zone,
    idempleado integer NOT NULL REFERENCES tbempleado(idempleado),
    idtipo_usuario_e integer NOT NULL REFERENCES tbtipo_usuario_e(idtipo_usuario_e),
    idestado_usuario_e integer NOT NULL REFERENCES tbestado_usuario_e(idestado_usuario_e)
);

CREATE TABLE tbcategoria(
    idcategoria_producto SERIAL primary key,
    categoria_producto character varying(25) UNIQUE NOT NULL,
    imagen_categoria VARCHAR(1000)
);

CREATE TABLE tbsubcategoria_producto (
    idsubcategoria_producto SERIAL primary key,
    subcategoria_producto character varying(60) UNIQUE NOT NULL,
    imagen_subcategoria VARCHAR(1000),
    idcategoria_producto integer NOT NULL REFERENCES tbcategoria(idcategoria_producto)
);

CREATE TABLE tbdistribuidor(
    iddistribuidor SERIAL primary key,
    nombre_distribuidor character varying(35) UNIQUE NOT NULL,
    direccion_distribuidor character varying(500),
    telefono_distribuidor character varying(20) NOT NULL
);

CREATE TABLE tbmarca (
    id_marca SERIAL primary key,
    nombre_marca character varying(35) UNIQUE NOT NULL,
    imagen_marca VARCHAR(1000)
);

CREATE TABLE tbcolor (
    idcolor SERIAL primary key,
    color character varying(15) UNIQUE NOT NULL
);

CREATE TABLE tbestado_producto (
    idestado_producto SERIAL primary key,
    estado_producto character varying UNIQUE NOT NULL
);

CREATE TABLE tbproducto (
    idproducto SERIAL primary key,
    nombre_producto character varying(75) NOT NULL,
    descripcion character varying(1000) NOT NULL,
    material character varying(50) NOT NULL,
    tamanio character varying(50),
    existencias smallint NOT NULL,
    porcentaje_descuento smallint DEFAULT 0 NOT NULL,
    precio_producto numeric(6,2) NOT NULL,
    imagen_principal VARCHAR(1000),
    idcolor integer NULL REFERENCES tbcolor(idcolor),
    id_marca integer NOT NULL REFERENCES tbmarca(id_marca),
    iddistribuidor integer NOT NULL REFERENCES tbdistribuidor(iddistribuidor),
    idestado_producto integer NULL REFERENCES tbestado_producto(idestado_producto),
    idsubcategoria_producto integer NOT NULL REFERENCES tbsubcategoria_producto(idsubcategoria_producto)
);

CREATE TABLE tbimagen_producto (
    idimagen_producto SERIAL primary key,
    imagen_producto VARCHAR(1000),
    idproducto integer NOT NULL REFERENCES tbproducto(idproducto)
);

CREATE TABLE tbinventario(
    idinventario SERIAL primary key,
    cantidad smallint NOT NULL,
    precio_unitario numeric(7,2) NOT NULL,
    fecha_entrega timestamp without time zone NOT NULL,
    fecha_inicio_ventas timestamp without time zone NOT NULL,
    idproducto integer NOT NULL REFERENCES tbproducto(idproducto)
);

CREATE TABLE tbestado_factura(
    idestado_factura SERIAL primary key,
    estado_factura character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbtipo_pago (
    idtipo_pago SERIAL primary key,
    tipo_pago character varying(40) UNIQUE NOT NULL
);

CREATE TABLE tbfactura (
    idfactura SERIAL primary key,
    fecha_factura timestamp NOT NULL,
    monto_total numeric(7,2) NULL,
    idestado_factura integer NOT NULL REFERENCES tbestado_factura(idestado_factura),
    idtipo_pago integer NULL REFERENCES tbtipo_pago(idtipo_pago),
    idusuario_c integer NULL REFERENCES tbusuario_cliente(idusuario_c),
    idusuario_e integer NULL REFERENCES tbusuario_empleado(idusuario_e)
);

CREATE TABLE tbdetalle_factura (
    iddetalle_factura SERIAL primary key,
    total_producto numeric(7,2) NOT NULL,
    precio_actual numeric(6,2) NOT NULL,
    cantidad_descuento numeric(6,2) DEFAULT 0 NULL,
    cantidad_producto smallint NOT NULL,
    idfactura integer NOT NULL REFERENCES tbfactura(idfactura),
    idproducto integer NOT NULL REFERENCES tbproducto(idproducto)
);

CREATE TABLE tbenvio_pedido (
    idenvio_pedido SERIAL primary key,
    direccion_entrega_pedido character varying(500) NOT NULL,
    fecha_entrega_pedido timestamp NOT NULL,
    idfactura integer NOT NULL REFERENCES tbfactura(idfactura)
);

CREATE TABLE tbestado_valoracion (
    idestado_valoracion SERIAL primary key,
    estado_valoracion character varying(25) UNIQUE NOT NULL
);

CREATE TABLE tbvaloraciones (
    idvaloracion SERIAL primary key,
    valoraciones smallint NOT NULL,
    reseña character varying(500),
    fecha_publicacion timestamp without time zone NOT NULL,
    idproducto integer NOT NULL REFERENCES tbproducto(idproducto),
    idestado_valoracion integer DEFAULT 1 NOT NULL REFERENCES tbestado_valoracion(idestado_valoracion),
    idusuario_c integer NOT NULL REFERENCES tbusuario_cliente(idusuario_c)
);

--------------------------------------------------------------------REGISTROS--------------------------------------------------------------------
INSERT INTO public."tbdistribuidor"("nombre_distribuidor", "direccion_distribuidor", "telefono_distribuidor")
    VALUES ('Lofi Girl', 'San Francisco, California Av12', '+15 150 9874 562'),
    ('Ariete', 'Beethoven 15 ático 3ª 08021 Barcelona', '+15 364 6564 547'),
    ('Laura Ashley', 'New York, Palm Street', '+34 941 58 76 76'),
    ('TENDANCE', 'San Francisco', '+15 154 4578 147'),
    ('Maine Furniture Co.', 'Corporate Offices: 133 Gibralter Ave, Annapolis, MD 21401', ''),
    ('Rebecca Mobili','Via dell Industria, 17, 62017 Porto Recanati MC, Italy','+39 0733 672081'),
    ('KitchenCraft', 'Lifetime Brands Europe Limited, KitchenCraft, The Hub, Nobel Way, Birmingham', '+44 (0) 121 604 6000'),
    ('WedgWood','Stoke-on-Trent, Reino Unido', '+44 (0)1782 282651'),
    ('Missoni Home', 'AMARA Living Limited Unit 4, Concord Farm','+44 1376 333557'),
    ('MisterWils', 'Calle Fridex Cuatro. Autopista Sevilla-Málaga A92 – km 4,4. 41500 Alcalá de Guadaíra, Sevilla.', '955 51 40 01'),
    ('Ruzafa Vintage', 'Carrer de Puerto Rico, 33, 46004 València, Valencia, España', '+34 657 19 12 24'),
    ('Francisco Segarra', 'Polígono Industrial Mijares, Carrer de la Indústria, 11, 12550 Almassora, Castellón, España', '+34 964 25 79 08'),
    ('Trouve', 'Avenida Álvaro Obregón 186, Bis B, Roma Nte., 06700 Ciudad de México, México', '+52 55 5264 4884'),
    ('Hilda Herrera', 'Q7J6+HV5, Nahuizalco', '+503 2357 4086'),
    ('Nahanche','Metrocentro, Tercera Etapa Local 74 y 75 San Salvador CP, 1101', '+503 2260 1581'),
    ('Coco Canela', '9° Calle Poniente #4036, Local #6, Plaza La Novena, Colonia Escalón entre la 77° y la, 79 Avenida Nte., San Salvador', '+503 2223 8088');

INSERT INTO public."tbcategoria"("categoria_producto", "imagen_categoria")
    VALUES ('Decoración', '629be07ca8673.jpg'), ('Accesorios', '629e07f09310e.png'),('Artesanales', '629be073b2933.jpg');


INSERT INTO public."tbsubcategoria_producto"("subcategoria_producto", "imagen_subcategoria","idcategoria_producto")
    VALUES ('Patio', '629c97600f700.jpg', 1),
    ('Terraza', '629c973194afc.jpg', 1),
    ('Otros', '629c973b1963c.jpg', 1), 
    ('Dormitorio', '629c9743ad46e.jpg', 1),
    ('Cocina', '629c974c8919b.jpeg', 1),
    ('Salón', '629c9755e9fee.jpg', 1),
    ('Baño', '629c9724c9951.jpg', 1),
    ('Accesorios tecnológicos', '629c97f978565.png', 1),
    ('Decoración de escritorios', '629c97d7e17e2.jpg', 1),
    ('Anillos', '629be63b8951e.jpg', 2),
    ('Aretes', '629be6230ffda.jpg', 2),
    ('Billeteras', '629be65082eaa.jpg', 2),
    ('Collares', '629be897cf087.jpg', 2),
    ('Llaveros', '629be69a1d41b.jpg', 2),
    ('Pines', '629be8a13b854.jpg', 2),
    ('Pulseras', '629be8aaf0092.jpg', 2),
    ('Relojes', '629c1d108f17e.jpg', 2),
    ('Playeras Masculinas', '629c1cf1d5fda.png', 2),
    ('Playeras Femeninas', '629be8bc89ab6.jpg', 2),
    ('Accesorios Artesanales', '629be68c37a81.jpg', 3),
    ('Decoración Artesanal', '629c96e0f1fd1.jpg', 3);

INSERT INTO public."tbcolor"("color")
    VALUES ('Rojo'),
    ('Azul'),
    ('Verde'),
    ('Amarillo'),
    ('Morado'),
    ('Rosado'),
    ('Celeste'),
    ('Naranja'),
    ('Cafe'),
    ('Gris'),
    ('Negro'),
    ('Blanco');

INSERT INTO public."tbmarca"("nombre_marca", "imagen_marca")
    VALUES 
    ('Lofi Girl', '62d4ca6e57136.png'),
    ('Ariete', '62d4cbb4f287e.jpg'),
    ('Laura Ashley', '62d4cc03c842f.png'),
    ('TENDANCE', '62d4d0771c9cf.png'),
    ('Maine Furniture Co', '62d4d0fc9aa10.png'),
    ('Rebecca Mobili', '62d4d26c987d7.jpg'),
    ('KitchenCraft', '62d4d2bdab07a.png'),
    ('WedgWood', '62d4d360569be.png'),
    ('Missoni Home', '62d4d3c2e8388.jpg'),
    ('MisterWils', '62d4d47e8bc44.jpg'),
    ('Ruzafa Vintage', '62d4d4e97b040.jpg'),
    ('Francisco Segarra', '62d4d5279194f.png'),
    ('Trouve', '62d4d569cd75c.png'),
    ('Hilda Herrera', '62d4d6eceed0d.jpg'),
    ('Nahanche', '62d4df449a6fe.png'),
    ('Coco Canela', '62d4df73c8572.png');

INSERT INTO public."tbestado_producto"("estado_producto")
    VALUES ('En venta'),
    ('Agotado'),
    ('Eliminado');

INSERT INTO public."tbproducto"("nombre_producto", "descripcion", "imagen_principal", "material", "tamanio", "existencias", "porcentaje_descuento", "precio_producto", "idcolor", "id_marca", "iddistribuidor", "idestado_producto", "idsubcategoria_producto")
    VALUES 
    ('KitchenCraft Colección Cascanueces', 'Utencilios de cocina', '62d97e2acf1fa.png', 'Madera', '30 cm', 5, 0, 19.60, 1, 7, 3, 1, 5),
	('Pete Cromer Echidna Tea Towel', 'Utencilios de cocina', '62d03b5b488c5.png', 'Algodon', '50cm x 70cm', 15, 0, 8.60, 1, 7, 3, 1, 5),
	('Pete Cromer Echidna Tote Bag', 'Utencilios de cocina', '62d04a9aca8d3.png', 'Algodon', '41cm x 42cm', 20, 0, 9.00, 1, 7, 3, 1, 5),
	('Pete Cromer Kookaburra Tote Bag', 'Utencilios de cocina', '62d97e714c56e.png', 'Algodon', '41cm x 42cm', 16, 0, 7.60, 7, 13, 3, 1, 5),
	('Eco-Friendly Bamboo Fibre Compost Bin', 'Accesorios', '62d03e2f493c8.png', 'fibras de bambú', '50 cm', 5, 0, 39.60, 1, 7, 3, 1, 3),
	('Estante colgante de utensilios de acero inoxidable', 'Utencilios de cocina', '62d04c4dad34c.png', 'Metal', '52cm', 15, 0, 5.60, 1, 7, 3, 1, 6),
	('Pulsera con piedras de mar', 'Accesorios', '62d04c9746c93.png', 'Malaquita', '6 cm', 5, 0, 49.60, 1, 13, 3, 1, 16),
	('Prendedor Estilo Art Decó', 'Accesorios', '62d04cd90358d.png', 'Metal', '2.2cm x 5cm', 15, 0, 48.60, 1, 13, 3, 1, 3),
	('Collar con cuentas de vidrio', 'Accesorios', '62d04d02de258.png', 'vidrio marroquí', '70cm', 2, 0, 99.00, 1, 13, 3, 1, 13),
	('Reloj Mach 2000 Dark Empire', 'Accesorios', '62d04d34487bd.png', 'Metal', '23.5cm', 16, 0, 87.60, 1, 13, 3, 1, 17),
	('Reloj Himalaya Automatic White.', 'Accesorios', '62d042b066c1f.png', 'Metal', '24.5cm', 5, 0, 99.60, 1, 13, 3, 1, 17),
	('Reloj para dama Milanese Type 18', 'Accesorios', '62d04d7061cd1.png', 'Metal', '23cmM', 15, 0, 5.60, 1, 13, 3, 1, 17),
	('Yogurella', 'Utencilios de cocina', '62d0443e83c14.png', 'Metal', '15cm', 15, 0, 65.60, 1, 2, 3, 1, 5),
	('POPCORN POPPER XL', 'Utencilios de cocina', '62d0453bbb40f.png', 'Metal', '34 x 52 x 29 cm.', 5, 0, 89.60, 1, 2, 3, 1, 5),
	('PIMMY 700W ORANGE', 'Utencilios de cocina', '62d045d0f1bc6.png', 'Metal', '20cm', 15, 0, 38.60, 1, 2, 3, 1, 5),
	('BLENDY METAL', 'Utencilios de cocina', '62d04e1b2a79a.png', 'Metal', '18 x 36 x 15 cm.', 2, 0, 59.00, 1, 2, 3, 1, 5),
	('SPREMÌ METAL', 'Utencilios de cocina', '62d04741ad581.png', 'Metal', '29.5cm', 16, 0, 87.60, 1, 2, 3, 1, 5),
	('CENTRIKA METAL.', 'Utencilios de cocina', '62d047891d3d9.png', 'Metal', '24.5cm', 5, 0, 99.60, 1, 2, 3, 1, 5),
    ('Black & White Lofi Girl Hoodie', 'Mejora tu guardarropa con esta sudadera con capucha original de Lofi Girl. En un color para una apariencia limpia y firmado con un bordado de logotipo de Lofi Girl simple pero elegante en el frente, puede mostrar su apoyo a la marca mientras se mantiene cómodo y relajado.', '62d7f3ffad04f.png', '85% cotton, 15% polyester', 'talla S', 50, 0, 62.90, 11, 1, 1, 1, 18),
	('Lofi Girl & Friends Hoodie', 'Un peluche, una niña y un gato, no podemos nombrar un trío más icónico. Mejora tus sesiones de estudio con esta original sudadera con capucha de Lofi Girl. En un solo color para una apariencia limpia y detallada con tres bordados individuales en el pecho, tienes garantizada una buena compañía durante tus estudios.', '62d7f468302a8.png', '85% algodón, 15% poliéster', 'talla M', 50, 0, 69.90, 11, 1, 1, 1, 18),
    ('Nighttime Radio Hoodie', 'Para los noctámbulos que prefieren la paz y la tranquilidad de trabajar hasta altas horas de la noche. Esta sudadera con capucha original de Lofi Girl es una prenda básica para tu guardarropa. En un color para una apariencia limpia e impreso con la ilustración de la radio nocturna en el frente, este es un artículo imprescindible para cualquier fan de Lofi Girl.', '62d7f4c29cac3.png', '85% cotton, 15% polyester', 'talla S', 50, 0, 69.90, 11, 1, 1, 1, 18),
	('Lofi Girl & Friends Sweatshirt', 'Un peluche, una niña y un gato: no podemos nombrar un trío más icónico. Mejora tus sesiones de estudio con esta original sudadera de Lofi Girl. En un solo color para una apariencia limpia y detallada con tres bordados individuales en el pecho, tienes garantizada una buena compañía durante tus estudios.', '62d9569ebfc2f.png', '85% algodón, 15% poliéster', 'talla M', 50, 0, 59.90, 11, 1, 1, 1, 18),
    ('Nighttime Radio Sweatshirt', 'Para los noctámbulos que prefieren la paz y la tranquilidad de trabajar hasta altas horas de la noche. Esta sudadera con capucha original de Lofi Girl es una prenda básica para tu guardarropa. En un color para una apariencia limpia e impreso con la ilustración de la radio nocturna en el frente, este es un artículo imprescindible para cualquier fan de Lofi Girl.', '62d95880e9eb6.png', '85% cotton, 15% polyester', 'talla S', 50, 0, 62.90, 11, 1, 1, 1, 19),
	('Lofi Girl Signature Sweatshirt', 'Refresca tus básicos diarios con esta sudadera original de Lofi Girl. En un color para una apariencia limpia y firmada con una caligrafía de la firma Lofi Girl simple pero elegante bordada en el frente, puedes mostrar tu apoyo a la marca en un ambiente sutil y relajado.', '62d95bc72dc85.png', '85% algodón, 15% poliéster', 'talla M', 50, 10, 59.90, 11, 1, 1, 1, 19),
    ('Large LOFI Logo  Hoodie', 'Haz una declaración con esta original sudadera con capucha de Lofi Girl. En un color para una apariencia limpia y terminado con un logotipo "LO-FI" simple pero audaz bordado en el frente, es el atuendo perfecto para que el mundo sepa que escuchas lofi hip hop.', '62d973715222b.png', '85% cotton, 15% polyester', 'talla S', 50, 0, 69.90, 11, 1, 1, 1, 19),
	('Lofi Girl Logo Hoodie', 'A veces menos es más. Mejora tu guardarropa con esta sudadera con capucha original de Lofi Girl. En un color para una apariencia limpia y firmado con un logotipo Lofi Girl simple pero elegante en el frente, puede mostrar su apoyo a la marca mientras se mantiene cómodo y relajado.', '62d974e8909c6.png', '85% algodón, 15% poliéster', 'talla M', 50, 0, 59.90, 11, 1, 1, 1, 19),
    ('Lofi Girl Signature Sweatshirt Girl', 'Para los noctámbulos que prefieren la paz y la tranquilidad de trabajar hasta altas horas de la noche. Esta sudadera con capucha original de Lofi Girl es una prenda básica para tu guardarropa. En un color para una apariencia limpia e impreso con la ilustración de la radio nocturna en el frente, este es un artículo imprescindible para cualquier fan de Lofi Girl.', '62d9766be672c.png', '85% cotton, 15% polyester', 'talla S', 50, 10, 59.90, 10, 1, 1, 1, 19),
	('Red Lofi Girl - Sweatshirt', 'Refresca tus básicos diarios con esta sudadera original de Lofi Girl. En un color para una apariencia limpia y firmada con una caligrafía de la firma Lofi Girl simple pero elegante bordada en el frente, puedes mostrar tu apoyo a la marca en un ambiente sutil y relajado.', '62d9798d58f00.png', '85% algodón, 15% poliéster', 'talla M', 50, 5, 59.90, 1, 1, 1, 1, 19),
    ('Lofi Girl & Friends TShirt', 'Un peluche, una niña y un gato: no podemos nombrar un trío más icónico. Mejora tus sesiones de estudio con esta original camiseta de Lofi Girl, confeccionada en 100% algodón para máxima comodidad y suavidad. En un solo color para una apariencia limpia y detallada con tres bordados individuales en el pecho, tienes garantizada una buena compañía durante tus estudios.', '62d9793aed6d5.png', '85% cotton, 15% polyester', 'talla S', 50, 15, 34.90, 6, 1, 1, 1, 19),
	('Lofi Girl Logo TShirt', 'A veces menos es más. Mejora tu guardarropa con esta sudadera con capucha original de Lofi Girl. En un color para una apariencia limpia y firmado con un logotipo Lofi Girl simple pero elegante en el frente, puede mostrar su apoyo a la marca mientras se mantiene cómodo y relajado.', '62d97a44f1b36.png', '85% algodón, 15% poliéster', 'talla M', 50, 10, 34.90, 3, 1, 1, 1, 19);

INSERT INTO public."tbproducto"("nombre_producto", "descripcion", "material", "tamanio", "existencias", "porcentaje_descuento", "precio_producto", "idcolor", "id_marca", "iddistribuidor", "idestado_producto", "idsubcategoria_producto")
    VALUES 
    ('Skyler Stripe Outdoor Rug', 'Para las mesas de afuera', 'Tela de algodón', 'Grande 18x12', 4, 0, 9.60, 2, 3, 3, 1, 3),
    ('LeiDrail', 'Luces solares para exteriores, recargan con luz solar', 'Metal', 'pequeño 20x7', 4, 0, 8.60, 4, 2, 2, 1, 1),
	('Camisa Lofi Color Negro', 'Camisa de algodón masculina con estampado de diseño Lofi Girl', 'Algodón', 'M', 20, 0, 29.99, 11, 1, 1, 1, 18),
    ('Lofi Girl Logo - Manga Larga', 'Con esta sudadera original de Lofi Girl. En un color para una apariencia limpia y firmado con un logotipo Lofi Girl simple pero elegante en el frente, puede mostrar su apoyo a la marca mientras se mantiene cómodo y relajado.', '-85%algodón, 15%poliéster', 'M', 36, 0, 44.90, 11, 1, 1, 1, 18),
    ('LO-FI grande. Logotipo - Sudadera con capucha', 'Ccon esta original sudadera con capucha de Lofi Girl. En un color para una apariencia limpia y terminado con un logotipo "LO-FI" simple pero audaz bordado en el frente, es el atuendo perfecto para que el mundo sepa que escuchas lofi hip hop.', '-85%algodón, 15%poliéster', 'M', 36, 0, 54.90, 11, 1, 1, 1, 18),
    ('Reloj Rolex 702', 'Reloj de mano de oro de 24k', 'oro', 'M', 10, 20, 250.00, 10, 6, 6, 1, 17),
    ('Reloj Rolex 505', 'Reloj de mano de oro de 24k', 'oro', 'M', 10, 20, 250.00, 10, 6, 6, 1, 17),
    ('Pulsera de carbon', 'Pulsera hecha de carbon', 'carbon', 'L', 5, 20, 10.00, 10, 2, 3, 1, 16),
    ('Brazalete de cuero', 'Brazalete de cuero color cafe', 'cuero', 'M', 10, 20, 5.00, 9, 1, 1, 1, 16),
    ('Camiseta de hombre Sivar', 'Camiseta de algodon de hombre', 'algodon', 'M', 10, 20, 11.00, 2, 14, 14, 1, 18);

INSERT INTO tbimagen_producto (imagen_producto, idproducto) VALUES
    ('62f2bbb2676d0.png', 28),
    ('62f2bc1c64bb9.png', 28),
    ('62f2bc8766762.png', 28),
    ('62f2bcba9848e.png', 28);

INSERT INTO public."tbimagen_producto"("imagen_producto", "idproducto")
    VALUES ('image1.png',2),
    ('image2.png',2),
    ('image3.png', 2),
    ('image4.png',2),
    ('image11.png', 6),
    ('image2.png',6),
    ('image21.png', 7),
    ('image22.png',7);

INSERT INTO public."tbtipo_empleado"("tipo_empleado")
    VALUES ('Administrador'), ('Repartidor');

INSERT INTO public."tbestado_empleado"("nombre_estado")
    VALUES ('Activo'), ('Inactivo'), ('Vacaciones');

INSERT INTO public."tbempleado"("nombre_empleado", "apellido_empleado", "duiempleado", "nitempleado", "telefono_empleado", "correo_empleado", "fecha_nacimiento_empleado", "idtipo_empleado", "idestado_empleado")
    VALUES ('Lenny Adrián', 'Elías Sánchez', '85961246-9', '1234-123456-123-4', '8888-8888', 'lenny@gmail.com', '2003-08-08', 1, 1),
    ('Aimee Vanessa', 'Osorio Canales', '44518967-4', '5284-165489-521-2', '4444-4444', 'canelita@gmail.com', '2004-05-07', 2, 1),
    ('Erick', 'Chinchilla', '26598743-8', '1568-689546-568-2', '7777-7777', 'erick@gmail.com', '2004-11-10', 2, 1),
    ('Mangandi', 'Cardona', '05864277-1', '6698-154879-354-3', '5555-5555', 'manga@gmail.com', '2003-10-04', 2, 1),
    ('Nelson', 'Peña', '45789954-1', '3214-658412-567-9', '9999-9999', 'nelson@gmail.com', '2003-04-12', 1, 1);

INSERT INTO public."tbestado_usuario_c"("estado_usuario_c")
VALUES ('Activo'), ('Inactivo');

INSERT INTO public."tbtipo_usuario_e"(
	"tipo_usuario_e")
	VALUES ('Administrador'),('Repartidor');

INSERT INTO public."tbestado_usuario_e"(
	"estado_usuario_e")
	VALUES ('Activo'), ('Inactivo');

INSERT INTO public."tbusuario_empleado"("usuario_e", "contrasena_e", "idempleado", "idtipo_usuario_e", "idestado_usuario_e")
	VALUES ('lenny', '1234', 1, 1, 1),
('aimee', '1234', 2, 2, 1),
('erick', '1234', 3, 1, 1),
('mangandi', '1234', 4, 1, 1),
('nelson', '1234', 5, 1, 1);

INSERT INTO public."tbusuario_cliente"("usuario_c", "contrasena_c", "nombre_cliente", "apellido_cliente", "correo_cliente", "telefono_cliente", "direccion_cliente", "fecha_creacion", "idestado_usuario_c")
	VALUES ('lenny', '1234', 'Lenny Adrián', 'Elías Sánchez', 'lennyx004@gmail.com', '7852-5487', 'Mejicanos, San Salvador', '2022-07-20', 1),
('aimee', '1234', 'Aimee Vanessa', 'Osorio Canales', 'aimee08@gmail.com', '1258-9467' ,'Mejicanos, San Salvador', '2022-07-17', 1),
('nelson', '1234', 'Nelson Daniel', 'Peña Pineda', 'nelson@gmail.com', '7895-9467' ,'Mejicanos, San Salvador', '2022-07-16', 1),
('mangandi', '1234', 'Rodrigo Gabriel', 'Mangandi Cardona', 'mangandi@gmail.com', '6952-5487', 'Mejicanos, San Salvador', '2022-07-15', 1),
('fatima', '123', 'Fatima Rocio', 'Lopez Franco', 'fatima08@gmail.com', '8795-4587' ,'San Martin, San Salvador', '2022-07-14', 1),
('sofia', '123', 'Sofia Bautista', 'Hernandez Martinez', 'sofi45@gmail.com', '8145-4587' ,'Colonia Escalon, San Salvador', '2022-07-14', 1),
('gabriela', '123', 'Gabriela Susana', 'Mendez Barrera', 'Gaby7u7@gmail.com', '1459-8956' ,'Mejicanos, San Salvador', '2022-07-14', 1),
('oliver', '123', 'Oliver Alejandro', 'Erazo Reyes', 'oliver01@gmail.com', '7789-8454' ,'Olocuilta, La Paz', '2022-07-8', 1),
('erick', '123', 'Erick Salvador', 'Chinchilla Chiquillo', 'ericku@gmail.com', '7741-5748' ,'San Salvador, San Salvador', '2022-07-8', 1),
('jesus', '123', 'Jesus Gerardo', 'Esquivel Ramirez', 'jesusDK@gmail.com', '8894-5545' ,'Mejicanos, San Salvador', '2022-07-1', 1),
('sey', '123', 'Sey Guadalupe', 'Alvarado Najarro', 'xmxxs@gmail.com', '8898-4243' ,'Mejicanos, San Salvador', '2022-07-3', 1),
('geissel', '123', 'Geissel Mireya', 'Hernandez Ramos', 'geissel24@gmail.com', '6588-4984' ,'San Martin, San Salvador', '2022-07-25', 1);

INSERT INTO tbusuario_cliente (usuario_c,contrasena_c,nombre_cliente,apellido_cliente,correo_cliente,telefono_cliente,direccion_cliente,fecha_creacion,idestado_usuario_c)
VALUES ('quis.arcu@yahoo.org','21776681','Kyra Ori','Hopkins Banks','kyraori@gmail.com','2350-5378','Maecenas ornare egestas ligula. Nullam feugiat placerat velit. Quisque varius. Nam porttitor scelerisque neque. Nullam nisl. Maecenas malesuada fringilla est.','2022-09-29',1),
    ('nisi.dictum@google.net','17639815','Galvin Jonah','Casey Anthony','galvinjonah@gmail.com','3288-1793','Sed nec metus facilisis lorem tristique aliquet. Phasellus fermentum convallis ligula. Donec luctus aliquet odio. Etiam ligula tortor, dictum eu,','2022-07-23',1),
    ('dui.fusce@icloud.com','18821870','Daryl Plato','Eaton Solomon','darylplato6672@gmail.com','1757-4051','est arcu ac orci. Ut semper pretium neque. Morbi quis urna. Nunc quis arcu vel quam dignissim pharetra. Nam ac','2022-08-22',1),
    ('eget.nisi@yahoo.ca','74235040','Griffin Abbot','Wade Dunlap','griffinabbot@gmail.com','2319-2420','Donec egestas. Aliquam nec enim. Nunc ut erat. Sed nunc est, mollis non, cursus non, egestas a, dui. Cras pellentesque.','2022-07-21',1),
    ('at@icloud.ca','26467684','Nyssa Chancellor','Richmond Sharp','nyssachancellor6332@gmail.com','1464-2668','tincidunt tempus risus. Donec egestas. Duis ac arcu. Nunc mauris. Morbi non sapien molestie orci tincidunt adipiscing. Mauris molestie pharetra','2022-09-10',1),
    ('lectus.convallis.est@protonmail.org','56511232','Trevor Zephania','Schneider Hull','trevorzephania6028@gmail.com','1261-6683','lacus. Ut nec urna et arcu imperdiet ullamcorper. Duis at lacus. Quisque purus sapien, gravida non, sollicitudin a, malesuada id,','2022-08-27',1),
    ('nisl.nulla@protonmail.couk','33244632','Xander Venus','Goff Dyer','xandervenus2503@gmail.com','3458-8014','vitae dolor. Donec fringilla. Donec feugiat metus sit amet ante. Vivamus non lorem vitae odio sagittis semper. Nam tempor diam','2022-09-22',1),
    ('accumsan.laoreet.ipsum@protonmail.couk','19387833','Ingrid Kevin','Sheppard Rosales','ingridkevin6563@gmail.com','5302-2226','tortor nibh sit amet orci. Ut sagittis lobortis mauris. Suspendisse aliquet molestie tellus. Aenean egestas hendrerit neque. In ornare sagittis','2022-09-30',1),
    ('vitae.semper.egestas@protonmail.couk','79196178','Sasha Micah','Kim Marsh','sashamicah@gmail.com','2231-9602','Integer aliquam adipiscing lacus. Ut nec urna et arcu imperdiet ullamcorper. Duis at lacus. Quisque purus sapien, gravida non, sollicitudin','2022-07-03',1),
    ('pede.nec@aol.com','62111562','Lillith Merrill','Hull Willis','lillithmerrill6896@gmail.com','8252-5384','Morbi sit amet massa. Quisque porttitor eros nec tellus. Nunc lectus pede, ultrices a, auctor non, feugiat nec, diam. Duis','2022-07-18',1),
    ('dui.fusce@aol.edu','27240524','Solomon Alyssa','Rogers Barrera','solomonalyssa8393@gmail.com','2764-4583','magna. Praesent interdum ligula eu enim. Etiam imperdiet dictum magna. Ut tincidunt orci quis lectus. Nullam suscipit, est ac facilisis','2022-07-10',1),
    ('lectus.quis@google.net','06267021','Cheyenne Bert','Hansen Bell','cheyennebert@gmail.com','4469-7745','Quisque tincidunt pede ac urna. Ut tincidunt vehicula risus. Nulla eget metus eu erat semper rutrum. Fusce dolor quam, elementum','2022-10-29',1),
    ('orci.lobortis.augue@icloud.org','42977638','Merrill Ella','Henry Harvey','merrillella@gmail.com','1096-1202','Nunc mauris elit, dictum eu, eleifend nec, malesuada ut, sem. Nulla interdum. Curabitur dictum. Phasellus in felis. Nulla tempor augue','2022-07-17',1),
    ('nisi.dictum@icloud.com','44833870','Garrison Odysseus','Cortez Lara','garrisonodysseus@gmail.com','3546-5836','sodales nisi magna sed dui. Fusce aliquam, enim nec tempus scelerisque, lorem ipsum sodales purus, in molestie tortor nibh sit','2022-10-16',1),
    ('id.risus@hotmail.net','43439528','Ira Allistair','Palmer Carrillo','iraallistair5813@gmail.com','0587-8772','magnis dis parturient montes, nascetur ridiculus mus. Proin vel arcu eu odio tristique pharetra. Quisque ac libero nec ligula consectetuer','2022-08-23',1),
    ('aliquam@protonmail.ca','09676522','Quail Illiana','Peck Jacobs','quaililliana@gmail.com','8172-0270','tempus mauris erat eget ipsum. Suspendisse sagittis. Nullam vitae diam. Proin dolor. Nulla semper tellus id nunc interdum feugiat. Sed','2022-09-08',1),
    ('sit@protonmail.couk','52917462','Daniel Leigh','Nicholson Garza','danielleigh@gmail.com','3414-9672','facilisis lorem tristique aliquet. Phasellus fermentum convallis ligula. Donec luctus aliquet odio. Etiam ligula tortor, dictum eu, placerat eget, venenatis','2022-09-06',1),
    ('a.ultricies.adipiscing@google.couk','27007657','Jerome Marsden','Levine Stephens','jeromemarsden2907@gmail.com','4913-4439','felis. Nulla tempor augue ac ipsum. Phasellus vitae mauris sit amet lorem semper auctor. Mauris vel turpis. Aliquam adipiscing lobortis','2022-09-08',1),
    ('ligula.consectetuer.rhoncus@protonmail.net','98755968','Cheryl Leigh','Bass Sherman','cherylleigh4923@gmail.com','7191-5237','magna. Nam ligula elit, pretium et, rutrum non, hendrerit id, ante. Nunc mauris sapien, cursus in, hendrerit consectetuer, cursus et,','2022-07-29',1),
    ('congue.a@aol.net','45648324','Dalton Devin','Roach George','daltondevin@gmail.com','3102-6973','fermentum arcu. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae Phasellus ornare. Fusce mollis. Duis','2022-09-26',1),
    ('sed@protonmail.com','25517044','Cooper Aaron','Allen Mann','cooperaaron2006@gmail.com','7063-3835','risus, at fringilla purus mauris a nunc. In at pede. Cras vulputate velit eu sem. Pellentesque ut ipsum ac mi','2022-07-25',1),
    ('sodales.nisi@protonmail.edu','89282104','Hector Paki','Rhodes Cochran','hectorpaki6397@gmail.com','4286-4072','sed sem egestas blandit. Nam nulla magna, malesuada vel, convallis in, cursus et, eros. Proin ultrices. Duis volutpat nunc sit','2022-10-01',1),
    ('mauris@aol.net','68908087','Quamar Caldwell','Wagner Burch','quamarcaldwell@gmail.com','4376-1583','quis urna. Nunc quis arcu vel quam dignissim pharetra. Nam ac nulla. In tincidunt congue turpis. In condimentum. Donec at','2022-07-20',1),
    ('elit.nulla@icloud.edu','00566637','Alden Mia','Dean David','aldenmia5365@gmail.com','5627-8787','Aliquam ornare, libero at auctor ullamcorper, nisl arcu iaculis enim, sit amet ornare lectus justo eu arcu. Morbi sit amet','2022-10-26',1),
    ('interdum@aol.ca','95223345','Whilemina Holly','Prince Mays','whileminaholly6142@gmail.com','6574-4742','sagittis felis. Donec tempor, est ac mattis semper, dui lectus rutrum urna, nec luctus felis purus ac tellus. Suspendisse sed','2022-09-03',1),
    ('cursus.et@aol.edu','29677374','Bert Hector','Dickerson Rios','berthector@gmail.com','2667-8074','lorem, luctus ut, pellentesque eget, dictum placerat, augue. Sed molestie. Sed id risus quis diam luctus lobortis. Class aptent taciti','2022-07-05',1),
    ('ridiculus@outlook.couk','77523079','Whitney Walter','Hodges Carver','whitneywalter@gmail.com','1496-1234','tempor lorem, eget mollis lectus pede et risus. Quisque libero lacus, varius et, euismod et, commodo at, libero. Morbi accumsan','2022-07-08',1),
    ('placerat.eget@hotmail.ca','57363174','Shea Roanna','Roth Waller','shearoanna@gmail.com','4351-0229','risus. Donec nibh enim, gravida sit amet, dapibus id, blandit at, nisi. Cum sociis natoque penatibus et magnis dis parturient','2022-08-27',1),
    ('ante.bibendum@google.couk','23215323','Gareth Joan','Simon Briggs','garethjoan6109@gmail.com','8749-4737','convallis in, cursus et, eros. Proin ultrices. Duis volutpat nunc sit amet metus. Aliquam erat volutpat. Nulla facilisis. Suspendisse commodo','2022-07-14',1),
    ('aenean.eget.metus@hotmail.com','67684771','Rudyard Rhona','Reynolds Adams','rudyardrhona@gmail.com','8879-4822','quis massa. Mauris vestibulum, neque sed dictum eleifend, nunc risus varius orci, in consequat enim diam vel arcu. Curabitur ut','2022-09-18',1),
    ('ac.turpis.egestas@outlook.com','54757557','Rhea Hilel','Cruz Gross','rheahilel@gmail.com','8666-3586','risus. Donec egestas. Aliquam nec enim. Nunc ut erat. Sed nunc est, mollis non, cursus non, egestas a, dui. Cras','2022-09-13',1),
    ('curabitur.massa@aol.ca','63203728','Dante Gray','Chase Fields','dantegray@gmail.com','1271-5322','sit amet orci. Ut sagittis lobortis mauris. Suspendisse aliquet molestie tellus. Aenean egestas hendrerit neque. In ornare sagittis felis. Donec','2022-09-22',1),
    ('nisi.nibh@hotmail.org','62048414','Cassidy Savannah','Riley Bowen','cassidysavannah4849@gmail.com','9574-8444','porttitor eros nec tellus. Nunc lectus pede, ultrices a, auctor non, feugiat nec, diam. Duis mi enim, condimentum eget, volutpat','2022-08-27',1),
    ('lobortis.ultrices.vivamus@yahoo.couk','66367833','Amena Dennis','Ortega Cleveland','amenadennis9769@gmail.com','0266-2332','mus. Donec dignissim magna a tortor. Nunc commodo auctor velit. Aliquam nisl. Nulla eu neque pellentesque massa lobortis ultrices. Vivamus','2022-09-26',1),
    ('elit@hotmail.ca','40061727','Bruce Ignatius','Odom Norris','bruceignatius2325@gmail.com','5523-7496','scelerisque, lorem ipsum sodales purus, in molestie tortor nibh sit amet orci. Ut sagittis lobortis mauris. Suspendisse aliquet molestie tellus.','2022-07-22',1),
    ('tempus@google.net','26097297','Castor Pearl','Gamble Grimes','castorpearl1465@gmail.com','7853-3351','Phasellus libero mauris, aliquam eu, accumsan sed, facilisis vitae, orci. Phasellus dapibus quam quis diam. Pellentesque habitant morbi tristique senectus','2022-09-09',1),
    ('amet.nulla@outlook.couk','75271931','Kirby Brett','Lloyd Simon','kirbybrett@gmail.com','2718-9101','Sed eu nibh vulputate mauris sagittis placerat. Cras dictum ultricies ligula. Nullam enim. Sed nulla ante, iaculis nec, eleifend non,','2022-10-12',1),
    ('hendrerit.consectetuer.cursus@google.ca','79389296','Hayfa Whilemina','Pruitt Heath','hayfawhilemina@gmail.com','6908-1935','tellus eu augue porttitor interdum. Sed auctor odio a purus. Duis elementum, dui quis accumsan convallis, ante lectus convallis est,','2022-08-24',1),
    ('laoreet.lectus@outlook.couk','13217900','Oliver Channing','Lee Salazar','oliverchanning@gmail.com','4860-7600','egestas rhoncus. Proin nisl sem, consequat nec, mollis vitae, posuere at, velit. Cras lorem lorem, luctus ut, pellentesque eget, dictum','2022-07-23',1),
    ('in.consectetuer.ipsum@outlook.ca','08407913','Unity Julian','Brock Owen','unityjulian@gmail.com','6867-1161','fames ac turpis egestas. Aliquam fringilla cursus purus. Nullam scelerisque neque sed sem egestas blandit. Nam nulla magna, malesuada vel,','2022-10-17',1),
    ('aliquam.nec@protonmail.net','26482771','Ariana Martena','Salas Weber','arianamartena9690@gmail.com','3634-2648','blandit congue. In scelerisque scelerisque dui. Suspendisse ac metus vitae velit egestas lacinia. Sed congue, elit sed consequat auctor, nunc','2022-09-05',1),
    ('duis.dignissim@icloud.net','86554997','Caryn Keelie','Huber Humphrey','carynkeelie9777@gmail.com','8722-7830','vel est tempor bibendum. Donec felis orci, adipiscing non, luctus sit amet, faucibus ut, nulla. Cras eu tellus eu augue','2022-09-24',1),
    ('pellentesque.massa@yahoo.com','52457755','Acton Adara','Wynn Long','actonadara@gmail.com','7016-7627','ac turpis egestas. Fusce aliquet magna a neque. Nullam ut nisi a odio semper cursus. Integer mollis. Integer tincidunt aliquam','2022-08-03',1),
    ('cubilia@icloud.com','30555846','Gannon Myra','Bentley Duran','gannonmyra@gmail.com','2846-2166','Donec sollicitudin adipiscing ligula. Aenean gravida nunc sed pede. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus','2022-09-14',1),
    ('hendrerit.neque@yahoo.net','80078250','Zahir Claire','Herman Huffman','zahirclaire9035@gmail.com','9449-1815','mi, ac mattis velit justo nec ante. Maecenas mi felis, adipiscing fringilla, porttitor vulputate, posuere vulputate, lacus. Cras interdum. Nunc','2022-09-04',1),
    ('praesent.eu@google.edu','16932861','Yeo India','Hebert Hamilton','yeoindia5592@gmail.com','7247-5730','mauris, aliquam eu, accumsan sed, facilisis vitae, orci. Phasellus dapibus quam quis diam. Pellentesque habitant morbi tristique senectus et netus','2022-07-29',1),
    ('lobortis.ultrices@icloud.org','42837266','Kelly Natalie','Flowers Medina','kellynatalie@gmail.com','5604-5324','convallis in, cursus et, eros. Proin ultrices. Duis volutpat nunc sit amet metus. Aliquam erat volutpat. Nulla facilisis. Suspendisse commodo','2022-09-17',1),
    ('semper.nam.tempor@aol.ca','78961579','Wayne Bruno','Estes Walters','waynebruno7478@gmail.com','3847-8040','aliquam eu, accumsan sed, facilisis vitae, orci. Phasellus dapibus quam quis diam. Pellentesque habitant morbi tristique senectus et netus et','2022-07-22',1),
    ('vulputate.dui@protonmail.edu','89752535','Nigel Chandler','Phillips Gill','nigelchandler@gmail.com','2212-1612','blandit congue. In scelerisque scelerisque dui. Suspendisse ac metus vitae velit egestas lacinia. Sed congue, elit sed consequat auctor, nunc','2022-07-07',1),
    ('laoreet@yahoo.ca','74997378','Basia Mannix','Kennedy Snow','basiamannix@gmail.com','6455-8341','et malesuada fames ac turpis egestas. Aliquam fringilla cursus purus. Nullam scelerisque neque sed sem egestas blandit. Nam nulla magna,','2022-10-07',1),
    ('nonummy.ac@icloud.net','77122612','Daquan Jolene','Odom Neal','daquanjolene9959@gmail.com','4213-5518','nisi a odio semper cursus. Integer mollis. Integer tincidunt aliquam arcu. Aliquam ultrices iaculis odio. Nam interdum enim non nisi.','2022-07-02',1),
    ('mauris@icloud.net','41823812','Demetria Xena','James Campos','demetriaxena@gmail.com','5184-5215','Quisque fringilla euismod enim. Etiam gravida molestie arcu. Sed eu nibh vulputate mauris sagittis placerat. Cras dictum ultricies ligula. Nullam','2022-08-25',1),
    ('purus.sapien@outlook.ca','30715347','Hasad Sacha','Horton Perkins','hasadsacha996@gmail.com','5003-8846','venenatis lacus. Etiam bibendum fermentum metus. Aenean sed pede nec ante blandit viverra. Donec tempus, lorem fringilla ornare placerat, orci','2022-07-06',1),
    ('donec.nibh@aol.com','64566476','Isabelle Geraldine','Preston Navarro','isabellegeraldine@gmail.com','0988-2741','non, cursus non, egestas a, dui. Cras pellentesque. Sed dictum. Proin eget odio. Aliquam vulputate ullamcorper magna. Sed eu eros.','2022-08-07',1),
    ('velit.sed.malesuada@aol.edu','37287738','Ulysses Buckminster','Christensen Bass','ulyssesbuckminster@gmail.com','7773-7433','sed, est. Nunc laoreet lectus quis massa. Mauris vestibulum, neque sed dictum eleifend, nunc risus varius orci, in consequat enim','2022-08-22',1),
    ('aliquam.fringilla@outlook.net','47868625','Burton Sydnee','Dillard Manning','burtonsydnee1435@gmail.com','4617-7941','lacus. Quisque imperdiet, erat nonummy ultricies ornare, elit elit fermentum risus, at fringilla purus mauris a nunc. In at pede.','2022-08-15',1),
    ('montes@aol.ca','13058481','Palmer Devin','Arnold Castro','palmerdevin9773@gmail.com','2610-5537','Suspendisse aliquet molestie tellus. Aenean egestas hendrerit neque. In ornare sagittis felis. Donec tempor, est ac mattis semper, dui lectus','2022-10-07',1),
    ('a.aliquet.vel@yahoo.ca','42987606','Regina Alika','Mcdonald Ellison','reginaalika@gmail.com','8421-8788','ipsum nunc id enim. Curabitur massa. Vestibulum accumsan neque et nunc. Quisque ornare tortor at risus. Nunc ac sem ut','2022-07-31',1),
    ('et@google.couk','66402699','Hedwig Shellie','Wilkins Santos','hedwigshellie5744@gmail.com','8887-7846','ut odio vel est tempor bibendum. Donec felis orci, adipiscing non, luctus sit amet, faucibus ut, nulla. Cras eu tellus','2022-08-08',1),
    ('lectus.ante@aol.org','55425312','Denton Melodie','Gutierrez Gregory','dentonmelodie@gmail.com','8163-5708','Nullam vitae diam. Proin dolor. Nulla semper tellus id nunc interdum feugiat. Sed nec metus facilisis lorem tristique aliquet. Phasellus','2022-10-18',1),
    ('metus.facilisis@hotmail.edu','39060714','Julian Keiko','Cook Simmons','juliankeiko@gmail.com','5718-7769','faucibus orci luctus et ultrices posuere cubilia Curae Phasellus ornare. Fusce mollis. Duis sit amet diam eu dolor egestas rhoncus.','2022-09-17',1),
    ('pharetra.ut@outlook.net','85431331','Indira Marsden','Walsh Cabrera','indiramarsden@gmail.com','6128-4033','ut erat. Sed nunc est, mollis non, cursus non, egestas a, dui. Cras pellentesque. Sed dictum. Proin eget odio. Aliquam','2022-07-29',1),
    ('ac.libero@aol.couk','25434089','Cheyenne Mallory','Stuart Bruce','cheyennemallory@gmail.com','3907-6661','dis parturient montes, nascetur ridiculus mus. Proin vel arcu eu odio tristique pharetra. Quisque ac libero nec ligula consectetuer rhoncus.','2022-08-24',1),
    ('eget@protonmail.org','06432274','Briar Sybil','Gilliam Meadows','briarsybil@gmail.com','5699-8855','sed, facilisis vitae, orci. Phasellus dapibus quam quis diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac','2022-07-07',1),
    ('est.ac.mattis@outlook.org','51647502','Joel Colt','Walter Gallagher','joelcolt@gmail.com','8279-7382','scelerisque scelerisque dui. Suspendisse ac metus vitae velit egestas lacinia. Sed congue, elit sed consequat auctor, nunc nulla vulputate dui,','2022-08-04',1),
    ('etiam.ligula.tortor@hotmail.ca','06315282','Emma Hedwig','Johnson Erickson','emmahedwig6928@gmail.com','7565-6893','sed orci lobortis augue scelerisque mollis. Phasellus libero mauris, aliquam eu, accumsan sed, facilisis vitae, orci. Phasellus dapibus quam quis','2022-07-27',1),
    ('cum@google.net','91551036','Morgan Liberty','Joseph Cortez','morganliberty4830@gmail.com','6139-4725','Nulla interdum. Curabitur dictum. Phasellus in felis. Nulla tempor augue ac ipsum. Phasellus vitae mauris sit amet lorem semper auctor.','2022-07-01',1),
    ('amet.risus@outlook.couk','17227815','Fay Victoria','Fuller Rowland','fayvictoria1219@gmail.com','8743-4475','dolor. Nulla semper tellus id nunc interdum feugiat. Sed nec metus facilisis lorem tristique aliquet. Phasellus fermentum convallis ligula. Donec','2022-08-14',1),
    ('mauris.magna.duis@google.edu','85446627','Ariana Jarrod','Hunt Maynard','arianajarrod@gmail.com','5621-7642','Pellentesque ut ipsum ac mi eleifend egestas. Sed pharetra, felis eget varius ultrices, mauris ipsum porta elit, a feugiat tellus','2022-07-13',1),
    ('nonummy.fusce@protonmail.couk','57877546','Kaitlin Benedict','Gordon Morrison','kaitlinbenedict2628@gmail.com','3334-4948','egestas rhoncus. Proin nisl sem, consequat nec, mollis vitae, posuere at, velit. Cras lorem lorem, luctus ut, pellentesque eget, dictum','2022-10-16',1),
    ('curabitur.vel@icloud.com','58513188','Macon Maris','Macdonald Andrews','maconmaris1467@gmail.com','1837-2237','at, nisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin vel nisl. Quisque fringilla euismod','2022-09-18',1),
    ('sapien.imperdiet@icloud.net','37750507','Xavier Kirsten','Pickett Ingram','xavierkirsten2549@gmail.com','9835-3474','tincidunt, neque vitae semper egestas, urna justo faucibus lectus, a sollicitudin orci sem eget massa. Suspendisse eleifend. Cras sed leo.','2022-08-10',1),
    ('dolor@outlook.couk','24939700','Fuller Eve','Montgomery Merrill','fullereve9044@gmail.com','6447-7540','ac mattis velit justo nec ante. Maecenas mi felis, adipiscing fringilla, porttitor vulputate, posuere vulputate, lacus. Cras interdum. Nunc sollicitudin','2022-10-06',1),
    ('nec.euismod@yahoo.org','17478477','Jordan Octavia','Peck Guthrie','jordanoctavia7312@gmail.com','2312-6198','fringilla purus mauris a nunc. In at pede. Cras vulputate velit eu sem. Pellentesque ut ipsum ac mi eleifend egestas.','2022-07-18',1),
    ('ac.tellus@protonmail.com','21062795','Dane Hyatt','Winters Bennett','danehyatt6529@gmail.com','2975-3554','eu augue porttitor interdum. Sed auctor odio a purus. Duis elementum, dui quis accumsan convallis, ante lectus convallis est, vitae','2022-10-20',1),
    ('enim.consequat@protonmail.edu','75815233','Chaim Lyle','Velez Mooney','chaimlyle@gmail.com','5858-1739','ipsum. Curabitur consequat, lectus sit amet luctus vulputate, nisi sem semper erat, in consectetuer ipsum nunc id enim. Curabitur massa.','2022-07-13',1),
    ('tincidunt.neque@icloud.edu','32143015','Skyler Virginia','Hogan Pruitt','skylervirginia5189@gmail.com','1618-8133','eu erat semper rutrum. Fusce dolor quam, elementum at, egestas a, scelerisque sed, sapien. Nunc pulvinar arcu et pede. Nunc','2022-07-14',1),
    ('fringilla.mi@icloud.net','19443775','Ashely Channing','Benson Berry','ashelychanning540@gmail.com','6533-8596','senectus et netus et malesuada fames ac turpis egestas. Fusce aliquet magna a neque. Nullam ut nisi a odio semper','2022-09-23',1),
    ('placerat.velit.quisque@protonmail.edu','31339779','Jessamine Ryan','Hughes Salas','jessamineryan2171@gmail.com','8566-8362','porttitor eros nec tellus. Nunc lectus pede, ultrices a, auctor non, feugiat nec, diam. Duis mi enim, condimentum eget, volutpat','2022-10-24',1),
    ('risus@yahoo.com','61433102','Chava Amos','Guerra Fernandez','chavaamos4194@gmail.com','4818-3799','tincidunt, nunc ac mattis ornare, lectus ante dictum mi, ac mattis velit justo nec ante. Maecenas mi felis, adipiscing fringilla,','2022-09-21',1),
    ('porttitor.interdum@hotmail.org','78764762','Rosalyn Zorita','Olson Holloway','rosalynzorita@gmail.com','1287-3853','lectus sit amet luctus vulputate, nisi sem semper erat, in consectetuer ipsum nunc id enim. Curabitur massa. Vestibulum accumsan neque','2022-10-30',1),
    ('arcu.eu@hotmail.org','15552745','Fiona Imogene','Snider Nelson','fionaimogene@gmail.com','4497-4428','dictum cursus. Nunc mauris elit, dictum eu, eleifend nec, malesuada ut, sem. Nulla interdum. Curabitur dictum. Phasellus in felis. Nulla','2022-10-26',1),
    ('elit.a@hotmail.net','96036266','Irma Isadora','Flowers Cabrera','irmaisadora@gmail.com','1215-6102','tincidunt orci quis lectus. Nullam suscipit, est ac facilisis facilisis, magna tellus faucibus leo, in lobortis tellus justo sit amet','2022-08-21',1),
    ('aliquet.molestie@google.com','71071718','Helen Dorothy','Mays Brady','helendorothy8376@gmail.com','9621-2272','Donec est. Nunc ullamcorper, velit in aliquet lobortis, nisi nibh lacinia orci, consectetuer euismod est arcu ac orci. Ut semper','2022-07-27',1),
    ('morbi.vehicula@hotmail.net','79422287','Bernard Kelly','Barnes Whitaker','bernardkelly@gmail.com','7972-9048','Quisque varius. Nam porttitor scelerisque neque. Nullam nisl. Maecenas malesuada fringilla est. Mauris eu turpis. Nulla aliquet. Proin velit. Sed','2022-08-07',1),
    ('augue.scelerisque@google.ca','33178514','Charles Ethan','Olson Cruz','charlesethan7113@gmail.com','5119-4272','Sed nunc est, mollis non, cursus non, egestas a, dui. Cras pellentesque. Sed dictum. Proin eget odio. Aliquam vulputate ullamcorper','2022-07-30',1),
    ('ut.nulla@yahoo.com','50657507','Cairo Karly','Bridges Rutledge','cairokarly@gmail.com','1122-8142','egestas. Fusce aliquet magna a neque. Nullam ut nisi a odio semper cursus. Integer mollis. Integer tincidunt aliquam arcu. Aliquam','2022-10-29',1),
    ('sed.nunc@protonmail.org','20218238','Kareem Peter','Mccullough Cote','kareempeter@gmail.com','1151-2327','ornare, lectus ante dictum mi, ac mattis velit justo nec ante. Maecenas mi felis, adipiscing fringilla, porttitor vulputate, posuere vulputate,','2022-08-17',1),
    ('tristique.neque@aol.net','66382347','Maris Clinton','Acosta Mason','marisclinton@gmail.com','7877-7025','Donec fringilla. Donec feugiat metus sit amet ante. Vivamus non lorem vitae odio sagittis semper. Nam tempor diam dictum sapien.','2022-07-06',1),
    ('aliquam.arcu@icloud.edu','26505277','Tana Dieter','Bartlett Fernandez','tanadieter4589@gmail.com','6555-0321','nec, eleifend non, dapibus rutrum, justo. Praesent luctus. Curabitur egestas nunc sed libero. Proin sed turpis nec mauris blandit mattis.','2022-09-17',1),
    ('penatibus.et.magnis@protonmail.edu','86538254','Karly Cade','Harvey Frost','karlycade2835@gmail.com','1548-1738','risus. Donec nibh enim, gravida sit amet, dapibus id, blandit at, nisi. Cum sociis natoque penatibus et magnis dis parturient','2022-10-12',1),
    ('et.lacinia.vitae@hotmail.org','00032655','Naida Igor','Stokes Donovan','naidaigor8628@gmail.com','5347-4867','molestie arcu. Sed eu nibh vulputate mauris sagittis placerat. Cras dictum ultricies ligula. Nullam enim. Sed nulla ante, iaculis nec,','2022-07-19',1),
    ('mi.felis@protonmail.com','27011366','Kaitlin Katelyn','O''connor Bradford','kaitlinkatelyn@gmail.com','2952-7356','gravida. Praesent eu nulla at sem molestie sodales. Mauris blandit enim consequat purus. Maecenas libero est, congue a, aliquet vel,','2022-09-18',1),
    ('lorem@outlook.org','24835265','Colin Orlando','Lewis Sampson','colinorlando@gmail.com','5621-9333','vel turpis. Aliquam adipiscing lobortis risus. In mi pede, nonummy ut, molestie in, tempus eu, ligula. Aenean euismod mauris eu','2022-09-02',1),
    ('natoque@yahoo.couk','78496523','Robin Lacy','Williamson Mckee','robinlacy3269@gmail.com','6816-5862','eu turpis. Nulla aliquet. Proin velit. Sed malesuada augue ut lacus. Nulla tincidunt, neque vitae semper egestas, urna justo faucibus','2022-09-11',1),
    ('donec.luctus@hotmail.couk','58737768','Hop Patricia','Wilkinson Johnson','hoppatricia7138@gmail.com','3796-9732','Mauris eu turpis. Nulla aliquet. Proin velit. Sed malesuada augue ut lacus. Nulla tincidunt, neque vitae semper egestas, urna justo','2022-08-24',1),
    ('et@outlook.com','71526008','Armando Brendan','Cobb Glover','armandobrendan5342@gmail.com','3427-0479','iaculis, lacus pede sagittis augue, eu tempor erat neque non quam. Pellentesque habitant morbi tristique senectus et netus et malesuada','2022-10-19',1),
    ('erat@hotmail.org','32225154','Malcolm Wesley','Taylor Barrett','malcolmwesley9646@gmail.com','8972-4763','ligula. Nullam enim. Sed nulla ante, iaculis nec, eleifend non, dapibus rutrum, justo. Praesent luctus. Curabitur egestas nunc sed libero.','2022-07-09',1),
    ('commodo@icloud.org','78522978','Arthur Tatum','Clarke Rodriguez','arthurtatum@gmail.com','8215-0361','lobortis ultrices. Vivamus rhoncus. Donec est. Nunc ullamcorper, velit in aliquet lobortis, nisi nibh lacinia orci, consectetuer euismod est arcu','2022-08-15',1),
    ('conubia.nostra@outlook.com','45186041','Maris Delilah','Best Bates','marisdelilah2081@gmail.com','7737-6425','eget, dictum placerat, augue. Sed molestie. Sed id risus quis diam luctus lobortis. Class aptent taciti sociosqu ad litora torquent','2022-10-08',1);

INSERT INTO public."tbestado_factura"("estado_factura")
	VALUES ('Cancelada'),('Pendiente'),('Retrasada'),('Entregando'), ('Editando');

INSERT INTO public."tbtipo_pago"("tipo_pago")
	VALUES ('Debito'),('Chivo Wallet');

INSERT INTO public."tbfactura"("fecha_factura", "monto_total", "idestado_factura", "idusuario_c")
	VALUES
    ('2022-06-05', 66.39, 2, 1),
    ('2022-06-05', 45.00, 1, 1),
    ('2022-06-05', 44.90, 1, 1),
    ('2022-06-05', 54.90, 1, 1),
	('2022-03-15', 17.20, 2, 2),
	('2022-03-16', 8.60, 2, 3),
	('2022-03-17', 8.60, 2, 4),
    ('2022-03-15', 17.20, 2, 5),
	('2022-03-16', 54.90, 3, 6),
	('2022-03-08', 250.00, 3, 7),
    ('2022-03-05', 20.00, 1, 8),
    ('2022-03-25', 44.90, 2, 9),
    ('2022-03-20', 11.00, 4, 10),
    ('2022-03-22', 500.00, 2, 1),
    ('2022-03-23', 98.58, 2, 2),
    ('2022-03-24', 20.00, 2, 3),
    ('2022-03-25', 68.5, 2, 5),
    ('2022-03-15', 290.99, 3, 4),
    ('2022-03-11', 48.19, 3, 5),
    ('2022-03-27', 49.90, 2, 6);

INSERT INTO tbfactura (fecha_factura,monto_total,idestado_factura,idtipo_pago,idusuario_c,idusuario_e)
VALUES
    ('2022-08-07',125.22,1,1,6,4),
    ('2022-08-06',34.73,1,1,6,2),
    ('2022-07-31',197.49,1,1,10,1),
    ('2022-08-27',159.72,1,1,5,2),
    ('2022-08-01',166.42,1,2,2,1),
    ('2022-08-10',190.49,1,2,9,4),
    ('2022-08-04',167.47,1,2,3,4),
    ('2022-08-27',45.19,1,1,10,3),
    ('2022-08-10',39.15,1,2,3,2),
    ('2022-08-06',79.84,1,2,9,4),
    ('2022-08-10',152.75,1,1,1,3),
    ('2022-08-17',153.26,1,1,5,3),
    ('2022-08-04',179.50,1,1,10,5),
    ('2022-08-18',64.88,1,1,3,4),
    ('2022-08-24',87.21,1,1,11,3),
    ('2022-07-24',74.48,1,2,11,2),
    ('2022-08-10',161.95,1,2,10,2),
    ('2022-08-08',144.57,1,1,3,2),
    ('2022-07-27',92.07,1,1,5,2),
    ('2022-08-28',52.21,1,2,7,3),
    ('2022-07-31',44.93,1,2,12,3),
    ('2022-07-26',155.11,1,1,11,3),
    ('2022-08-13',172.93,1,1,3,4),
    ('2022-08-22',93.20,1,1,12,2),
    ('2022-08-12',106.90,1,2,2,2),
    ('2022-08-14',127.07,1,2,6,4),
    ('2022-08-09',183.95,1,2,1,1),
    ('2022-08-12',89.18,1,2,2,2),
    ('2022-08-18',54.71,1,1,12,3),
    ('2022-07-23',31.71,1,2,11,3),
    ('2022-08-17',118.37,1,2,7,4),
    ('2022-08-14',173.74,1,2,4,4),
    ('2022-07-31',109.71,1,2,3,4),
    ('2022-08-03',167.45,1,1,5,2),
    ('2022-08-20',153.50,1,2,10,4),
    ('2022-08-05',118.47,1,2,9,5),
    ('2022-08-04',97.03,1,1,8,2),
    ('2022-08-11',185.44,1,1,7,3),
    ('2022-07-30',181.76,1,2,8,2),
    ('2022-07-22',148.51,1,2,3,1),
    ('2022-08-18',183.76,1,1,4,3),
    ('2022-08-16',37.28,1,1,5,4),
    ('2022-08-21',55.32,1,2,2,4),
    ('2022-08-15',137.95,1,1,8,1),
    ('2022-08-28',89.20,1,2,11,3),
    ('2022-07-30',94.64,1,2,3,2),
    ('2022-07-23',116.82,1,1,3,5),
    ('2022-08-21',195.69,1,2,9,3),
    ('2022-08-13',162.15,1,1,7,3),
    ('2022-08-20',78.44,1,1,11,5),
    ('2022-08-30',48.96,1,2,5,2),
    ('2022-07-27',31.24,1,1,12,4),
    ('2022-07-30',70.81,1,1,5,2),
    ('2022-07-30',26.31,1,2,4,4),
    ('2022-08-05',47.97,1,1,7,4),
    ('2022-08-26',92.68,1,2,11,3),
    ('2022-08-20',70.86,1,2,2,1),
    ('2022-08-01',127.05,1,1,12,2),
    ('2022-08-06',185.44,1,1,4,3),
    ('2022-08-01',104.76,1,1,4,1),
    ('2022-07-25',150.59,1,1,3,3),
    ('2022-08-25',79.91,1,1,9,1),
    ('2022-07-25',140.27,1,1,2,3),
    ('2022-08-28',45.02,1,2,11,5),
    ('2022-08-25',116.55,1,2,3,3),
    ('2022-07-23',171.17,1,1,9,3),
    ('2022-07-31',34.55,1,2,2,5),
    ('2022-08-11',27.34,1,2,9,2),
    ('2022-08-15',68.52,1,2,3,4),
    ('2022-08-11',189.90,1,1,10,3),
    ('2022-08-09',37.13,1,1,7,4),
    ('2022-08-22',142.85,1,1,10,2),
    ('2022-08-30',38.76,1,2,9,3),
    ('2022-08-14',145.55,1,1,2,2),
    ('2022-08-25',163.20,1,1,2,4),
    ('2022-07-26',32.03,1,2,7,4),
    ('2022-08-15',162.72,1,2,3,2),
    ('2022-08-17',100.88,1,1,10,3),
    ('2022-08-12',150.02,1,2,9,4),
    ('2022-07-27',87.29,1,1,10,4),
    ('2022-08-23',57.73,1,2,4,1),
    ('2022-08-10',90.34,1,1,2,4),
    ('2022-08-15',132.96,1,2,12,1),
    ('2022-08-28',140.92,1,2,7,3),
    ('2022-08-10',32.44,1,2,9,3),
    ('2022-07-25',112.74,1,1,9,4),
    ('2022-08-30',127.88,1,2,5,1),
    ('2022-07-31',118.60,1,2,1,4),
    ('2022-08-29',199.71,1,1,4,3),
    ('2022-08-12',107.53,1,1,9,1),
    ('2022-08-16',82.82,1,2,3,3),
    ('2022-08-03',42.40,1,2,3,4),
    ('2022-07-25',27.65,1,1,5,3),
    ('2022-07-26',53.87,1,1,1,2),
    ('2022-08-27',174.28,1,1,3,3),
    ('2022-07-29',41.49,1,2,12,3),
    ('2022-08-30',74.18,1,2,6,2),
    ('2022-08-27',154.61,1,1,10,1),
    ('2022-07-22',187.24,1,1,3,5),
    ('2022-08-29',182.88,1,2,7,4);

INSERT INTO tbdetalle_factura (total_producto,precio_actual,cantidad_descuento,cantidad_producto,idfactura,idproducto)
VALUES
    (47.20,161.51,16,4,106,14),
    (27.52,34.55,10,3,77,39),
    (124.75,65.63,20,1,118,3),
    (74.67,71.08,16,4,33,28),
    (199.53,89.88,18,2,36,5),
    (182.49,159.71,13,2,95,18),
    (132.70,192.66,11,3,80,28),
    (183.13,166.05,17,2,76,17),
    (106.88,131.81,16,1,69,3),
    (112.73,45.02,18,2,73,37),
    (50.41,106.78,16,2,50,10),
    (145.64,121.85,19,4,75,30),
    (71.95,173.02,11,2,46,35),
    (118.79,189.30,10,3,37,37),
    (96.32,168.90,19,1,18,5),
    (73.92,89.31,13,3,71,11),
    (67.86,153.53,11,1,119,16),
    (109.10,173.73,11,3,58,11),
    (129.03,54.29,20,2,90,16),
    (174.80,122.20,17,2,52,10),
    (179.12,48.72,7,2,87,13),
    (87.12,45.14,12,3,86,2),
    (35.39,182.40,18,3,104,20),
    (198.30,118.91,10,3,120,21),
    (116.07,65.41,7,2,58,28),
    (107.53,42.28,19,2,20,8),
    (48.01,122.08,10,1,25,20),
    (161.95,151.62,12,1,55,31),
    (128.34,69.12,16,3,47,38),
    (51.05,171.02,19,3,92,14),
    (48.43,193.64,17,3,3,20),
    (198.17,46.11,7,2,105,12),
    (194.42,181.25,10,2,44,35),
    (131.97,124.14,11,4,6,6),
    (153.07,125.62,14,3,24,5),
    (152.41,171.79,6,2,69,24),
    (123.15,55.51,15,1,22,31),
    (34.78,118.44,11,3,27,30),
    (198.13,196.95,9,1,47,34),
    (96.82,49.05,9,4,24,36),
    (122.85,152.39,13,3,40,26),
    (56.14,34.79,15,4,67,37),
    (99.95,165.45,5,3,110,28),
    (27.99,180.73,9,3,101,29),
    (91.24,164.87,6,3,16,15),
    (174.56,69.77,15,2,35,3),
    (78.11,128.25,7,3,65,23),
    (87.62,82.10,16,2,27,40),
    (170.63,174.65,7,3,15,28),
    (193.53,65.46,10,1,21,14),
    (45.45,88.15,13,2,78,29),
    (193.13,110.61,16,3,60,36),
    (88.44,107.93,18,3,9,10),
    (194.50,197.82,15,3,59,6),
    (176.69,77.25,16,4,114,20),
    (90.72,55.47,6,2,50,17),
    (29.05,79.99,7,2,51,35),
    (147.10,131.34,7,2,108,29),
    (104.34,152.90,14,3,120,40),
    (55.54,141.24,12,2,50,35),
    (150.03,119.18,12,2,61,10),
    (101.20,56.80,10,3,87,39),
    (190.08,152.46,14,3,12,40),
    (29.57,179.82,14,3,87,37),
    (122.57,82.41,8,4,103,38),
    (49.88,141.07,13,2,113,16),
    (130.39,77.25,16,3,78,25),
    (42.60,173.08,19,2,107,21),
    (53.45,46.95,15,3,48,28),
    (77.36,179.15,17,2,24,34),
    (35.52,40.18,16,2,8,6),
    (39.23,152.65,8,1,54,34),
    (197.99,130.44,11,1,9,18),
    (72.52,192.15,16,4,87,9),
    (136.04,139.57,12,3,79,12),
    (41.48,147.32,16,3,16,9),
    (103.89,146.89,9,2,10,35),
    (47.07,25.89,18,3,35,9),
    (127.34,34.62,20,2,47,9),
    (127.06,35.66,11,2,40,21),
    (84.82,113.91,10,4,77,26),
    (96.41,30.22,15,2,60,14),
    (39.43,116.41,10,2,49,19),
    (124.53,88.24,5,3,53,23),
    (135.19,188.21,19,1,71,31),
    (192.97,157.37,13,3,110,18),
    (167.82,140.22,20,4,101,28),
    (52.51,70.68,12,2,63,7),
    (94.33,115.50,14,2,24,30),
    (83.48,58.15,7,3,92,33),
    (98.78,51.23,8,3,40,15),
    (33.36,144.90,20,1,22,24),
    (146.68,55.28,7,4,63,20),
    (105.87,90.97,11,2,114,37),
    (133.59,189.61,14,3,97,16),
    (66.33,61.34,16,2,54,27),
    (56.07,119.21,8,3,53,35),
    (176.50,187.32,17,1,111,11),
    (79.29,104.00,9,2,89,3),
    (59.18,93.09,8,2,59,23),
    (158.99,126.29,10,4,111,36),
    (57.24,104.57,9,4,117,22),
    (191.57,190.11,17,4,52,17),
    (108.61,125.37,19,3,117,14),
    (153.57,105.28,6,3,29,27),
    (115.57,149.97,13,1,55,31),
    (89.10,110.71,19,3,78,17),
    (136.36,90.43,9,4,3,40),
    (139.42,26.15,9,2,31,31),
    (44.79,151.62,6,2,118,21),
    (158.29,136.86,11,1,6,14),
    (135.14,25.24,13,4,88,16),
    (44.37,49.17,14,2,26,13),
    (165.69,147.39,11,1,56,3),
    (102.49,160.01,18,3,31,39),
    (96.60,73.06,9,4,4,29),
    (54.94,48.49,19,1,36,7),
    (176.40,196.92,17,2,100,21),
    (123.28,27.05,15,1,89,16),
    (123.22,109.80,20,2,82,18),
    (114.48,99.94,10,2,70,7),
    (31.69,190.14,7,4,84,36),
    (164.09,64.12,10,1,108,16),
    (62.98,54.48,5,3,99,34),
    (82.40,195.97,7,1,84,38),
    (122.69,108.39,18,2,12,35),
    (120.14,66.89,12,2,29,35),
    (97.79,52.44,10,1,94,29),
    (155.43,55.51,18,2,3,22),
    (106.96,136.81,13,2,111,24),
    (172.22,122.31,16,2,13,10),
    (168.44,94.41,14,4,10,3),
    (29.70,74.32,12,2,70,38),
    (99.28,161.50,11,3,92,36),
    (171.38,143.33,15,4,6,3),
    (192.08,198.79,9,2,58,17),
    (93.83,87.32,18,2,72,26),
    (144.54,199.95,11,3,48,9),
    (192.87,75.91,17,4,4,24),
    (102.22,193.26,19,2,99,32),
    (33.60,107.33,9,1,51,24),
    (55.13,73.25,8,3,88,38),
    (57.82,63.95,9,2,89,35),
    (167.70,198.82,12,3,71,36),
    (121.81,81.07,10,4,18,19),
    (154.79,191.02,15,2,75,6),
    (68.75,189.55,14,4,101,4),
    (114.69,28.45,10,2,11,24),
    (149.76,61.99,12,1,111,34),
    (108.15,178.47,13,3,86,35),
    (196.12,84.70,11,1,2,26),
    (126.99,99.49,13,2,12,24),
    (68.85,92.18,18,1,83,17),
    (159.32,190.23,6,4,57,35),
    (155.47,78.61,13,2,50,35),
    (80.99,174.79,15,4,97,19),
    (101.86,43.01,12,2,15,16),
    (104.67,46.48,15,1,4,25),
    (40.99,107.35,14,4,5,5),
    (147.45,50.17,17,3,1,37),
    (123.14,89.80,11,4,33,11),
    (59.33,168.46,16,4,18,39),
    (196.60,152.24,7,2,92,14),
    (163.42,179.83,11,4,103,9),
    (104.15,93.44,18,3,92,38),
    (189.23,184.45,9,2,22,12),
    (56.45,171.83,6,2,21,19),
    (144.46,140.52,14,2,15,16),
    (42.95,92.08,18,3,11,7),
    (85.52,182.12,6,1,56,38),
    (163.68,64.04,15,3,53,3),
    (163.44,140.86,6,3,117,31),
    (153.92,135.37,17,2,119,11),
    (122.91,71.10,15,2,104,8),
    (48.32,167.17,12,2,58,12),
    (64.67,67.02,20,4,56,19),
    (131.11,44.18,9,4,88,12),
    (164.23,71.96,15,4,55,32),
    (113.58,78.61,7,2,37,10),
    (130.02,142.41,5,2,34,17),
    (152.62,133.02,7,2,9,25),
    (141.52,85.53,17,1,100,38),
    (195.75,69.91,20,2,93,22),
    (33.67,31.86,16,2,101,40),
    (127.44,40.52,19,1,88,27),
    (176.98,125.09,11,2,9,33),
    (133.75,51.45,9,1,19,35),
    (136.31,32.52,15,4,21,39),
    (193.14,84.71,7,4,96,12),
    (109.20,176.38,11,3,78,13),
    (182.94,108.06,12,2,34,15),
    (143.91,32.11,17,3,111,33),
    (99.14,48.21,17,2,90,21),
    (50.47,46.93,18,1,81,6),
    (67.35,70.91,18,4,92,17),
    (172.58,35.79,12,1,64,35),
    (126.71,193.20,18,2,17,30),
    (184.90,133.50,13,3,27,21),
    (35.80,143.45,19,3,15,10),
    (101.05,98.81,17,4,4,22),
    (62.13,155.94,12,4,34,5),
    (83.72,78.57,12,2,63,25),
    (152.89,88.16,9,3,35,19),
    (148.03,121.34,16,1,110,22),
    (103.15,184.72,17,1,88,19),
    (70.61,185.19,7,2,40,28),
    (49.87,63.01,6,4,101,6),
    (175.49,178.67,16,1,77,33),
    (147.86,98.61,8,3,39,35),
    (166.60,79.77,13,2,62,29),
    (123.40,59.65,16,3,108,22),
    (72.71,148.36,8,1,26,39),
    (132.52,64.71,18,2,88,34),
    (149.86,54.24,5,4,92,33),
    (195.10,59.50,11,3,92,28),
    (68.41,147.34,12,1,94,13),
    (115.03,111.47,8,2,56,7),
    (67.75,52.31,16,1,81,33),
    (40.00,45.42,13,3,71,26),
    (149.15,53.79,13,3,30,34),
    (130.14,178.34,8,1,8,29),
    (158.98,193.55,14,3,74,26),
    (93.46,118.59,9,4,37,27),
    (166.87,197.35,6,2,9,7),
    (44.38,122.73,7,3,88,19),
    (148.61,146.52,6,2,63,15),
    (47.69,65.09,8,4,110,33),
    (136.97,149.25,16,2,34,3),
    (69.96,97.35,8,1,20,9),
    (139.97,31.96,18,3,37,11),
    (77.20,132.97,16,3,108,4),
    (187.97,30.05,15,2,113,15),
    (114.60,134.22,7,3,61,19),
    (27.73,60.45,5,3,65,23),
    (71.15,112.09,6,4,70,19),
    (71.10,121.66,13,3,42,30),
    (38.56,26.51,10,2,112,28),
    (148.73,137.54,14,3,18,31),
    (28.09,47.67,12,2,112,36),
    (83.54,81.11,17,1,78,21),
    (147.03,41.38,9,2,89,13),
    (192.60,177.12,14,1,103,25),
    (43.69,104.95,13,1,111,22),
    (77.15,76.27,15,1,34,27),
    (65.17,25.18,7,1,109,23),
    (180.62,149.44,11,2,120,25),
    (97.65,84.11,17,2,90,25),
    (157.39,136.69,5,3,20,37),
    (43.79,34.92,20,2,89,26),
    (33.31,46.57,12,3,94,28);

INSERT INTO public."tbenvio_pedido"("direccion_entrega_pedido", "fecha_entrega_pedido", "idfactura")
	VALUES ('Metrocentro', '2022-06-06', 1),
    ('25 Avenida Sur y, Alameda Franklin Delano Roosevelt, San Salvador', '2022-06-06', 2),
    ('25 Avenida Sur y, Alameda Franklin Delano Roosevelt, San Salvador', '2022-05-29', 3),
    ('Colonia Escalon Av23 Casa#14', '2022-03-22', 4),
	('Plaza Mundo', '2022-03-23', 5),
	('Mejicanos, Colonia Zacamil residencial universitaria', '2022-03-24', 6),
    ('San Martin, San Salvador', '2022-03-29', 7),
	('Santo Tomas AV 14 casa #12', '2022-03-20', 8),
	('Colonia Monte Carmelo, Ilopango', '2022-03-17', 9),
	('Residencia España, Av34 Casa #67', '2022-03-25', 10),
    ('Calle vista al lago en circulo cercano al restaurante, casa #89', '2022-05-25', 11),
    ('Instituto Técnico Ricaldone', '2022-05-25', 12),
    ('Monseñor Romero y Final Calle 5 de Noviembre entre 21ª y 23ª', '2022-05-29', 13),
    ('SAN SALVADOR. Dirección: 87 Ave. Sur, No. 7, Colonia Escalón, San Salvador', '2022-05-29', 14),
    ('25 Avenida Sur y, Alameda Franklin Delano Roosevelt, San Salvador', '2022-05-29', 15);

INSERT INTO public."tbestado_valoracion"(
	"estado_valoracion")
	VALUES ('Visible'),('Invisible');

INSERT INTO tbvaloraciones(
	valoraciones, "reseña", fecha_publicacion, idproducto, idestado_valoracion, idusuario_c)
	VALUES (4, 'Me ha gustado el producto','2022-05-06', 2, 1, 1),
    (4, 'Me ha gustado el producto','2022-05-06', 3, 1, 1);

INSERT INTO tbinventario (cantidad,precio_unitario,fecha_entrega, fecha_inicio_ventas, idproducto) VALUES
    (24,110.56,'2022-10-19','2021-08-09',11),
    (17,34.33,'2022-09-08','2021-08-24',24),
    (33,174.11,'2022-07-30','2021-08-03',19),
    (19,86.97,'2022-09-26','2021-08-07',5),
    (31,18.18,'2022-10-28','2021-09-13',32),
    (41,91.37,'2022-10-06','2021-10-04',39),
    (17,137.24,'2022-09-11','2021-09-02',4),
    (30,16.85,'2022-10-07','2021-08-15',28),
    (26,155.70,'2022-10-13','2021-10-29',14),
    (32,106.05,'2022-10-31','2021-10-30',34),
    (26,51.23,'2022-09-20','2021-10-22',7),
    (15,139.13,'2022-10-03','2021-07-29',12),
    (5,13.16,'2022-09-26','2021-08-31',18),
    (12,86.71,'2022-09-26','2021-08-31',32),
    (24,77.64,'2022-08-15','2021-09-29',9),
    (9,145.34,'2022-10-22','2021-09-17',6),
    (39,155.63,'2022-10-30','2021-10-16',35),
    (8,179.91,'2022-10-05','2021-08-15',32),
    (37,96.21,'2022-08-17','2021-10-10',13),
    (42,95.77,'2022-09-18','2021-09-01',7),
    (9,58.01,'2022-09-03','2021-10-10',36),
    (48,148.21,'2022-08-04','2021-09-04',21),
    (17,120.08,'2022-09-02','2021-09-10',13),
    (17,59.87,'2022-08-01','2021-09-18',3),
    (34,90.97,'2022-09-03','2021-09-15',15),
    (40,45.84,'2022-10-23','2021-08-06',38),
    (46,88.79,'2022-08-10','2021-08-02',14),
    (49,74.62,'2022-07-31','2021-10-30',21),
    (5,183.10,'2022-09-09','2021-09-17',23),
    (33,85.47,'2022-09-06','2021-10-11',2),
    (44,169.59,'2022-09-04','2021-09-27',39),
    (8,89.49,'2022-08-11','2021-09-10',29),
    (41,144.61,'2022-07-30','2021-08-03',3),
    (49,158.93,'2022-09-23','2021-09-05',33),
    (18,78.35,'2022-08-20','2021-08-02',30),
    (38,69.39,'2022-09-22','2021-08-03',7),
    (20,43.76,'2022-09-11','2021-07-29',28),
    (21,54.59,'2022-10-22','2021-10-17',33),
    (26,191.13,'2022-09-07','2021-09-03',36),
    (21,173.12,'2022-10-23','2021-09-06',17),
    (33,143.97,'2022-08-12','2021-07-29',34),
    (16,97.58,'2022-09-30','2021-09-19',31),
    (20,39.67,'2022-10-27','2021-09-27',2),
    (24,108.29,'2022-09-22','2021-10-17',4),
    (36,10.04,'2022-08-04','2021-10-17',37),
    (25,46.53,'2022-10-09','2021-10-21',23),
    (6,103.24,'2022-10-17','2021-08-06',18),
    (14,180.99,'2022-07-27','2021-08-24',37),
    (11,52.47,'2022-08-16','2021-09-11',5),
    (12,14.61,'2022-08-04','2021-10-02',29),
    (8,17.12,'2022-08-01','2021-09-15',31),
    (36,32.09,'2022-08-24','2021-10-20',16),
    (11,52.17,'2022-09-02','2021-09-25',13),
    (26,155.67,'2022-08-26','2021-10-08',34),
    (15,154.45,'2022-07-25','2021-09-20',10),
    (9,47.27,'2022-10-17','2021-07-25',7),
    (31,13.87,'2022-09-22','2021-09-22',22),
    (33,186.95,'2022-10-01','2021-08-12',13),
    (49,57.27,'2022-08-20','2021-09-14',22),
    (43,55.74,'2022-09-27','2021-08-11',14),
    (29,50.72,'2022-09-18','2021-08-08',23),
    (16,23.66,'2022-08-21','2021-10-13',8),
    (31,25.38,'2022-10-26','2021-09-10',34),
    (30,159.91,'2022-09-18','2021-10-12',5),
    (16,185.90,'2022-10-23','2021-10-08',30),
    (15,188.31,'2022-08-17','2021-10-30',21),
    (29,128.30,'2022-10-07','2021-09-15',23),
    (34,186.25,'2022-10-07','2021-09-03',34),
    (48,109.71,'2022-09-30','2021-10-14',16),
    (30,40.48,'2022-09-09','2021-08-24',20),
    (45,17.63,'2022-09-22','2021-10-15',28),
    (33,56.04,'2022-08-29','2021-09-25',18),
    (6,141.98,'2022-09-09','2021-07-28',37),
    (7,104.73,'2022-08-31','2021-10-19',9),
    (49,61.49,'2022-09-11','2021-09-15',34),
    (28,74.34,'2022-09-25','2021-07-28',7),
    (40,150.89,'2022-08-18','2021-10-13',23),
    (22,52.81,'2022-09-22','2021-08-27',28),
    (31,102.34,'2022-10-18','2021-08-18',16),
    (44,107.47,'2022-08-26','2021-08-27',33),
    (9,55.76,'2022-09-01','2021-09-20',35),
    (12,72.50,'2022-08-04','2021-09-25',30),
    (29,177.45,'2022-08-31','2021-08-01',37),
    (33,134.05,'2022-09-03','2021-09-19',17),
    (24,123.31,'2022-10-22','2021-09-30',34),
    (28,194.81,'2022-09-20','2021-09-12',5),
    (42,142.35,'2022-10-25','2021-08-31',7),
    (10,178.87,'2022-10-14','2021-08-05',16),
    (17,122.65,'2022-10-20','2021-08-06',22),
    (28,50.41,'2022-09-18','2021-08-14',23),
    (28,48.68,'2022-10-01','2021-08-06',32),
    (14,33.99,'2022-10-22','2021-08-18',21),
    (22,46.33,'2022-09-17','2021-08-05',11),
    (39,99.64,'2022-09-03','2021-08-02',38),
    (15,53.50,'2022-09-07','2021-10-19',31),
    (26,161.52,'2022-10-22','2021-10-26',7),
    (23,183.68,'2022-08-26','2021-10-27',25),
    (40,39.73,'2022-08-09','2021-10-09',2),
    (24,21.62,'2022-09-21','2021-10-01',14),
    (26,146.70,'2022-10-09','2021-09-25',31),
    (18,34.24,'2022-09-12','2021-09-19',4),
    (6,32.87,'2022-10-15','2021-08-31',21),
    (18,16.60,'2022-10-30','2021-10-19',13),
    (44,33.32,'2022-09-06','2021-09-17',11),
    (30,62.11,'2022-09-15','2021-09-04',4),
    (8,115.64,'2022-09-14','2021-09-14',19),
    (30,86.98,'2022-08-11','2021-07-30',35),
    (31,37.01,'2022-08-06','2021-09-10',26),
    (49,147.78,'2022-08-08','2021-09-17',11),
    (15,16.11,'2022-08-04','2021-08-10',21),
    (23,58.41,'2022-08-26','2021-10-09',27),
    (22,159.29,'2022-08-31','2021-08-15',4),
    (45,111.55,'2022-10-15','2021-08-22',24),
    (47,110.74,'2022-10-30','2021-09-06',10),
    (44,186.61,'2022-08-05','2021-08-03',31),
    (36,90.84,'2022-08-07','2021-10-27',35),
    (25,140.34,'2022-10-10','2021-10-28',25),
    (20,138.92,'2022-09-17','2021-08-21',34),
    (12,142.09,'2022-10-13','2021-08-22',33),
    (49,142.90,'2022-08-14','2021-07-29',6),
    (32,89.55,'2022-09-09','2021-10-25',17),
    (19,73.91,'2022-10-19','2021-08-04',31),
    (38,139.94,'2022-09-30','2021-08-26',28),
    (16,127.45,'2022-08-10','2021-08-22',21),
    (33,149.64,'2022-10-18','2021-09-05',35),
    (43,100.94,'2022-08-03','2021-09-17',4),
    (41,28.00,'2022-08-28','2021-10-15',28),
    (32,130.99,'2022-10-12','2021-10-29',17),
    (44,161.61,'2022-10-27','2021-08-29',22),
    (42,66.02,'2022-08-31','2021-09-11',28),
    (20,13.49,'2022-08-30','2021-09-22',31),
    (5,50.92,'2022-08-23','2021-10-28',16),
    (17,83.65,'2022-08-20','2021-09-17',3),
    (29,193.97,'2022-08-17','2021-10-07',33),
    (41,19.89,'2022-10-12','2021-09-17',37),
    (26,191.13,'2022-10-01','2021-08-22',20),
    (13,15.13,'2022-10-18','2021-10-03',37),
    (25,166.34,'2022-10-06','2021-08-13',11),
    (17,185.28,'2022-08-02','2021-09-10',15),
    (18,198.51,'2022-09-09','2021-07-26',25),
    (44,195.19,'2022-09-20','2021-08-10',29),
    (36,76.21,'2022-07-31','2021-08-25',34),
    (10,84.33,'2022-10-02','2021-08-07',5),
    (15,74.34,'2022-10-16','2021-07-30',21),
    (38,196.73,'2022-10-19','2021-09-12',36),
    (21,170.51,'2022-09-16','2021-07-26',17),
    (14,22.53,'2022-09-14','2021-08-16',11),
    (39,11.54,'2022-10-24','2021-08-02',25),
    (22,123.46,'2022-08-15','2021-10-14',21),
    (42,170.75,'2022-09-18','2021-10-13',39),
    (31,174.29,'2022-08-25','2021-10-30',31),
    (20,37.06,'2022-09-03','2021-09-14',5),
    (19,63.18,'2022-10-24','2021-08-11',3),
    (37,151.49,'2022-10-28','2021-10-27',4),
    (13,14.98,'2022-10-14','2021-09-27',34),
    (35,154.73,'2022-09-15','2021-09-27',17),
    (28,70.77,'2022-10-31','2021-09-07',24),
    (30,172.50,'2022-09-26','2021-10-22',4),
    (18,93.15,'2022-09-28','2021-09-13',15),
    (23,112.18,'2022-10-02','2021-10-17',22),
    (42,55.94,'2022-08-25','2021-10-09',31),
    (39,157.92,'2022-10-19','2021-08-27',7),
    (8,173.16,'2022-08-06','2021-08-18',22),
    (46,75.59,'2022-07-25','2021-10-17',39),
    (37,15.50,'2022-10-06','2021-09-25',21),
    (9,62.78,'2022-10-27','2021-09-04',33),
    (19,146.86,'2022-10-30','2021-09-07',38),
    (16,79.73,'2022-08-30','2021-08-10',8),
    (21,27.86,'2022-09-28','2021-10-29',5),
    (6,171.08,'2022-07-26','2021-07-28',7),
    (23,84.24,'2022-10-26','2021-10-09',31),
    (16,30.80,'2022-10-27','2021-08-17',2),
    (30,196.94,'2022-09-29','2021-10-07',10),
    (31,90.94,'2022-10-05','2021-10-28',17),
    (6,179.40,'2022-09-17','2021-10-23',23),
    (5,80.79,'2022-08-22','2021-10-17',13),
    (16,176.96,'2022-08-24','2021-08-21',17),
    (48,161.93,'2022-10-01','2021-10-08',11),
    (22,76.72,'2022-08-29','2021-08-31',17),
    (22,113.15,'2022-09-27','2021-10-17',3),
    (48,126.30,'2022-10-07','2021-10-21',28),
    (35,31.62,'2022-09-23','2021-09-14',24),
    (33,99.55,'2022-09-19','2021-08-18',31),
    (46,133.32,'2022-09-01','2021-08-30',10),
    (9,104.34,'2022-08-15','2021-09-25',34),
    (13,95.04,'2022-09-28','2021-09-21',16),
    (31,83.75,'2022-10-10','2021-10-22',6),
    (19,148.42,'2022-07-31','2021-10-19',30),
    (19,142.02,'2022-10-09','2021-10-21',21),
    (48,181.49,'2022-08-18','2021-09-19',34),
    (50,141.07,'2022-09-29','2021-09-18',14),
    (42,23.26,'2022-10-30','2021-08-24',16),
    (31,53.44,'2022-09-10','2021-09-21',37),
    (17,79.80,'2022-10-11','2021-10-11',3),
    (48,144.17,'2022-10-07','2021-10-04',30),
    (30,107.63,'2022-10-05','2021-09-23',39),
    (37,21.39,'2022-08-13','2021-10-26',28),
    (29,165.74,'2022-09-14','2021-08-21',29),
    (20,11.87,'2022-10-03','2021-08-26',10),
    (29,28.55,'2022-10-04','2021-10-17',22),
    (22,124.68,'2022-08-04','2021-08-01',28),
    (8,148.12,'2022-09-01','2021-08-09',30),
    (20,34.66,'2022-09-05','2021-08-13',13),
    (8,157.12,'2022-08-12','2021-08-04',26),
    (23,19.19,'2022-08-10','2021-08-13',40),
    (41,40.04,'2022-09-06','2021-08-09',10),
    (15,23.55,'2022-10-13','2021-09-05',23),
    (32,54.82,'2022-10-26','2021-08-28',14),
    (38,56.71,'2022-10-14','2021-10-01',8),
    (20,81.43,'2022-09-01','2021-10-12',13),
    (40,100.47,'2022-09-04','2021-08-16',28),
    (17,124.02,'2022-08-24','2021-10-04',11),
    (23,62.00,'2022-10-15','2021-09-14',5),
    (16,174.69,'2022-09-11','2021-10-08',4),
    (43,120.21,'2022-09-21','2021-09-29',29),
    (41,128.31,'2022-07-25','2021-08-12',39),
    (33,43.09,'2022-08-10','2021-10-27',8),
    (18,178.07,'2022-09-01','2021-10-02',5),
    (26,26.87,'2022-07-31','2021-09-17',24),
    (34,133.92,'2022-10-12','2021-08-17',9),
    (37,49.61,'2022-09-16','2021-08-04',32),
    (26,165.84,'2022-09-26','2021-10-16',14),
    (26,77.18,'2022-10-29','2021-09-17',14),
    (48,176.50,'2022-08-17','2021-09-07',13),
    (37,152.40,'2022-09-27','2021-08-05',11),
    (17,50.60,'2022-09-24','2021-09-01',21),
    (44,188.26,'2022-10-08','2021-10-22',21),
    (9,69.17,'2022-07-31','2021-08-12',5),
    (45,60.22,'2022-08-28','2021-08-18',23),
    (39,167.86,'2022-09-03','2021-09-23',30),
    (35,80.55,'2022-08-28','2021-09-26',23),
    (42,87.86,'2022-08-04','2021-09-22',19),
    (31,82.22,'2022-09-15','2021-09-10',29),
    (34,92.43,'2022-09-10','2021-08-14',36),
    (41,189.79,'2022-10-01','2021-08-13',37),
    (50,120.54,'2022-09-28','2021-08-16',8),
    (42,181.58,'2022-08-28','2021-09-08',33),
    (49,139.43,'2022-08-15','2021-10-03',23),
    (32,15.57,'2022-08-09','2021-10-27',13),
    (22,170.96,'2022-09-13','2021-09-30',26),
    (48,134.25,'2022-10-03','2021-09-17',13),
    (7,135.09,'2022-09-27','2021-10-29',21),
    (49,127.21,'2022-09-05','2021-09-24',5),
    (44,178.05,'2022-08-05','2021-08-13',34),
    (23,93.25,'2022-08-11','2021-09-01',29),
    (10,51.64,'2022-09-07','2021-07-31',24),
    (47,87.34,'2022-10-26','2021-09-15',7),
    (31,174.33,'2022-09-24','2021-08-03',16),
    (46,173.44,'2022-09-10','2021-10-20',20),
    (6,111.06,'2022-09-11','2021-08-08',34);


---------------------------------------------------INNER JOIN---------------------------------------------------

Select "nombre_empleado", "apellido_empleado", "duiempleado", "telefono_empleado", "fecha_nacimiento_empleado", "correo_empleado", "nitempleado", "tbtipo_empleado"."tipo_empleado", "tbestado_empleado"."nombre_estado"
FROM "tbempleado"
INNER JOIN "tbtipo_empleado" ON "tbtipo_empleado"."idtipo_empleado" = "tbempleado"."idtipo_empleado"
INNER JOIN "tbestado_empleado" ON "tbestado_empleado"."idestado_empleado" = "tbempleado"."idestado_empleado"

-----------------------------UPDATES-----------------------------

UPDATE public."tbfactura" set "monto_total" = 8.60 WHERE "idfactura" = 5;

UPDATE public."tbenvio_pedido" set "direccion_entrega_pedido" = 'Colonia Miramonte' WHERE "idenvio_pedido" = 8;

UPDATE public."tbproducto" set "porcentaje_descuento" = 0 WHERE "idproducto" = 10 AND "existencias">0;

update "tbproducto" set "nombre_producto" = 'HP' where "idproducto"=4;

update "tbmarca" set "nombre_marca" = 'ASUS' where "id_marca"=3;


-----------------------------Consultas-----------------------------
select * from public."tbdistribuidor";
select * from public."tbcategoria";
select * from public."tbsubcategoria_producto";
select * from public."tbcolor";
select * from public."tbmarca";
select * from public."tbestado_producto";
select * from public."tbproducto";
select * from public."tbtipo_empleado";
select * from public."tbestado_empleado";
select * from public."tbempleado";
select * from public."tbestado_usuario_c";
select * from public."tbtipo_usuario_e";
select * from public."tbestado_usuario_e";
select * from public."tbusuario_empleado";
select * from public."tbusuario_cliente";
select * from public."tbestado_factura";
select * from public."tbtipo_pago";
select * from public."tbfactura";
select * from public."tbdetalle_factura";
select * from public."tbenvio_pedido";

-----------------------------Consultas (Group By, Order by, Between)-----------------------------
/*Suma de total a pagar de los productos comprados*/
SELECT SUM(("tbdetalle_factura"."total_producto") * "tbdetalle_factura"."cantidad_producto")
FROM public."tbdetalle_factura", public."tbfactura" 
WHERE "tbdetalle_factura"."idfactura" = "tbfactura"."idfactura" and "tbfactura"."idfactura"=7;

/*Ventas hechas en rango de fechas*/
SELECT * FROM public."tbfactura" 
WHERE "tbfactura"."fecha_factura" BETWEEN '2022-03-17' AND '2022-03-22';

/*Dirección de entrega de pedido en rango de fechas Mayor a menor*/
SELECT "tbenvio_pedido"."direccion_entrega_pedido", "tbenvio_pedido"."fecha_entrega_pedido" 
FROM public."tbenvio_pedido", public."tbfactura", public."tbestado_factura"
WHERE "tbenvio_pedido"."idfactura" =  "tbfactura"."idfactura" 
AND "tbfactura"."idestado_factura" = "tbestado_factura"."idestado_factura" AND "tbestado_factura"."estado_factura" = 'Pendiente' ORDER BY "tbenvio_pedido"."fecha_entrega_pedido" DESC;


---------------------------------------------FUNCIONES---------------------------------------------

--FUNCION QUE MUESTRE SI HAY O NO EXISTENCIAS
create or replace function Rango_Precios_Productos(nombre varchar(60))
returns varchar
as $$
declare 
existencias varchar(40);
valor integer;
begin 
valor = (select "tbproducto"."existencias" FROM public."tbproducto" WHERE "tbproducto"."nombre_producto" = nombre); 
if valor>0 then
    existencias := 'Hay productos';
	return existencias;
else
    existencias := 'No hay productos';
    return existencias;
end if;    
END $$
language 'plpgsql';

select * from public.Rango_Precios_Productos('Brazalete de cuero');


--FUNCION QUE MUESTRE El DINERO OBTENIDO POR LAS VENTAS EN UN RANGO DE FECHA
create or replace function Dinero_Ganado_Rango(fecha1 TIMESTAMP WITHOUT TIME ZONE, fecha2 TIMESTAMP WITHOUT TIME ZONE)
returns numeric
as $$
declare
Monto_Total Numeric(8,2);
begin 
Monto_Total = (select SUM("tbfactura"."monto_total") FROM public."tbfactura" WHERE "tbfactura"."fecha_factura" BETWEEN fecha1 AND fecha2);
    return Monto_Total;
END $$
language 'plpgsql';

select * from public.Dinero_Ganado_Rango('2022-03-10','2022-03-20');


---------------------------------------------FUNCION TABLA---------------------------------------------

--FUNCION QUE MUESTRE PEDIDOS PENDIENTES
create or replace function public.MostrarPedidos(estado varchar(25))
returns TABLE
(
Direccion_Entrega VARCHAR(500),
Fecha_Entrega TIMESTAMP WITHOUT TIME ZONE,
Monto_Total NUMERIC,
Estado_Factura VARCHAR
)
as $$
DECLARE reg RECORD;
BEGIN
for reg IN SELECT "tbenvio_pedido"."direccion_entrega_pedido", "tbenvio_pedido"."fecha_entrega_pedido", "tbfactura"."monto_total", "tbestado_factura"."estado_factura" 
FROM public."tbenvio_pedido", public."tbfactura", public."tbestado_factura" 
WHERE "tbenvio_pedido"."idfactura" = "tbfactura"."idfactura" AND "tbfactura"."idestado_factura" = "tbestado_factura"."idestado_factura" 
AND "tbestado_factura"."estado_factura" = estado
LOOP
Direccion_Entrega := reg."direccion_entrega_pedido";
Fecha_Entrega := reg."fecha_entrega_pedido";
Monto_Total := reg."monto_total";
Estado_Factura := reg."estado_factura";
RETURN NEXT;
	END LOOP;
RETURN;
END $$
LANGUAGE 'plpgsql';

select * from public.MostrarPedidos('Pendiente');

----------------------------------------------------------PROCEDIMIENTOS----------------------------------------------------------
/*Ingresar un distribuidor*/
create or replace procedure public.distribuidorPlaceholder(
nombre_distribuidor VARCHAR(35),
direccion_distribuidor VARCHAR(500),
telefono_distribuidor VARCHAR(25)
)
language 'plpgsql'
as $$
begin
insert into public."tbdistribuidor"
("nombre_distribuidor", "direccion_distribuidor", "telefono_distribuidor")
values (nombre_distribuidor,direccion_distribuidor,telefono_distribuidor);
commit;
end;
$$

call public.distribuidorPlaceholder(VARCHAR(35) 'Artesanías – Regalos Artesanales SV', VARCHAR(500) 'San Salvador', varchar(25)'+503 8592-5248');
select * FROM "tbdistribuidor";

--------------------------------------------
/*Ingresar una marca*/
create or replace procedure public.marcaPlaceholder(
nombre_marca VARCHAR(35),
imagen_marca VARCHAR(1000)
)
language 'plpgsql'
as $$
begin
insert into public."tbmarca"
("nombre_marca", "imagen_marca")
values (nombre_marca,imagen_marca);
commit;
end;
$$

call public.marcaPlaceholder(VARCHAR(35) 'Zara', VARCHAR(1000) '');
select * FROM "tbmarca";

-------------------------------------------------------------
/*Actualizar contacto del distribuidor*/
create or replace procedure public.distribuidorUpdate(
telefono_distribuidor VARCHAR(25)
)
language 'plpgsql'
as $$
begin
update "tbdistribuidor" set
"telefono_distribuidor" =  "telefono_distribuidor" where "iddistribuidor" = 14;
commit;
end;
$$

call public.distribuidorUpdate(varchar(25)'+503 2357 4086');
select * FROM "tbdistribuidor";
----------------------------------------------------------TRIGGERS----------------------------------------------------------

------------------------------------------------------FUNCIÓN O TRIGGER DE INVENTARIO------------------------------------------------------

--FUNCIÓN TRIGGER
CREATE OR REPLACE FUNCTION estado_producto() RETURNS TRIGGER
language plpgsql
as $$
DECLARE existencias1 INTEGER;
DECLARE existencias2 INTEGER;
BEGIN
	existencias1 = (SELECT "cantidad" FROM "tbinventario" WHERE "idinventario" = (SELECT MAX("idinventario") FROM "tbinventario"));
    existencias2 = (SELECT "existencias" FROM "tbproducto", "tbinventario" WHERE "tbproducto"."idproducto" = "tbinventario"."idproducto" AND "idinventario" = (SELECT MAX("idinventario") FROM "tbinventario"));
    UPDATE "tbproducto" set "existencias" = (existencias1+existencias2)
    FROM "tbinventario" 
    WHERE "tbproducto"."idproducto" = (SELECT "idproducto" FROM "tbinventario" WHERE "idinventario" = (SELECT MAX("idinventario") FROM "tbinventario")) AND
    "tbinventario"."fecha_inicio_ventas" < now();
	RETURN NEW;
END; $$

--TRIGGER
CREATE TRIGGER IngresoRegistro
AFTER INSERT
ON "tbinventario"
FOR EACH ROW
EXECUTE FUNCTION estado_producto();

--PROBANDO TRIGGER
INSERT INTO "tbinventario"("cantidad", "precio_unitario", "fecha_entrega", "fecha_inicio_ventas", "idproducto")
	VALUES (10, 15.99, '2022-03-10', '2022-03-12', 1);


SELECT * FROM "tbproducto"
SELECT * FROM "tbinventario"

-------------------------------------------------------------------FUNCIÓN O TRIGGER DE MARCA-------------------------------------------------------------------------

/*El funcionamiento del primer trigger es similar al de una bitacora debido a que lo que hace es cuando se elimina un registro o se actualiza este
queda guardado en la tabla de auditoria, se guarda lo que es el dato eliminado o actualizado, el id del que se elimino, descripcion, el usuario
que lo elimino, la hora y el dia exacto en que todo proceso se desarrollo*/

CREATE TABLE auditoria(
    "idlog" integer,
    "nombre" character varying(250),
    "descripcion" character varying(250), 
    "usuario" character varying(250) NOT NULL,
    "fecha" date NOT NULL,
    "tiempo" time NOT NULL
);


/*Trigger cuando ingresas en Marca*/
CREATE FUNCTION insert_triggers() returns Trigger
as
$$
Declare
usuario Varchar(250) := User;
fecha date := current_date;
tiempo Time := current_time;
begin 
insert into auditoria values(old."id_marca", old."nombre_marca", old."imagen_marca", usuario, fecha, tiempo);
return new;
End
$$
Language plpgsql;

Create Trigger insert_auditoria after DELETE OR UPDATE on "tbmarca"
for each row
execute procedure insert_triggers();

/*probar trigger*/
INSERT INTO public."tbmarca"("nombre_marca")VALUES ('Artesanías–Regalos Artesanales SV');
update "tbmarca" set "nombre_marca" = 'Artesanías–Regalos Artesanales 503' where "id_marca"=17;
SELECT * FROM auditoria;
SELECT * FROM "tbmarca"

--------------------------------------------------------------------------------FUNCIÓN O TRIGGER DE PRODUCTO--------------------------------------------------------------------------------

CREATE FUNCTION insert_producto() returns Trigger
as
$$
Declare
usuario Varchar(250) := User;
fecha date := current_date;
tiempo Time := current_time;
begin 
insert into auditoria values(old."idproducto", old."nombre_producto",old."descripcion", usuario, fecha, tiempo);
return new;
End
$$
Language plpgsql;


Create Trigger insert_auditoria after DELETE OR UPDATE on "tbproducto"
for each row
execute procedure insert_producto();

select * from "tbproducto"
update "tbproducto" set "nombre_producto" = 'Lofi Girl Logo Camiseta Manga Larga' where "idproducto"=4
select * from "tbproducto"
SELECT * FROM auditoria;


--------------------------------------------------Trigger Direccion--------------------------------------------------
--El funcionamiento de este es ingresar el dato de direccion que se envio en Envio Pedido a Tabla Usuario cliente


CREATE OR REPLACE FUNCTION Actualizar_direccion() RETURNS TRIGGER AS $insertar$
	DECLARE 
	BEGIN 
	UPDATE "tbusuario_cliente" SET "direccion_cliente" = NEW."direccion_entrega_pedido" where "idusuario_c" = (SELECT "idusuario_c" FROM public."tbfactura" WHERE "idfactura" =(SELECT MAX("idfactura") FROM "tbfactura"));
	RETURN NEW;
	END;
	$insertar$ LANGUAGE plpgsql;


CREATE TRIGGER insertar_auditoria AFTER INSERT
	ON "tbenvio_pedido" FOR EACH ROW
	EXECUTE PROCEDURE Actualizar_direccion()


INSERT INTO public."tbenvio_pedido"("direccion_entrega_pedido", "fecha_entrega_pedido", "idfactura")
	VALUES ('Residencial las flores casa 98°a', '2022-03-21', 9);
	
SELECT * FROM "tbenvio_pedido";
SELECT * FROM "tbusuario_cliente"

--------------------------------------------------CONSULTAS DE REPORTES--------------------------------------------------

INSERT INTO public."tbestado_valoracion"("estado_valoracion")
	VALUES ('Visible'),
    ('Invisible');

INSERT INTO "tbvaloraciones"(
	"valoraciones", "reseña", "fecha_publicacion", "idproducto", "idestado_valoracion", "idusuario_c")
	VALUES ( 5, 'Muy buen producto', '2022-03-15', 1, 1, 1);
    INSERT INTO "tbvaloraciones"(
	"valoraciones", "reseña", "fecha_publicacion", "idproducto", "idestado_valoracion", "idusuario_c")
	VALUES ( 0, 'Muy mal producto', '2022-03-15', 1, 2, 2);

select * from public."tbvaloraciones" 
where "valoraciones" = '5'

select * from "tbproducto" 
where "id_marca" = 1;

select * from "tbproducto" 
where "idsubcategoria_producto" = 1;

select * from "tbproducto"
where "idestado_producto" = 1;

select * from "tbproducto"
where "porcentaje_descuento" != 0

--------------------------------------------------CONSULTAS DE REPORTES CON RANGO DE FECHAS--------------------------------------------------
/*Reporte para mostrar lo que ha vendido un empleado en un rango de fechas*/
SELECT SUM("monto_total") AS Monto_Total, "tbusuario_empleado"."usuario_e"
FROM public."tbfactura", public."tbusuario_empleado"
WHERE "tbfactura"."idusuario_e" = "tbusuario_empleado"."idusuario_e" AND ("tbfactura"."fecha_factura" BETWEEN '2022-03-01' AND '2022-03-30') GROUP BY "usuario_e"

/*Reporte para saber los envios de pedido en una fecha exacta*/
SELECT * FROM "tbenvio_pedido" WHERE "fecha_entrega_pedido" = '2022-03-20'

/*Reporte para saber que productos han ingresado al inventario en un rango de fechas*/
SELECT * FROM "tbinventario" WHERE "fecha_entrega" BETWEEN '2022-03-01' AND '2022-03-30'