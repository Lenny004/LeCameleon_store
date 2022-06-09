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
    tamaño character varying(50),
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

CREATE TABLE tbinventario (
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
    fecha_factura timestamp without time zone NOT NULL,
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
    fecha_entrega_pedido timestamp without time zone NOT NULL,
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
    VALUES ('Decoración', '629be07ca8673.jpg'), ('Accesorios', '629be06ae1845.jpg'),('Artesanales', '629be073b2933.jpg');


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
    VALUES ('rojo'),
    ('azul'),
    ('verde'),
    ('amarillo'),
    ('morado'),
    ('rosado'),
    ('celeste'),
    ('naranja'),
    ('cafe'),
    ('gris'),
    ('negro'),
    ('blanco');

INSERT INTO public."tbmarca"("nombre_marca")
    VALUES 
    ('Lofi Girl'),
    ('Ariete'),
    ('Laura Ashley'),
    ('TENDANCE'),
    ('Maine Furniture Co.'),
    ('Rebecca Mobili'),
    ('Living Nostalgia'),
    ('WedgWood'),
    ('Missoni Home'),
    ('MisterWils'),
    ('Ruzafa Vintage'),
    ('Francisco Segarra'),
    ('Trouve'),
    ('Hilda Herrera'),
    ('Nahanche'),
    ('Coco Canela');

INSERT INTO public."tbestado_producto"("estado_producto")
    VALUES ('En venta'),
    ('Agotado');

INSERT INTO public."tbproducto"("nombre_producto", "descripcion", "material", "tamaño", "existencias", "porcentaje_descuento", "precio_producto", "idcolor", "id_marca", "iddistribuidor", "idestado_producto", "idsubcategoria_producto")
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

INSERT INTO public."tbusuario_cliente"("usuario_c", "contrasena_c", "nombre_cliente", "apellido_cliente", "correo_cliente", "telefono_cliente", "direccion_cliente", "idestado_usuario_c")
	VALUES ('lenny', '1234', 'Lenny Adrián', 'Elías Sánchez', 'lennyx004@gmail.com', '7852-5487', 'Mejicanos, San Salvador', 1),
('aimee', '1234', 'Aimee Vanessa', 'Osorio Canales', 'aimee08@gmail.com', '1258-9467' ,'Mejicanos, San Salvador', 1),
('nelson', '1234', 'Nelson Daniel', 'Peña Pineda', 'nelson@gmail.com', '7895-9467' ,'Mejicanos, San Salvador', 1),
('mangandi', '1234', 'Rodrigo Gabriel', 'Mangandi Cardona', 'mangandi@gmail.com', '6952-5487', 'Mejicanos, San Salvador', 1),
('fatima', '123', 'Fatima Rocio', 'Lopez Franco', 'fatima08@gmail.com', '8795-4587' ,'San Martin, San Salvador', 1),
('sofia', '123', 'Sofia Bautista', 'Hernandez Martinez', 'sofi45@gmail.com', '8145-4587' ,'Colonia Escalon, San Salvador', 1),
('gabriela', '123', 'Gabriela Susana', 'Mendez Barrera', 'Gaby7u7@gmail.com', '1459-8956' ,'Mejicanos, San Salvador', 1),
('oliver', '123', 'Oliver Alejandro', 'Erazo Reyes', 'oliver01@gmail.com', '7789-8454' ,'Olocuilta, La Paz', 1),
('erick', '123', 'Erick Salvador', 'Chinchilla Chiquillo', 'ericku@gmail.com', '7741-5748' ,'San Salvador, San Salvador', 1),
('jesus', '123', 'Jesus Gerardo', 'Esquivel Ramirez', 'jesusDK@gmail.com', '8894-5545' ,'Mejicanos, San Salvador', 1),
('sey', '123', 'Sey Guadalupe', 'Alvarado Najarro', 'xmxxs@gmail.com', '8898-4243' ,'Mejicanos, San Salvador', 1),
('geissel', '123', 'Geissel Mireya', 'Hernandez Ramos', 'geissel24@gmail.com', '6588-4984' ,'San Martin, San Salvador', 1);

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

INSERT INTO public."tbdetalle_factura"("total_producto", "precio_actual", "cantidad_descuento", "cantidad_producto", "idfactura", "idproducto")
	VALUES 
    (19.20, 9.60, 0, 2, 1, 1),
    (17.20, 8.60, 0, 2, 1, 2),
    (29.99, 29.99, 0, 1, 1, 3),
	(8.60, 8.60, 0, 1, 2, 2),
	(44.90, 44.90, 0, 1, 3, 4),
	(54.90, 54.90, 0, 1, 4, 5),

	(8.60, 8.60, 0, 1, 4, 2),
	(8.60, 8.60, 0, 1, 5, 2),
    (54.90, 54.90, 0, 1, 6, 5),
    (250.00, 250.00, 0, 1, 7, 7),
    (5.00, 5.00, 0, 4, 8, 9),
    (44.90, 44.90, 0, 1, 9, 4),
    (11.00, 44.90, 0, 1, 10, 10),
    (250.00, 44.90, 0, 1, 11, 6),
    (250.00, 44.90, 0, 1, 11, 7),
    (8.60, 8.60, 0, 1, 12, 2),
    (29.99, 29.99, 0, 1, 12, 3),
    (44.90, 44.90, 0, 1, 12, 4),
    (10.00, 10.00, 0, 1, 12, 8),
    (5.00, 5.00, 0, 1, 12, 9),
    (10.00, 10.00, 0, 1, 13, 8),
    (5.00, 5.00, 0, 2, 13, 9),
    (8.60, 8.60, 0, 1, 14, 2),
    (54.90, 54.90, 0, 1, 14, 5),
    (5.00, 5.00, 0, 1, 14, 9),
    (11.00, 11.00, 0, 1, 15, 10),
    (250.00, 250.00, 0, 1, 15, 7),
    (29.99, 29.99, 0, 1, 15, 3),
    (9.60, 9.60, 0, 1, 16, 1),
    (8.60, 8.60, 0, 1, 16, 2),
    (29.99, 29.99, 0, 1, 16, 3),
    (44.90, 44.90, 0, 1, 17, 4),
    (5.00, 5.00, 0, 1, 17, 9);

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