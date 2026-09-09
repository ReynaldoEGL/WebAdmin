# WebAdmin - API REST

## Descripción

WebAdmin es un proyecto de API REST desarrollado en PHP,
Apache y MySQL/MariaDB.

El proyecto contiene una versión V1 sin autenticación,
una versión V2 protegida mediante Bearer Token y una API
de gestión de tareas desarrollada siguiendo el enfoque
API-First.

---

# API-First

La API de tareas se diseñó mediante un contrato OpenAPI
antes de realizar su implementación.

El contrato se encuentra en:

server/api/api/public/openapi.yaml

---

# Endpoints de la API-First

## Listar tareas

GET /tareas

## Obtener una tarea

GET /tareas/{id}

## Crear una tarea

POST /tareas

Body:

{
    "titulo": "Estudiar API-First",
    "completada": false
}

## Actualizar una tarea

PUT /tareas/{id}

Body:

{
    "titulo": "Estudiar API-First",
    "completada": true
}

## Eliminar una tarea

DELETE /tareas/{id}

Esta operación constituye una extensión del
requisito mínimo de la práctica.

---

# Modelo de datos

{
    "id": 1,
    "titulo": "Estudiar API-First",
    "completada": false,
    "fecha_creacion": "2026-09-07T10:00:00Z"
}

---

# Swagger UI

La documentación interactiva está disponible en:

http://localhost:8080/api-docs/

Swagger UI carga el contrato:

openapi.yaml

---

# Ejecución local con Docker

## Construir la imagen

docker compose -f compose.api-local.yml build

## Iniciar los servicios

docker compose -f compose.api-local.yml up -d

## Verificar los servicios

docker compose -f compose.api-local.yml ps

## Detener los servicios

docker compose -f compose.api-local.yml down

---

# Servicios Docker

## API

Apache + PHP 8.4

Puerto:

8080

## Base de datos

MariaDB

Puerto local:

3307

Puerto interno:

3306

---

# Pruebas

Las pruebas se realizaron mediante:

- Postman
- Swagger UI

Se verificaron las operaciones:

GET /tareas
GET /tareas/{id}
POST /tareas
PUT /tareas/{id}

También se implementó DELETE /tareas/{id}
como extensión.

---

# Versiones existentes

## V1

/api/v1/

No requiere autenticación.

## V2

/api/v2/

Utiliza autenticación mediante Bearer Token.

---

# Servidor de clase

La API debe desplegarse posteriormente en el servidor
de clase utilizando la configuración de base de datos
correspondiente al entorno del servidor.

La configuración local de Docker se mantiene separada
de la configuración del servidor.